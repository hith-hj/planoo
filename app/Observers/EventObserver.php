<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\NotificationTypes;
use App\Models\Event;

final class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        // $event->days()->delete();
        // $event->location()->delete();
        // $event->appointments()->delete();
        // $event->tags()->detach();
        // foreach ($event->customers as $customer) {
        //     $customer->notify(
        //         'Event removal',
        //         "this event {$event->name} is removed.",
        //         ['type' => NotificationTypes::event->value, 'event' => $event->id],
        //     );
        // }
        // $event->customers()->detach();
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
