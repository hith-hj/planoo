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
        array $orderBy = []
    ) {
        $query = Event::query();
        $query->with($this->toBeLoaded());

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
        Truthy($this->isAttending($customer, $event), 'Already attending this event.');
        Truthy($event->is_full, 'Event is full');
        Truthy($event->status !== EventStatus::pending->value, 'Event is canceled');
        Truthy(! $event->is_active, 'Event is inactive');

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
        Truthy(! $this->isAttending($customer, $event), 'Not attending this event.');
        Truthy(! $this->canCancel($customer, $event), 'Can\'t cancel this event.');
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

    private function toBeLoaded()
    {
        return ['days', 'location', 'tags', 'medias', 'category', 'reviews', 'customers'];
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
}
