<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Enums\CourseStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->seed();
    Carbon::setTestNow(Carbon::parse('2026-05-19')); // Tuesday
    Appointment::truncate();
    Notification::truncate();
});

afterEach(function () {
    Carbon::setTestNow();
});

describe('Notify Course Begun Test', function () {
    it('notifies course is soon when course starts tomorrow', function () {
        $user = User::factory()->create();

        $course = Course::factory()
            ->hasDays(['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00'])
            ->create([
                'user_id' => $user->id,
                'start_date' => Carbon::now()->addDay()->toDateString(),
                'status' => CourseStatus::pending->value,
            ]);
        $course->customers()->attach(1, [
            'remaining_sessions' => 2,
            'is_complete' => false,
        ]);
        Artisan::call('app:ncb');

        assertDatabaseHas('notifications', [
            'type' => NotificationTypes::course->value,
        ]);
    });

    it('skips courses not starting today or tomorrow', function () {
        $course = Course::factory()
            ->create([
                'start_date' => Carbon::now()->addDays(3)->toDateString(),
                'status' => CourseStatus::pending->value,
            ]);

        $course->appointments()->delete();

        Artisan::call('app:ncb');

        expect(Appointment::count())->toBe(0);
    });

    it('activates course and creates appointment when course starts today', function () {
        $user = User::factory()->create();

        $course = Course::factory()
            ->hasDays(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00'])
            ->create([
                'user_id' => $user->id,
                'start_date' => Carbon::now()->toDateString(), // Tuesday
                'status' => CourseStatus::pending->value,
            ]);
        $course->customers()->attach(2, [
            'remaining_sessions' => 2,
            'is_complete' => false,
        ]);

        $course->appointments()->delete();

        Artisan::call('app:ncb');

        assertDatabaseHas('appointments', [
            'appointable_id' => $course->id,
            'appointable_type' => $course::class,
            'status' => AppointmentStatus::accepted->value,
        ]);

        $course->refresh();

        expect($course->status)->toBe(CourseStatus::active->value)
            ->and($course->appointments)->toHaveCount(1);
    });

    it('cancels conflicting appointment and notifies holder', function () {
        $user = User::factory()->create();

        $course = Course::factory()
            ->hasDays(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00'])
            ->create([
                'user_id' => $user->id,
                'start_date' => Carbon::now()->toDateString(), // Today (Tuesday)
                'status' => CourseStatus::pending->value,
            ]);
        $course->customers()->attach(1, [
            'remaining_sessions' => 2,
            'is_complete' => false,
        ]);

        $conflict = Appointment::factory()->create([
            'date' => Carbon::now()->toDateString(),
            'time' => '10:00',
            'status' => AppointmentStatus::accepted->value,
        ]);

        Artisan::call('app:ncb');

        $conflict->refresh();

        expect($conflict->status)->toBe(AppointmentStatus::canceled->value);
    });
});
