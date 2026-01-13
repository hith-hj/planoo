<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->seed();
});

describe('test NotifyCourseCustomer Command', function () {
    it('creates appointments and sends notifications', function () {
        $instructor = User::factory()->create();
        $course = Course::factory()->for($instructor, 'user')->create();
        $course->days()->delete();
        $customer = Customer::factory()->create();
        $course->customers()->attach($customer->id, [
            'remaining_sessions' => 2,
            'is_complete' => false,
        ]);
        $course->days()->create([
            'day' => mb_strtolower(Carbon::now()->format('l')),
            'start' => '10:00',
            'end' => '20:00',
        ]);
        Appointment::truncate();
        artisan('app:nccs')->assertExitCode(0);
        assertDatabaseHas('appointments', [
            'appointable_id' => $course->id,
            'appointable_type' => $course::class,
            'date' => Carbon::now()->toDateString(),
            'time' => '10:00',
            'status' => AppointmentStatus::accepted->value,
        ]);
        assertDatabaseHas('course_customer', [
            'customer_id' => $customer->id,
            'course_id' => $course->id,
            'remaining_sessions' => 1,
            'is_complete' => false,
        ]);
        assertDatabaseHas('notifications', [
            'belongTo_id' => $customer->id,
            'belongTo_type' => $customer::class,
            'is_viewed' => 0,
            'type' => NotificationTypes::session->value,
        ]);
    });

    it('marks customer complete and sends finish notification', function () {
        $course = Course::factory()->create();
        $course->days()->delete();
        $course->days()->create([
            'day' => mb_strtolower(Carbon::now()->format('l')),
            'start' => '10:00',
            'end' => '20:00',
        ]);

        $customer = Customer::factory()->create();
        $course->customers()->attach($customer->id, [
            'remaining_sessions' => 1,
            'is_complete' => false,
        ]);

        $course->user()->associate(User::factory()->create())->save();

        artisan('app:nccs')->assertExitCode(0);
        assertDatabaseHas('course_customer', [
            'customer_id' => $customer->id,
            'is_complete' => true,
            'remaining_sessions' => 0,
        ]);

        assertDatabaseHas('notifications', [
            'belongTo_id' => $customer->id,
            'belongTo_type' => $customer::class,
            'is_viewed' => 0,
            'type' => NotificationTypes::course->value,
        ]);
    });

    it('cancels conflicting appointments', function () {
        $user = User::factory()->create();
        $course = Course::factory()->for($user, 'user')->create();
        $course->days()->delete();
        $course->days()->create([
            'day' => mb_strtolower(Carbon::now()->format('l')),
            'start' => '10:00',
            'end' => '20:00',
        ]);
        Appointment::truncate();
        $conflict = Appointment::factory()->create([
            'date' => Carbon::now()->toDateString(),
            'time' => '10:00',
            'status' => AppointmentStatus::accepted->value,
        ]);
        $conflict->holder()->associate($course)->save();
        artisan('app:nccs')->assertExitCode(0);
        assertDatabaseHas('appointments', [
            'id' => $conflict->id,
            'status' => AppointmentStatus::canceled->value,
        ]);
    });
});
