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

it('accepts returned day ids and second precision while preserving omitted weekdays', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    $day = $owner->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);
    $omitted = $owner->days()->create(['day' => 'tuesday', 'start' => '09:00', 'end' => '12:00']);

    $this->patchJson(route("partner.{$type}.update"), [
        $type.'_id' => $owner->id, ...$owner->toArray(), 'days' => [[
            'id' => $day->id, 'day' => 'monday', 'start' => '09:15:00', 'end' => '13:15:00', 'is_active' => true,
        ]],
    ])->assertOk();

    expect($owner->days()->count())->toBe(2)
        ->and($omitted->fresh())->not->toBeNull()
        ->and($day->fresh()->start)->toStartWith('09:15');
})->with(['activity', 'course', 'event']);

it('rejects day ids belonging to another owner', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    $other = ($this->scheduleOwner)($type);
    $day = $other->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);

    $this->patchJson(route("partner.{$type}.update"), [
        $type.'_id' => $owner->id, ...$owner->toArray(), 'days' => [[
            'id' => $day->id, 'day' => 'monday', 'start' => '10:00', 'end' => '13:00',
        ]],
    ])->assertNotFound();

    expect($owner->days()->count())->toBe(0)
        ->and($day->fresh()->start)->toStartWith('09:00');
})->with(['activity', 'course', 'event']);

it('protects overnight appointments using dated intervals', function (string $type) {
    $owner = ($this->scheduleOwner)($type);
    $day = $owner->days()->create(['day' => 'monday', 'start' => '22:00', 'end' => '02:00']);
    $owner->appointments()->create([
        'date' => now()->next('monday')->toDateString(), 'time' => '23:00', 'end_at' => '01:00',
        'session_duration' => 120, 'price' => 100, 'status' => AppointmentStatus::accepted->value,
    ]);
    $payload = [$type.'_id' => $owner->id, ...$owner->toArray(), 'days' => [[
        'id' => $day->id, 'day' => 'monday', 'start' => '21:00', 'end' => '03:00',
    ]]];
    $this->patchJson(route("partner.{$type}.update"), $payload)->assertOk();
    $payload['days'][0]['end'] = '00:30';
    $this->patchJson(route("partner.{$type}.update"), $payload)->assertStatus(400);
    expect($day->fresh()->end)->toStartWith('03:00');
})->with(['activity', 'course', 'event']);

it('keeps event end dates consistent across schedule and metadata updates and excludes inactive days', function () {
    $owner = ($this->scheduleOwner)('event');
    $start = now()->next('monday');
    $payload = ['event_id' => $owner->id, ...$owner->toArray(),
        'event_duration' => 2, 'start_date' => $start->toDateString(), 'days' => [
            ['day' => 'monday', 'start' => '22:00', 'end' => '02:00'],
            ['day' => 'friday', 'start' => '09:00', 'end' => '12:00', 'is_active' => false],
        ],
    ];
    $expected = $start->copy()->addDays(8)->toDateString();
    $this->patchJson(route('partner.event.update'), $payload)->assertOk()->assertJsonPath('payload.event.end_date', $expected);

    unset($payload['days']);
    $payload['name'] = 'New name';
    $this->patchJson(route('partner.event.update'), $payload)->assertOk()->assertJsonPath('payload.event.end_date', $expected);
    expect($owner->fresh()->end_date)->toBe($expected);
});
