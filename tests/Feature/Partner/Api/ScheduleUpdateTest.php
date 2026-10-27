<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Event;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->scheduleOwner = function (string $type) {
        $model = match ($type) {
            'activity' => Activity::class,
            'course' => Course::class,
            'event' => Event::class,
        };
        $owner = $model::factory()->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')->create();
        $owner->appointments()->delete();
        $owner->days()->delete();

        return $owner;
    };
});

it('updates schedules for activities courses and events through their own update endpoints', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    $monday = $owner->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);
    $removed = $owner->days()->create(['day' => 'tuesday', 'start' => '09:00', 'end' => '12:00']);
    $payload = [$type.'_id' => $owner->id, ...$owner->toArray(), 'days' => [
        ['day' => 'monday', 'start' => '08:00', 'end' => '14:00'],
        ['day' => 'friday', 'start' => '10:00', 'end' => '12:00'],
    ]];
    if ($type === 'event') {
        $payload['start_date'] = now()->next('monday')->toDateString();
        $payload['event_duration'] = 2;
    }

    $response = $this->patchJson(route("partner.{$type}.update"), $payload)->assertOk();
    expect($response->json("payload.{$type}.days"))->toHaveCount(2)
        ->and($owner->days()->where('day', 'monday')->first()->id)->toBe($monday->id)
        ->and($monday->fresh()->start)->toStartWith('08:00')
        ->and($owner->days()->whereKey($removed->id)->exists())->toBeFalse();
    if ($type === 'event') {
        expect($owner->fresh()->end_date)->toBe(now()->next('monday')->addDays(4)->toDateString());
    }
})->with(['activity', 'course', 'event']);

it('leaves schedules unchanged when days are omitted for every owner type', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    $owner->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);
    $before = $owner->days()->get()->toArray();

    $this->patchJson(route("partner.{$type}.update"), [
        $type.'_id' => $owner->id, ...$owner->toArray(), 'name' => 'Updated',
    ])->assertOk();

    expect($owner->days()->get()->toArray())->toBe($before);
})->with(['activity', 'course', 'event']);

it('protects upcoming accepted appointments for every owner type and rolls back rejected changes', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    $day = $owner->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);
    $owner->appointments()->create([
        'date' => now()->next('monday')->toDateString(), 'time' => '10:00', 'end_at' => '11:00',
        'session_duration' => 60, 'price' => 100, 'status' => AppointmentStatus::accepted->value,
    ]);

    foreach ([
        ['day' => 'friday', 'start' => '09:00', 'end' => '12:00'],
        ['day' => 'monday', 'start' => '09:00', 'end' => '12:00', 'is_active' => false],
        ['day' => 'monday', 'start' => '11:00', 'end' => '12:00'],
    ] as $newDay) {
        $this->patchJson(route("partner.{$type}.update"), [
            $type.'_id' => $owner->id, ...$owner->toArray(), 'name' => 'Must not save', 'days' => [$newDay],
        ])->assertStatus(400)->assertJson(['message' => 'The schedule cannot exclude an upcoming accepted appointment.']);
        expect($owner->fresh()->name)->toBe($owner->name)
            ->and($day->fresh()->is_active)->toBeTrue()
            ->and($day->fresh()->start)->toStartWith('09:00');
    }

    $this->patchJson(route("partner.{$type}.update"), [
        $type.'_id' => $owner->id, ...$owner->toArray(), 'days' => [
            ['day' => 'monday', 'start' => '08:00', 'end' => '14:00'],
        ],
    ])->assertOk();
})->with(['activity', 'course', 'event']);

it('allows schedules to change when appointments are canceled or in the past', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    foreach ([
        [now()->next('monday')->toDateString(), AppointmentStatus::canceled->value],
        [today()->subWeek()->toDateString(), AppointmentStatus::accepted->value],
    ] as [$date, $status]) {
        $owner->appointments()->create([
            'date' => $date, 'time' => '10:00', 'end_at' => '11:00',
            'session_duration' => 60, 'price' => 100, 'status' => $status,
        ]);
    }

    $this->patchJson(route("partner.{$type}.update"), [
        $type.'_id' => $owner->id, ...$owner->toArray(), 'days' => [
            ['day' => 'friday', 'start' => '08:00', 'end' => '09:00'],
        ],
    ])->assertOk();
})->with(['activity', 'course', 'event']);
