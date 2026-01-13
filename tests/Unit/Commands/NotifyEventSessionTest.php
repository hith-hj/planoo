<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Enums\EventStatus;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->seed();
    Carbon::setTestNow(Carbon::create(2025, 11, 4, 10));
    Appointment::truncate();
    Notification::truncate();
});

describe('Notify Event Session Test', function () {
    it('notifies customers and creates appointment for matching event day', function () {
        $user = User::factory()->create();
        $event = Event::factory()
            ->hasDays(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00'])
            ->hasCustomers(2)
            ->create([
                'user_id' => $user->id,
                'end_date' => Carbon::now()->toDateString(),
                'status' => EventStatus::active->value,
            ]);
        $event->appointments()->delete();
        Artisan::call('app:nes');
        $event->refresh();
        expect($event->appointments)
            ->toHaveCount(1)
            ->and($event->status)
            ->toBe(EventStatus::completed->value);
    });

    it('skips events with no matching day', function () {
        $event = Event::factory()
            ->hasDays(['day' => 'monday', 'start' => '10:00', 'end' => '12:00'])
            ->create(['status' => EventStatus::active->value]);
        $event->appointments()->delete();
        Artisan::call('app:nes');
        expect(Appointment::count())->toBe(0);
    });

    it('cancels conflicting appointment and notifies holder', function () {
        $user = User::factory()->create();
        Event::factory()
            ->hasDays(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00'])
            ->create([
                'user_id' => $user->id,
                'status' => EventStatus::active->value,
            ]);
        $conflict = Appointment::factory()->create([
            'status' => AppointmentStatus::accepted->value,
            'date' => Carbon::now()->toDateString(),
            'time' => '10:00',
        ]);
        Artisan::call('app:nes');
        $conflict->refresh();
        expect($conflict->status)->toBe(AppointmentStatus::canceled->value);
    });

    it('outputs confirmation message', function () {
        Artisan::call('app:nes');
        $output = Artisan::output();
        expect($output)->toContain('Customers notified for the upcoming event session.');
    });
});
