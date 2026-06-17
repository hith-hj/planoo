<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventStatus;
use App\Enums\NotificationTypes;
use App\Models\Customer;
use App\Models\Event;
use App\Models\User;
use App\Traits\Filters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class EventServices
{
    use Filters;

    public function allByFilter(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = [],
        mixed $query = null
    ) {
        if ($query === null) {
            $query = Event::query();
            $query->with($this->toBeLoaded());
        } else {
            $query->with([...$this->toBeLoaded(), 'isFavorite', 'isAttending']);
        }

        $this->applyFilters($query, $filters, [
            'is_active' => [true, false],
            'is_full' => [true, false],
            'start_date' => [],
            'category_id' => [],
        ]);

        $this->applyOrderBy($query, $orderBy, ['rate', 'admission_fee', 'event_duration']);

        $events = $query->paginate($perPage, ['*'], 'page', $page);

        NotFound($events->items(), 'events');

        return $events;
    }

    public function all(): Collection
    {
        $events = Event::all();
        NotFound($events, 'events');

        return $events->load($this->toBeLoaded());
    }

    public function allByUser(User $user): Collection|Model
    {
        Required($user, 'user');
        $events = $user->events;
        NotFound($events, 'events');

        return $events->load($this->toBeLoaded());
    }

    public function findByUser(User $user, int $id): Event
    {
        Required($user, 'user');
        $event = $user->events()->whereId($id)->first();
        NotFound($event, 'event');

        return $event->load($this->toBeLoaded());
    }

    public function find(int $id): Event
    {
        Required($id, 'id');
        $event = Event::whereId($id)->first();
        NotFound($event, 'event');

        return $event->load([...$this->toBeLoaded(), 'isFavorite', 'isAttending']);
    }

    public function create(User $user, array $data): Event
    {
        Required($user, 'user');
        Required($data, 'event data');
        $event = $user->events()->create($this->prepEventData($data));

        return $event->fresh()->load($this->toBeLoaded());
    }

    public function update(User $user, Event $event, array $data): Event
    {
        Required($user, 'user');
        Required($data, 'event data');
        $event->update($this->prepEventData($data));

        return $event->load($this->toBeLoaded());
    }

    public function delete(Event $event): bool
    {
        Required($event, 'event');

        return $event->delete();
    }

    public function toggleActivation(Event $event): bool
    {
        Required($event, 'event');

        return $event->update(['is_active' => ! $event->is_active]);
    }

    public function attend(Customer $customer, Event $event)
    {
        Required($customer, 'customer');
        Required($event, 'event');
        Truthy($this->isAttending($customer, $event), 'already attending this event');
        Truthy($event->is_full, 'Event is full');
        Truthy($event->status !== EventStatus::pending->value, 'event is canceled');
        Truthy(! $event->is_active, 'event is inactive');

        $event->customers()->attach($customer->id);
        if ($event->customers()->count() === $event->capacity) {
            $event->update(['is_full' => true]);
        }
        $event->user->notify(
            'New Customer',
            'You have a new customer',
            ['type' => NotificationTypes::event->value, 'event' => $event->id]
        );

        return $event;
    }

    public function cancel(Customer $customer, Event $event)
    {
        Required($customer, 'customer');
        Required($event, 'event');
        Truthy(! $this->isAttending($customer, $event), 'not attending this event');
        Truthy(! $this->canCancel($customer, $event), 'can\'t cancel this event');
        $event->customers()->detach($customer->id);
        if ($event->customers()->count() < $event->capacity) {
            $event->update(['is_full' => false]);
        }
        $event->user->notify(
            'Customer Left',
            'Customer left corse',
            ['type' => NotificationTypes::event->value, 'event' => $event->id]
        );

        return $event;
    }

    public function calculateEventEndDate(Event $event): array|bool
    {
        $event = $event->load('days');
        $totalSessionsRequired = (int) $event->event_duration;
        $weeklySchedule = $event->days;
        $currentDate = Carbon::parse($event->start_date);
        $dayMapping =
            [
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6,
                'sunday' => 7,
            ];
        $processedSchedule = [];
        foreach ($weeklySchedule as $session) {
            $dayNum = $dayMapping[mb_strtolower($session['day'])];
            $processedSchedule[$dayNum] = [
                'day' => $session['day'],
                'start' => $session['start'],
                'end' => $session['end'],
                'is_overnight' => strcmp($session['end'], $session['start']) < 0,
            ];
        }
        ksort($processedSchedule);

        $generatedSessions = [];
        $maxIterations = $totalSessionsRequired * 7;
        $iterations = 0;

        while (count($generatedSessions) < $totalSessionsRequired && $iterations < $maxIterations) {
            $iterations++;
            $currentDayOfWeek = $currentDate->dayOfWeekIso;

            // Check if today matches a day in our schedule
            if (isset($processedSchedule[$currentDayOfWeek])) {
                $session = $processedSchedule[$currentDayOfWeek];
                $dateStr = $currentDate->toDateString();

                // Handle midnight rollover
                $endDateStr = $dateStr;
                if ($session['is_overnight']) {
                    $endDateStr = $currentDate->copy()->addDay()->toDateString();
                }

                $generatedSessions[] = [
                    'session_number' => count($generatedSessions) + 1,
                    'day_of_week' => $session['day'],
                    'start_date' => $dateStr,
                    'start_time' => $session['start'].':00',
                    'end_date' => $endDateStr,
                    'end_time' => $session['end'].':00',
                ];
            }

            // increment daays
            $currentDate->addDay();
        }

        $data['processed_sessions'] = $generatedSessions;
        $data['start_date'] = $generatedSessions[0]['start_date'] ?? null;
        $data['end_date'] = end($generatedSessions)['end_date'] ?? null;
        $data['event_duration'] = count($generatedSessions);

        return $event->update(['end_date' => $data['end_date']]);
    }

    public function getCustomerQuery(Customer $customer)
    {
        return $customer->events();
    }

    private function toBeLoaded()
    {
        return ['court', 'days', 'location', 'tags', 'medias', 'category', 'reviews', 'customers'];
    }

    private function isAttending(Customer $customer, Event $event): bool
    {
        return $event->customers()->where('customer_id', $customer->id)->exists();
    }

    private function canCancel(Customer $customer, Event $event): bool
    {
        $eventCustomer = $event
            ->customers()
            ->where('customer_id', $customer->id)
            ->first();
        $diff = now()
            ->diffInSeconds($eventCustomer->pivot->created_at) / 3600;
        if (abs($diff) > config('app.settings.event_cancelation_period', 24)) {
            return false;
        }

        return true;
    }

    private function prepEventData(array $data): array
    {
        $data = checkAndCastData($data, ['start_date' => 'string', 'event_duration' => 'int']);
        $end_date = $data['start_date'];
        if (($duration = $data['event_duration']) > 1) {
            $start_date = Carbon::createFromDate($data['start_date']);
            $end_date = $start_date->copy()->addDays($duration)->toDateString();
        }
        $data['end_date'] = $end_date;

        return $data;
    }

    private function getDaysOfWeek(Collection $eventDays): array
    {
        $days = [];
        foreach ($eventDays as $day) {
            $days[] = $day->day;
        }

        return $days;
    }
}
