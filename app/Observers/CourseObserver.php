<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\NotificationTypes;
use App\Jobs\ResourceConflictDetectorJob;
use App\Models\Course;

final class CourseObserver
{
    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {

        if (config('services.conflicts_detection', false) === true) {
            ResourceConflictDetectorJob::dispatch(
                'course',
                $course->id
            );
        }
    }

    /**
     * Handle the Course "updated" event.
     */
    public function updated(Course $course): void
    {

        if (config('services.conflicts_detection', false) === true) {
            ResourceConflictDetectorJob::dispatch(
                'course',
                $course->id
            );
        }
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        foreach ($course->customers as $customer) {
            $customer->notify(
                'Event removal',
                "this course {$course->name} is removed.",
                ['type' => NotificationTypes::course->value],
            );
        }
        $course->customers()->detach();
        $course->tags()->detach();
        $course->days()->delete();
        $course->location()->delete();
        $course->appointments()->delete();
    }

    /**
     * Handle the Course "restored" event.
     */
    public function restored(Course $course): void
    {
        //
    }

    /**
     * Handle the Course "force deleted" event.
     */
    public function forceDeleted(Course $course): void
    {
        //
    }
}
