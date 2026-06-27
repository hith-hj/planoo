<?php

use App\Models\Court;
use App\Models\Activity;
use App\Jobs\ResourceConflictDetectorJob;
use App\Models\Course;
use App\Models\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->seed();
    Queue::fake();

});

test('creating an activity dispatches the conflict check job', function () {
    $court = Court::factory()->create();
    $activity = Activity::factory()->for($court,'court')->create();

    Queue::assertPushed(ResourceConflictDetectorJob::class, function ($job) use ($activity) {
        $reflect = new ReflectionClass($job);
        $resource_type = $reflect->getProperty('resource_type')->getValue($job);
        $resource_id = $reflect->getProperty('resource_id')->getValue($job);
        return $resource_type === 'activity' && $resource_id === $activity->id;
    });
});

test('detects duplicate schedules successfully', function () {
    $court = Court::factory()->create();

    $event = Event::factory()->for($court,'court')->create();
    $event->days()->create([
        'day'   => 'Monday',
        'start' => '10:00',
        'end'   => '12:00',
    ]);

    $newActivity = Activity::factory()->for($court,'court')->create();
    $newActivity->days()->create([
        'day'   => 'Monday',
        'start' => '10:00',
        'end'   => '12:00',
    ]);

    Artisan::call('resource:detect-conflict', [
        '--resource-type' => 'activity',
        '--resource-id'   => $newActivity->id,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Conflicts detected');
    expect($output)->toContain('duplication');
});

test('detects overlapping schedules successfully', function () {
    $court = Court::factory()->create();

    $existingActivity = Activity::factory()->for($court,'court')->create();
    $existingActivity->days()->create([
        'day'   => 'Monday',
        'start' => '10:00',
        'end'   => '12:00',
    ]);

    $newActivity = Activity::factory()->for($court,'court')->create();
    $newActivity->days()->create([
        'day'   => 'Monday',
        'start' => '11:00',
        'end'   => '13:00',
    ]);

    Artisan::call('resource:detect-conflict', [
        '--resource-type' => 'activity',
        '--resource-id'   => $newActivity->id,
    ]);

    $output = Artisan::output();

    expect($output)->toContain('Conflicts detected');
    expect($output)->toContain('intersection');
});

test('confirms a clean schedule when no conflicts exist', function () {
    $court = Court::factory()->create();

    $existingActivity = Activity::factory()->for($court,'court')->create();
    $existingActivity->days()->create([
        'day'   => 'Monday',
        'start' => '10:00',
        'end'   => '12:00',
    ]);

    $newActivity = Activity::factory()->for($court,'court')->create();
    $newActivity->days()->create([
        'day'   => 'Monday',
        'start' => '13:00',
        'end'   => '15:00',
    ]);

    Artisan::call('resource:detect-conflict', [
        '--resource-type' => 'activity',
        '--resource-id'   => $newActivity->id,
    ]);

    $output = Artisan::output();

    expect($output)->toContain('No conflicts found');
});

test('confirms a clean schedule when no conflicts exist with courses', function () {
    $court = Court::factory()->create();

    $existingActivity = Activity::factory()->for($court,'court')->create();
    $existingActivity->days()->create([
        'day'   => 'Monday',
        'start' => '10:00',
        'end'   => '12:00',
    ]);

    $course = Course::factory()->for($court,'court')->create();
    $course->days()->create([
        'day'   => 'Monday',
        'start' => '13:00',
        'end'   => '15:00',
    ]);

    Artisan::call('resource:detect-conflict', [
        '--resource-type' => 'activity',
        '--resource-id'   => $existingActivity->id,
    ]);

    $output = Artisan::output();

    expect($output)->toContain('No conflicts found');
});
