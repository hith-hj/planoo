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

        return $appointment->load($this->ToBeLoaded());
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
                    ->whereIn('appointable_id', $user->courses()->pluck('id')),
                SectionsTypes::event->name => Appointment::where('appointable_type', Event::class)
                    ->whereIn('appointable_id', $user->events()->pluck('id')),
                default => throw new Exception('ownerType is required'),
            };
        }
    }

    public function getCustomerQuery(Customer $customer, string $ownerType = 'activity')
    {
        return match ($ownerType) {
            SectionsTypes::activity->name => $customer->appointments(),
            SectionsTypes::course->name => Appointment::where('appointable_type', Course::class)
                ->whereIn('appointable_id', $customer->courses()->pluck('id')),
            SectionsTypes::event->name => Appointment::where('appointable_type', Event::class)
                ->whereIn('appointable_id', $customer->events()->pluck('id')),
            default => throw new Exception('ownerType is required'),
        };
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

    private function canCancelAppointment(Appointment $appointment, int $buffer = 60)
    {
        $now = now();
        $bookingTime = $appointment->created_at;
        $diffInMinutes = $now->diffInMinutes($bookingTime, true);
        Truthy($diffInMinutes > $buffer, 'appointment cannot be canceled after one hour');
    }
}
