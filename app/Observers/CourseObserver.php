<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\NotificationTypes;
use App\Models\Course;

final class CourseObserver
{
    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {
        //
    }

    /**
     * Handle the Course "updated" event.
     */
    public function updated(Course $course): void
    {
        //
    }

    /**
     * Handle the Course "deleted" event.
     */
    public function deleted(Course $course): void
    {
        // $course->days()->delete();
        // $course->location()->delete();
        // $course->appointments()->delete();
        // $course->tags()->detach();
        // foreach ($course->customers as $customer) {
        //     $customer->notify(
        //         'Course removal',
        //         "this course {$course->name} is removed.",
        //         ['type' => NotificationTypes::course->value, 'course' => $course->id],
        //     );
        // }
        // $course->customers()->detach();
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
