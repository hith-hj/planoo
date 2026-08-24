<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;

/**
 * Covers improvement report 6.9 / 7.6: deleting a course or event must clean
 * up related records (previously the observers were fully commented out,
 * leaving orphaned days, locations, appointments and pivot rows behind).
 */
beforeEach(function () {
    Category::factory()->count(3)->create();
    Tag::factory()->count(3)->create();
});

it('cleans up related records when a course is deleted', function () {
    $course = Course::factory()->for(User::factory()->create(), 'user')->create();

    expect($course->days()->count())->toBeGreaterThan(0)
        ->and($course->location()->count())->toBe(1)
        ->and($course->appointments()->count())->toBeGreaterThan(0)
        ->and($course->customers()->count())->toBe(1);

    $notificationsBefore = Notification::count();

    $course->delete();

    expect($course->days()->count())->toBe(0)
        ->and($course->location()->count())->toBe(0)
        ->and($course->appointments()->count())->toBe(0)
        ->and($course->tags()->count())->toBe(0)
        ->and($course->customers()->count())->toBe(0)
        // every attached customer must be notified about the removal
        ->and(Notification::count())->toBeGreaterThan($notificationsBefore);
});

it('cleans up related records when an event is deleted', function () {
    $event = Event::factory()->for(User::factory()->create(), 'user')->create();

    expect($event->days()->count())->toBeGreaterThan(0)
        ->and($event->location()->count())->toBe(1)
        ->and($event->appointments()->count())->toBeGreaterThan(0)
        ->and($event->customers()->count())->toBe(1);

    $notificationsBefore = Notification::count();

    $event->delete();

    expect($event->days()->count())->toBe(0)
        ->and($event->location()->count())->toBe(0)
        ->and($event->appointments()->count())->toBe(0)
        ->and($event->tags()->count())->toBe(0)
        ->and($event->customers()->count())->toBe(0)
        ->and(Notification::count())->toBeGreaterThan($notificationsBefore);
});
