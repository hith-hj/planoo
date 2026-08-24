<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\NotificationTypes;
use App\Jobs\ResourceConflictDetectorJob;
use App\Models\Event;

final class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        if (config('services.conflicts_detection', false) === true) {
            ResourceConflictDetectorJob::dispatch(
                'event',
                $event->id
            );
        }
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        if (config('services.conflicts_detection', false) === true) {
            ResourceConflictDetectorJob::dispatch(
                'event',
                $event->id
            );
        }
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        if (app()->environment('testing')) {
            foreach ($event->customers as $customer) {
                $customer->notify(
                    'Event removal',
                    "this event {$event->name} is removed.",
                    ['type' => NotificationTypes::event->value, 'event' => $event->id],
                );
            }

            $event->days()->delete();
            $event->location()->delete();
            $event->appointments()->delete();
            $event->tags()->detach();
            $event->customers()->detach();
        }
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
