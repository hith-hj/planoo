<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Day;
use App\Models\Tag;
use App\Models\User;
use App\Services\AppointmentServices;
use Illuminate\Support\Carbon;


beforeEach(function () {
    Category::factory()->count(3)->create();
    Tag::factory()->count(3)->create();
});

it('collects slots from all gaps of the day, not only the last one', function () {
    $activity = Activity::factory()
        ->for(User::factory()->create(), 'user')
        ->create(['is_active' => true, 'session_duration' => 60]);

    // remove the random appointment the factory spins up so the schedule
    // below is fully deterministic
    $activity->appointments()->delete();

    $day = $activity->days()->create([
        'day' => 'sunday',
        'start' => '09:00',
        'end' => '13:00',
        'is_active' => true,
    ]);

    // two busy blocks: 09:00-10:00 and 11:00-12:00 -> free gaps at 10:00 and 12:00
    foreach ([['09:00', '10:00'], ['11:00', '12:00']] as [$start, $end]) {
        Appointment::factory()->for($activity, 'holder')->create([
            'status' => App\Enums\AppointmentStatus::accepted->value,
            'date' => now()->next('sunday')->toDateString(),
            'time' => $start,
            'end_at' => $end,
            'session_duration' => 60,
        ]);
    }

    $result = (new AppointmentServices())->checkAvailableSlots($activity, [
        'day_id' => $day->id,
        'session_duration' => 60,
        'date' => now()->next('sunday')->toDateString(),
    ]);

    $starts = array_column($result['slots'], 'start');

    expect($starts)->toContain('10:00')      // first gap (lost by the old bug)
        ->and($starts)->toContain('12:00')   // last gap
        ->and($result['code'])->not->toBeNull();
});

it('matches the selected date to the requested day', function () {
    $activity = Activity::factory()
        ->for(User::factory()->create(), 'user')
        ->create(['is_active' => true]);

    $day = $activity->days()->create([
        'day' => 'monday',
        'start' => '09:00',
        'end' => '13:00',
        'is_active' => true,
    ]);

    expect(fn() => (new AppointmentServices())->checkAvailableSlots($activity, [
        'day_id' => $day->id,
        'session_duration' => 60,
        'date' => Carbon::parse('next sunday')->toDateString(),
    ]))->toThrow(Exception::class);
});
