<?php

declare(strict_types=1);

use App\Jobs\ResourceConflictDetectorJob;
use App\Models\Activity;
use App\Models\Category;
use App\Models\Course;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

/**
 * Covers improvement report 5.5: ResourceConflictDetectorJob must only be
 * dispatched when conflicts detection is enabled in configuration.
 */
beforeEach(function () {
    Queue::fake();
    Category::factory()->count(3)->create();
    Tag::factory()->count(3)->create();
});

it('does not queue conflict jobs when detection is disabled', function () {
    config(['services.conflicts_detection' => false]);

    Activity::factory()->for(User::factory()->create(), 'user')->create();

    Queue::assertNothingPushed();
});

it('queues a conflict job on create when detection is enabled', function () {
    config(['services.conflicts_detection' => true]);

    $activity = Activity::factory()->for(User::factory()->create(), 'user')->create();

    Queue::assertPushed(ResourceConflictDetectorJob::class, function (ResourceConflictDetectorJob $job) use ($activity) {
        $properties = (new ReflectionClass($job))->getProperty('resource_type')->getValue($job);

        return $properties === 'activity';
    });
});

it('queues a conflict job on update when detection is enabled', function () {
    config(['services.conflicts_detection' => true]);

    $activity = Activity::factory()->for(User::factory()->create(), 'user')->create();
    Queue::fake(); // reset: ignore events fired during factory setup

    $activity->update(['name' => 'renamed-activity']);

    Queue::assertPushed(ResourceConflictDetectorJob::class);
});

it('queues a conflict job for course updates when detection is enabled', function () {
    config(['services.conflicts_detection' => true]);

    $course = Course::factory()->for(User::factory()->create(), 'user')->create();
    Queue::fake();

    $course->update(['description' => 'updated description']);

    Queue::assertPushed(ResourceConflictDetectorJob::class);
});
