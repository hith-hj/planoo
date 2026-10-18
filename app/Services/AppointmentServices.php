<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Enums\CodesTypes;
use App\Enums\SectionsTypes;
use App\Enums\SessionDuration;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Code;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Event;
use App\Models\User;
use App\Support\Pagination;
use App\Traits\Filters;
use Carbon\Carbon;
use DomainException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class AppointmentServices
{
    use Filters;

    public function allByQuery(
        $query,
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = [],
        bool $paginate = true
    ) {
        Pagination::validate($page, $perPage);
        Required($query, 'query');
        Truthy($paginate === false && count($filters) === 0, 'invalid operation');
        $query->with($this->toBeLoaded());
        $this->applyFilters($query, $filters, [
            'status' => AppointmentStatus::values(),
            'session_duration' => SessionDuration::values(),
            'date' => [],
        ]);

        $this->applyOrderBy($query, $orderBy, ['date', 'time']);

        $appointments = $paginate ?
            $query->paginate($perPage, ['*'], 'page', $page) :
            Pagination::bounded($query);

        NotFound($paginate ? $appointments->items() : $appointments, 'appointments');

        return $appointments;
    }

    public function acceptedByQuery($query, array $orderBy = [])
    {
        Required($query, 'query');
        $query->where('status', AppointmentStatus::accepted->value);
        $query->with($this->toBeLoaded());
        $this->applyOrderBy($query, $orderBy, ['date', 'time']);

        $appointments = Pagination::bounded($query);

        NotFound($appointments, 'appointments');

        return $appointments;
    }

    public function find(mixed $id)
    {
        Required($id, 'appointment id');
        $appointment = Appointment::find($id);
        NotFound($appointment, 'appointment');

        return $appointment->load($this->toBeLoaded());
    }

    public function findForActor(Customer|User $actor, mixed $id): Appointment
    {
        $query = Appointment::query();
        if ($actor instanceof Customer) {
            $query->where(function ($query) use ($actor): void {
                $query->where('customer_id', $actor->id)
                    ->orWhereHasMorph('holder', [Course::class, Event::class], function ($holder) use ($actor): void {
                        $holder->whereHas('customers', fn ($customers) => $customers->whereKey($actor->id));
                    });
            });
        } else {
            $query->whereHasMorph('holder', [Activity::class, Course::class, Event::class],
                fn ($holder) => $holder->where('user_id', $actor->id));
        }

        return $query->with($this->toBeLoaded())->findOrFail($id);
    }

    public function checkAvailableSlots(object $owner, array $data): array
    {
        $data = checkAndCastData($data, [
            'day_id' => 'int',
            'session_duration' => 'int',
            'date' => 'string',
        ]);

        $duration = SessionDuration::from($data['session_duration'])->value;
        $data['date'] = Carbon::parse($data['date'])->toDateString();
        Truthy(! $owner->is_active, 'inactive owner');
        $selectedDay = app(DayServices::class)->findByObject($owner, $data['day_id']);
        Truthy(! $selectedDay->is_active, 'inactive day');
        $day = $selectedDay->only(['day', 'start', 'end']);
        $dayOfSelectedDate = Carbon::parse($data['date'])->format('l');
        Truthy($day['day'] !== mb_strtolower($dayOfSelectedDate), "selected date don't match selected day");
        $appointments = Appointment::owner($owner::class, $owner->id)
            ->where('date', $data['date'])
            ->where('status', AppointmentStatus::accepted->value)
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
            $gapEnd = $session['start']->lt($end) ? $session['start']->copy() : $end->copy();
            $gapMinutes = $gapStart->diffInMinutes($gapEnd);

            if ($gapMinutes >= $duration) {
                $slots = array_merge($slots, $this->checkAllowedDurations($gapStart, $gapEnd, $duration));
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
                $slots = array_merge($slots, $this->checkAllowedDurations($gapStart, $gapEnd, $duration));
            }
        }
        $code = app(CodeServices::class)->createCode(
            type: CodesTypes::appointment->name,
            timeToExpire: '2:m'
        );
        $actor = Auth::user();
        $code->update(['booking_context' => [
            'actor_type' => $actor ? $actor::class : null,
            'actor_id' => $actor?->getKey(),
            'activity_id' => $owner->getKey(),
            'day_id' => $data['day_id'],
            'date' => Carbon::parse($data['date'])->toDateString(),
            'session_duration' => $duration,
            'slots' => array_column($slots, 'start'),
        ]]);

        return ['code' => $code->id, 'day' => $day['day'], 'date' => $data['date'], 'slots' => $slots];
    }

    public function checkAppointmentExists(object $owner, array $data): bool
    {
        $data = checkAndCastData($data, [
            'date' => 'string',
            'session_duration' => 'int',
            'time' => 'string',
        ]);
        $start = Carbon::parse($data['time']);
        $end = $start->copy()->addMinutes($data['session_duration']);

        return Appointment::owner($owner::class, $owner->id)
            ->conflict($data['date'], $start->toTimeString('minute'), $end->toTimeString('minute'))
            ->exists();
    }

    public function book(Activity $activity, Customer|User $actor, Customer $customer, array $data): Appointment
    {
        $data['date'] = Carbon::parse($data['date'])->toDateString();

        return DB::transaction(function () use ($activity, $actor, $customer, $data): Appointment {
            $activity = Activity::query()->lockForUpdate()->findOrFail($activity->id);
            $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            $code = Code::query()->lockForUpdate()->find($data['code']);
            Truthy(! $code || ! $code->isValid() || $code->type !== CodesTypes::appointment->name, 'invalid code');
            $context = $code->booking_context ?? [];
            foreach ([
                'actor_type' => $actor::class,
                'actor_id' => $actor->getKey(),
                'activity_id' => $activity->id,
                'day_id' => (int) $data['day_id'],
                'date' => Carbon::parse($data['date'])->toDateString(),
                'session_duration' => (int) $data['session_duration'],
            ] as $key => $value) {
                Truthy(($context[$key] ?? null) !== $value, 'invalid booking context');
            }
            Truthy(! in_array($data['time'], $context['slots'] ?? [], true), 'invalid booking slot');
            $day = $activity->days()->lockForUpdate()->findOrFail($data['day_id']);
            $start = Carbon::parse($data['date'].' '.$data['time']);
            $end = $start->copy()->addMinutes((int) $data['session_duration']);
            Truthy(! $day->is_active || mb_strtolower($start->format('l')) !== $day->day, 'invalid booking day');
            Truthy($start->isPast() || $start->toDateString() !== $end->toDateString()
                || $start->format('H:i:s') < Carbon::parse($day->start)->format('H:i:s')
                || $end->format('H:i:s') > Carbon::parse($day->end)->format('H:i:s'), 'outside booking hours');
            Truthy($this->checkAppointmentExists($activity, $data), 'appointment just got booked');
            Truthy(! $this->canCreateAppointment($customer, $data), 'You have Appointment at this date');
            $appointment = $this->create($activity, $data, $customer);
            $code->delete();

            return $appointment;
        }, 3);
    }

    public function canCreateAppointment(Customer $customer, array $data): bool
    {
        $data = checkAndCastData($data, [
            'date' => 'string',
            'time' => 'string',
            'session_duration' => 'int',
        ]);

        $start = Carbon::parse($data['time']);
        $end = $start->copy()->addMinutes($data['session_duration']);

        $date = $data['date'];
        $startTime = $start->toTimeString('minute');
        $endTime = $end->toTimeString('minute');

        if ($customer->appointments()->conflict($date, $startTime, $endTime)->exists()) {
            return false;
        }

        return ! Appointment::query()
            ->whereHasMorph('holder', [Course::class, Event::class], function ($holder) use ($customer): void {
                $holder->whereHas('customers', fn ($customers) => $customers->whereKey($customer->id));
            })
            ->conflict($date, $startTime, $endTime)
            ->exists();
    }

    public function getAppointmentIfExists(object $owner, array $data): ?Appointment
    {
        $data = checkAndCastData($data, [
            'date' => 'string',
            'session_duration' => 'int',
            'time' => 'string',
        ]);
        $start_time = Carbon::parse($data['time']);
        $end_time = (clone $start_time)->addMinutes($data['session_duration']);

        return Appointment::owner($owner::class, $owner->id)
            ->where([
                ['status', AppointmentStatus::accepted->value],
                ['date', $data['date']],
                ['time', '<', $end_time->toTimeString('minute')],
                ['end_at', '>', $start_time->toTimeString('minute')],
            ])->first();
    }

    public function create(object $owner, array $data, ?Customer $customer = null): Appointment
    {
        Truthy(! method_exists($owner, 'appointments'), 'missing appointments() method');
        Truthy(! $owner->is_active, 'inactive owner');

        $date = Carbon::parse($data['date']);
        $start_time = Carbon::createFromTimeString($data['time']);
        $end_at = (clone $start_time)->addMinutes($data['session_duration']);
        $appointment = $owner->appointments()->create([
            'customer_id' => $customer->id ?? null,
            'date' => $date->toDateString(),
            'time' => $start_time->toTimeString('minute'),
            'end_at' => $end_at->toTimeString('minute'),
            'session_duration' => $data['session_duration'],
            'price' => $data['price'] ?? $this->calculatePrice($data['session_duration'], $owner),
            'status' => AppointmentStatus::accepted->value,
            'notes' => $data['notes'] ?? null,
        ]);

        return $appointment->load($this->toBeLoaded());
    }

    public function cancel(object $user, Appointment $appointment): bool
    {
        if ($user instanceof Customer) {
            abort_unless($appointment->customer_id === $user->id && $appointment->appointable_type === Activity::class, 403);
        } elseif ($user instanceof User) {
            abort_unless($appointment->holder?->user_id === $user->id, 403);
        } else {
            abort(403);
        }
        Truthy($appointment->status !== AppointmentStatus::accepted->value, 'invalid appointment status');
        Truthy(! $this->canCancel($appointment), 'you can\'t cancel now');

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
                default => throw new DomainException('ownerType is required'),
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
            default => throw new DomainException('ownerType is required'),
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

    private function toBeLoaded()
    {
        return ['customer.medias', 'holder.category', 'holder.medias'];
    }

    private function calculatePrice($session_duration, $owner): int
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
        if (abs($diff) > app_setting('appointment_cancelation_period', 1)) {
            return false;
        }

        return true;
    }
}
