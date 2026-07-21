<?php

declare(strict_types=1);

namespace App\Observers;

use App\Console\Commands\ResourceConflictDetector;
use App\Jobs\ResourceConflictDetectorJob;
use App\Models\Activity;
use Illuminate\Support\Facades\Artisan;

final class ActivityObserver
{
    /**
     * Handle the Activity "created" event.
     */
    public function created(Activity $activity): void
    {
        // defer(
        //     fn() => Artisan::call(
        //         ResourceConflictDetector::class,
        //         [
        //             'resource' => 'activity',
        //             'resource_id' => $activity->id,
        //         ]
        //     )
        // );

        ResourceConflictDetectorJob::dispatch(
            'activity',
            $activity->id
        );
    }

    /**
     * Handle the Activity "updated" event.
     */
    public function updated(Activity $activity): void
    {
        ResourceConflictDetectorJob::dispatch(
            'activity',
            $activity->id
        );
    }

    /**
     * Handle the Activity "deleted" event.
     */
    public function deleted(Activity $activity): void
    {
        $activity->days()->delete();
        $activity->location()->delete();
        $activity->appointments()->delete();
        $activity->tags()->detach();
    }

    /**
     * Handle the Activity "restored" event.
     */
    public function restored(Activity $activity): void
    {
        //
    }

    /**
     * Handle the Activity "force deleted" event.
     */
    public function forceDeleted(Activity $activity): void
    {
        //
    }
}
