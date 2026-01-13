<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Day;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/day';
});

describe('Day Controller tests', function () {
    it('returns all days for a given activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $response = $this->getJson("{$this->url}/all/activity/{$activity->id}")
            ->assertOk();

        expect($response->json('payload.days'))->toHaveCount($activity->days()->count());
    });

    it('returns 404 for non-existent activity when fetching days', function () {
        $this->getJson("{$this->url}/all/activity/9999")->assertStatus(404);
    });

    it('finds a specific day by ID', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $day = $activity->days()->first();

        $response = $this->getJson("{$this->url}/find/activity/{$activity->id}?day_id={$day->id}")
            ->assertOk();

        expect($response->json('payload.day.id'))->toBe($day->id);
    });

    it('returns 404 when finding a day with invalid activity and day ID', function () {
        $this->getJson("{$this->url}/find/activity/9999?day_id=9999")->assertStatus(404);
    });

    it('creates a new day for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->days()->delete();

        $dayData = Day::factory()->day()->make()->toArray();

        $response = $this->postJson("{$this->url}/create/activity/{$activity->id}", $dayData)
            ->assertOk();

        expect($response->json('payload.day'))->not->toBeNull()
            ->and($response->json('payload.day.day'))->toBe($dayData['day'])
            ->and($response->json('payload.day.start'))->toBe($dayData['start'])
            ->and($response->json('payload.day.end'))->toBe($dayData['end']);
    });

    it('creates multiple days for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->days()->delete();
        $days = Day::factory()->days();
        $response = $this->postJson("{$this->url}/createMany/activity/{$activity->id}", ['days' => $days])
            ->assertOk();

        expect($response->json('payload.days'))->not->toBeNull()
            ->and($response->json('payload.days'))->toHaveCount(count($days));
    });

    it('cant creates more that 7 days for activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->days()->delete();
        $this->postJson(
            "{$this->url}/createMany/activity/{$activity->id}",
            ['days' => Day::factory()->days(7)]
        )->assertOk();

        $this->postJson(
            "{$this->url}/createMany/activity/{$activity->id}",
            ['days' => Day::factory()->days(1)]
        )->assertStatus(400);
    });

    it('fails to create a day with invalid data', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $this->postJson("{$this->url}/create/activity/{$activity->id}", [])
            ->assertStatus(422);
    });

    it('fails to create a duplicate day', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $existingDay = $activity->days()->first()->toArray();

        $this->postJson("{$this->url}/create/activity/{$activity->id}", $existingDay)
            ->assertStatus(400);
    });

    it('updates a specific day', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $day = $activity->days()->first();
        $updateData = Day::factory()->day()->make()->toArray();

        $response = $this->patchJson("{$this->url}/update/activity/{$activity->id}", [
            'day_id' => $day->id,
            ...$updateData,
        ])->assertOk();

        expect($response->json('payload.day'))->not->toBeNull();
    });

    it('toggles activation status of a day', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $day = $activity->days()->first();
        $day->update(['is_active' => false]);

        expect($day->is_active)->toBeFalse();

        $this->postJson("{$this->url}/toggleActivation/activity/{$activity->id}", [
            'day_id' => $day->id,
        ])->assertOk();

        expect($day->fresh()->is_active)->toBeTrue();
    });

    it('deletes a specific day', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        Day::factory(2)->day()->for($activity, 'holder')->create();
        $day = $activity->days()->first();

        $res = $this->deleteJson("{$this->url}/delete/activity/{$activity->id}", [
            'day_id' => $day->id,
        ]);
        $res->assertOk();

        expect(Day::find($day->id))->toBeNull();
    });

    it('cant deletes the last day', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $day = $activity->days()->first();

        $res = $this->deleteJson("{$this->url}/delete/activity/{$activity->id}", [
            'day_id' => $day->id,
        ]);
        $res->assertStatus(400);
    });
});
