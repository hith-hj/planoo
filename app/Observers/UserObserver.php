<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;

final class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void {}

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void {}

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        foreach ($user->activities as $activity) {
            $activity->delete();
        }
        foreach ($user->courses as $course) {
            $course->delete();
        }
        foreach ($user->events as $event) {
            $event->delete();
        }
        foreach ($user->courts as $court) {
            $court->delete();
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
