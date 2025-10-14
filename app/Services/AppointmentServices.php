<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\Appointment;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

final class AppointmentServices
{
    public function allByQuery(
        $query,
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ) {
        Required($query, 'query');
        $query->with($this->relationToLoad())
            ->where('status', AppointmentStatus::accepted->value);

        $this->applyFilters($query, $filters, [
            'status' => AppointmentStatus::values(),
            'session_duration' => SessionDuration::values(),
            'date' => [],
        ]);

        $this->applyOrderBy($query, $orderBy, ['date', 'time']);

        $appointments = $query->paginate($perPage, ['*'], 'page', $page);

        NotFound($appointments->items(), 'appointments');

        return $appointments;
    }

    public function find(int $id)
    {
        Required($id, 'Appointment id');
        $appointment = Appointment::find($id);
        NotFound($appointment, 'Appointment');

        return $appointment->load($this->relationToLoad());
    }

    public function checkAvailableSlots(object $owner, array $data): array
    {
        checkAndCastData($data, [
            'day_id' => 'int',
            'session_duration' => 'int',
            'date' => 'string',
        ]);
        $duration = SessionDuration::from($data['session_duration'])->value;
        $day = app(DayServices::class)
            ->findByObject($owner, $data['day_id'])
            ->only(['day', 'start', 'end']);
        $appointments = Appointment::owner($owner::class, $owner->id)
            ->where('date', $data['date'])
            ->get(['date', 'time', 'session_duration']);

        $start = Carbon::parse($day['start']);
        $end = Carbon::parse($day['end']);

        $busySessions = collect($appointments)->map(function ($appt) {
            $start = Carbon::parse($appt['time']);
            $end = $start->copy()->addMinutes($appt['session_duration']);

            return ['start' => $start, 'end' => $end];
        })->sortBy('start')->values();

        $slots = [];
        $previousEnd = $start;
        // add breack here $break = config('break_in_minute',10)
        foreach ($busySessions as $session) {
            $sessionStart = $previousEnd;
            $sessionEnd = $session['start'];
            $sessionMinutes = $sessionStart->diffInMinutes($sessionEnd); // + $break;
            $slots = [...$this->checkAllowedDurations($sessionStart, $sessionMinutes, $duration)];
            $previousEnd = max($previousEnd, $session['end']);
        }

        if ($previousEnd->lt($end)) {
            $sessionStart = $previousEnd;
            $sessionEnd = $end;
            $sessionMinutes = $sessionStart->diffInMinutes($sessionEnd);
            $slots = [...$this->checkAllowedDurations($sessionStart, $sessionMinutes, $duration)];
        }

        return ['day' => $day['day'], 'date' => $data['date'], 'slots' => $slots];
    }

    public function checkAppointmentExists(array $data): bool
    {
        $data = checkAndCastData($data, [
            'activity_id' => 'int',
            'date' => 'string',
            'session_duration' => 'int',
            'time' => 'string',
        ]);
        $activity = app(ActivityServices::class)->find($data['activity_id']);
        Truthy(! $activity || ! method_exists($activity, 'appointments'), 'invalid activity');

        return $activity->appointments()->where([
            ['date', $data['date']],
            ['time', $data['time']],
            ['session_duration', $data['session_duration']],
            ['status', '!=', AppointmentStatus::canceled->value],
        ])->exists();
    }

    public function create(object $owner, array $data): Appointment
    {
        Truthy(! method_exists($owner, 'appointments'), 'missing appointments() method');

        $appointment = $owner->appointments()->create([
            'date' => $data['date'],
            'time' => $data['time'],
            'session_duration' => $data['session_duration'],
            'status' => AppointmentStatus::accepted->value,
            'price' => $this->caculatePrice($data['session_duration'], $owner),
            'notes' => $data['notes'] ?? null,
        ]);

        $this->attachRelations($appointment, $data);

        return $appointment->load($this->relationToLoad());
    }

    public function cancel(object $user, Appointment $appointment): bool
    {
        Truthy($appointment->status !== AppointmentStatus::accepted->value, 'invalid appointment status');
        $this->canCancelAppointment($appointment);

        return $appointment->update([
            'status' => AppointmentStatus::canceled->value,
            'canceled_by' => class_basename($user::class),
        ]);
    }

    public function getQuery($user, string $type = 'activity')
    {
        $query = '';
        try {
            $owner = getModel();
            Truthy(! method_exists($owner, 'appointments'), 'missing appointments() method');
            $query = $owner->appointments();
        } catch (Exception $e) {
            $query = match ($type) {
                'activity' => function () use ($user) {
                    return Appointment::where('appointable_type', Activity::class)
                        ->whereIn('appointable_id', $user->activities()->pluck('id'));
                },
                default => throw new Exception('Type is required for query'),
            };
        } finally {
            return $query;
        }
    }

    private function relationToLoad()
    {
        return ['customer', 'holder'];
    }

    private function attachRelations(object $owner, array $data = []): Appointment
    {
        Required($owner, 'owner');
        if (isset($data['customer_id']) || isset($data['customer_phone'])) {
            if (isset($data['customer_id'])) {
                $customer = app(CustomerServices::class)->find((int) $data['customer_id']);
            }
            if (isset($data['customer_phone'])) {
                $customer = app(CustomerServices::class)->createIfNotExists([
                    'phone' => $data['customer_phone'],
                ]);
            }
            $owner->customer()->associate($customer)->save();
        }

        return $owner;
    }

    private function caculatePrice($session_duration, $owner): int
    {
        checkAndCastData($owner->toArray(), [
            'session_duration' => 'int',
            'price' => 'int',
        ]);
        $price = ($session_duration / $owner->session_duration) * $owner->price;

        return (int) ceil($price);
    }

    private function checkAllowedDurations($gapStart, $gapMinutes, $duration): array
    {
        $slots = [];
        foreach (SessionDuration::values() as $sd) {
            if ($sd === $duration && $gapMinutes >= $sd) {
                $slots[] = [
                    'start' => $gapStart->format('H:i'),
                    'end' => $gapStart->copy()->addMinutes($sd)->format('H:i'),
                    'session_duration' => $sd,
                ];
            }
        }

        return $slots;
    }

    private function canCancelAppointment(Appointment $appointment, int $buffer = 60)
    {
        $now = now();
        $bookingTime = $appointment->created_at;
        $diffInMinutes = $now->diffInMinutes($bookingTime, true);
        Truthy($diffInMinutes > $buffer, 'appointment cannot be canceled after one hour');
    }

    private function applyFilters(object $query, array $filters = [], array $allowedFilters = []): object
    {
        return $query->when(
            ! empty($filters),
            function (Builder $filter) use ($filters, $allowedFilters) {
                foreach ($filters as $key => $value) {
                    if (! in_array($key, array_keys($allowedFilters))) {
                        return;
                    }
                    if (is_numeric($value)) {
                        $value = (int) $value;
                    }
                    if (in_array($value, $allowedFilters[$key]) || empty($allowedFilters[$key])) {
                        $filter->where($key, $value);
                    }
                }
            }
        );
    }

    private function applyOrderBy(object $query, array $orderBy = [], array $allowedOrderBy = []): object
    {
        return $query->when(
            ! empty($orderBy) && count($orderBy) === 1,
            function (Builder $query) use ($orderBy, $allowedOrderBy) {
                $key = array_key_first($orderBy);
                $value = array_pop($orderBy);
                if (in_array($key, $allowedOrderBy) && in_array($value, ['asc', 'desc'])) {
                    $query->orderBy($key, $value);
                }
            }
        );
    }
}
