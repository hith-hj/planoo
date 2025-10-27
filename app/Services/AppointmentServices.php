<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\SectionsTypes;
use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Customer;
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
        $query->with($this->relationToLoad());

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
        $dayOfSelectedDate = Carbon::parse($data['date'])->format('l');
        Truthy($day['day'] !== mb_strtolower($dayOfSelectedDate), "Selected date don't match selected day");
        $appointments = Appointment::owner($owner::class, $owner->id)
            ->where('date', $data['date'])
            ->get(['date', 'time', 'session_duration']);

        $start = Carbon::parse($day['start']);
        $end = Carbon::parse($day['end']);

        $busySessions = collect($appointments)->map(function ($appt) {
            $s = Carbon::parse($appt['time']);
            $e = $s->copy()->addMinutes($appt['session_duration']);

            return ['start' => $s, 'end' => $e];
        })->sortBy('start')->values();

        $slots = [];
        $previousEnd = $start->copy();
        foreach ($busySessions as $session) {
            $gapStart = $previousEnd->copy();
            $gapEnd = $session['start']->copy();
            $gapMinutes = $gapStart->diffInMinutes($gapEnd);

            if ($gapMinutes >= $duration) {
                $slots = [...$this->checkAllowedDurations($gapStart, $gapEnd, $duration)];
            }

            if ($session['end']->greaterThan($previousEnd)) {
                $previousEnd = $session['end']->copy();
            }
        }

        if ($previousEnd->lt($end)) {
            $gapStart = $previousEnd->copy();
            $gapEnd = $end->copy();
            $gapMinutes = $gapStart->diffInMinutes($gapEnd);

            if ($gapMinutes >= $duration) {
                $slots = [...$this->checkAllowedDurations($gapStart, $gapEnd, $duration)];
            }
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

    public function create(object $owner, Customer $customer, array $data): Appointment
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

        $appointment->customer()->associate($customer)->save();

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

    public function getQuery($user, string $ownerType = 'activity')
    {
        $query = '';
        try {
            $owner = getModel();
            Truthy(! method_exists($owner, 'appointments'), 'missing appointments() method');
            $query = $owner->appointments();
        } catch (Exception) {
            $query = match ($ownerType) {
                SectionsTypes::activity->name => Appointment::where('appointable_type', Activity::class)
                    ->whereIn('appointable_id', $user->activities()->pluck('id')),
                SectionsTypes::course->name => Appointment::where('appointable_type', Course::class)
                    ->whereIn('appointable_id', $user->courses()->pluck('id')),
                default => throw new Exception('ownerType is required for query'),
            };
        } finally {
            return $query;
        }
    }

    public function getCustomer(array $data)
    {
        $customer = null;
        if (isset($data['customer_id']) || isset($data['customer_phone'])) {
            if (isset($data['customer_phone'])) {
                $customer = app(CustomerServices::class)->createIfNotExists([
                    'phone' => $data['customer_phone'],
                ]);
            }
            if (isset($data['customer_id'])) {
                $customer = app(CustomerServices::class)->find((int) $data['customer_id']);
            }

            return $customer;
        }
        NotFound($customer, 'customer');
    }

    private function checkAllowedDurations($gapStart, $gapEnd, $duration): array
    {
        $slots = [];
        $break = (int) config('break_in_minute', 0);
        $cursor = $gapStart->copy();
        while ($cursor->copy()->addMinutes($duration)->lessThanOrEqualTo($gapEnd)) {
            $slots[] = [
                'start' => $cursor->format('H:i'),
                'end' => $cursor->copy()->addMinutes($duration)->format('H:i'),
                'session_duration' => $duration,
            ];
            $cursor->addMinutes($duration + $break);
        }

        return $slots;
    }

    private function relationToLoad()
    {
        return ['customer', 'holder'];
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
