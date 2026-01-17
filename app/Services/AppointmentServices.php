<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\CodesTypes;
use App\Enums\SectionsTypes;
use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Event;
use App\Models\User;
use App\Traits\Filters;
use Carbon\Carbon;
use Exception;

final class AppointmentServices
{
    use Filters;

    public function allByQuery(
        $query,
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = []
    ) {
        Required($query, 'query');
        $query->with($this->ToBeLoaded());

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

        return $appointment->load($this->ToBeLoaded());
    }

    public function checkAvailableSlots(object $owner, array $data): array
    {
        $data = checkAndCastData($data, [
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
        $code = app(CodeServices::class)->createCode(
            type: CodesTypes::appointment->name,
            timeToExpire: '2:m'
        );

        return ['code' => $code->id, 'day' => $day['day'], 'date' => $data['date'], 'slots' => $slots];
    }

    public function checkAppointmentExists(array $data): bool
    {
        $data = checkAndCastData($data, [
            'date' => 'string',
            'session_duration' => 'int',
            'time' => 'string',
        ]);
        $start_time = Carbon::parse($data['time']);
        $end_time = (clone $start_time)->addMinutes($data['session_duration']);

        return Appointment::where([
            ['status', AppointmentStatus::accepted->value],
            ['date', $data['date']],
            ['time', '<', $end_time->toTimeString('minute')],
            ['end_at', '>', $start_time->toTimeString('minute')],
        ])->exists();
    }

    public function getAppointmentIfExists(array $data): ?Appointment
    {
        $data = checkAndCastData($data, [
            'date' => 'string',
            'session_duration' => 'int',
            'time' => 'string',
        ]);
        $start_time = Carbon::parse($data['time']);
        $end_time = (clone $start_time)->addMinutes($data['session_duration']);

        return Appointment::where([
            ['status', AppointmentStatus::accepted->value],
            ['date', $data['date']],
            ['time', '<', $end_time->toTimeString('minute')],
            ['end_at', '>', $start_time->toTimeString('minute')],
        ])->first();
    }

    public function create(object $owner, array $data, ?Customer $customer = null): Appointment
    {
        Truthy(! method_exists($owner, 'appointments'), 'missing appointments() method');
        Truthy(! $owner->is_active, 'Inactive owner');

        $date = Carbon::parse($data['date']);
        $start_time = Carbon::createFromTimeString($data['time']);
        $end_at = (clone $start_time)->addMinutes($data['session_duration']);
        $appointment = $owner->appointments()->create([
            'customer_id' => $customer->id ?? null,
            'date' => $date->toDateString(),
            'time' => $start_time->toTimeString('minute'),
            'end_at' => $end_at->toTimeString('minute'),
            'session_duration' => $data['session_duration'],
            'price' => $data['price'] ?? $this->caculatePrice($data['session_duration'], $owner),
            'status' => AppointmentStatus::accepted->value,
            'notes' => $data['notes'] ?? null,
        ]);

        return $appointment->load($this->ToBeLoaded());
    }

    public function cancel(object $user, Appointment $appointment): bool
    {
        Truthy($appointment->status !== AppointmentStatus::accepted->value, 'invalid appointment status');
        Truthy(! $this->canCancel($appointment), 'You can\'t cancel now');

        return $appointment->update([
            'status' => AppointmentStatus::canceled->value,
            'canceled_by' => class_basename($user::class),
        ]);
    }

    public function getUserQuery(User $user, string $ownerType = 'activity')
    {
        try {
            $owner = getModel();
            Truthy(! method_exists($owner, 'appointments'), 'missing appointments() method');

            return $owner->appointments();
        } catch (Exception) {
            return match ($ownerType) {
                SectionsTypes::activity->name => Appointment::where('appointable_type', Activity::class)
                    ->whereIn('appointable_id', $user->activities()->pluck('id')),
                SectionsTypes::course->name => Appointment::where('appointable_type', Course::class)
                    ->whereIn('appointable_id', $user->courses()->pluck('courses.id')),
                SectionsTypes::event->name => Appointment::where('appointable_type', Event::class)
                    ->whereIn('appointable_id', $user->events()->pluck('events.id')),
                default => throw new Exception('ownerType is required'),
            };
        }
    }

    public function getCustomerQuery(Customer $customer, string $ownerType = 'activity')
    {
        return match ($ownerType) {
            SectionsTypes::activity->name => $customer->appointments(),
            SectionsTypes::course->name => Appointment::where('appointable_type', Course::class)
                ->whereIn('appointable_id', $customer->courses()->pluck('courses.id')),
            SectionsTypes::event->name => Appointment::where('appointable_type', Event::class)
                ->whereIn('appointable_id', $customer->events()->pluck('events.id')),
            default => throw new Exception('ownerType is required'),
        };
    }

    private function checkAllowedDurations($gapStart, $gapEnd, $duration): array
    {
        $slots = [];
        $break = (int) config('app.settings.break_in_minute', 0);
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

    private function ToBeLoaded()
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

    private function canCancel(Appointment $appointment): bool
    {
        $diff = now()
            ->diffInSeconds($appointment->created_at) / 3600;
        if (abs($diff) > config('app.settings.appointment_cancelation_period', 1)) {
            return false;
        }

        return true;
    }
}
