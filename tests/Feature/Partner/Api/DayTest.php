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
	it('returns all days for activity', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$res = $this->getJson("$this->url/all/activity/{$activity->id}")->assertOk();
		expect($res->json('payload.days'))->toHaveCount($activity->days()->count());
	});

	it('fails returns days for invalid activity', function () {
		$this->getJson("$this->url/all/activity/1000")->assertStatus(404);
	});

	it('finds specific day by ID', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$day = $activity->days->first();
		$res = $this->getJson("$this->url/find/activity/{$activity->id}?day_id=$day->id")->assertOk();
		expect($res->json('payload.day.id'))->toBe($day->id);
	});

	it('fails finds specific day by invalid ID', function () {
		$this->getJson("$this->url/find/activity/1000?day_id=1000")->assertStatus(404);
	});

	it('creates new day', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$day = Day::factory()->day()->make()->toArray();
		$res = $this->postJson(
			"$this->url/create/activity/{$activity->id}",
			$day
		);
		$res->assertOk();
		expect($res->json('payload.day'))->not->toBeNull()
			->and($res->json('payload.day.day'))->toBe($day['day'])
			->and($res->json('payload.day.start'))->toBe($day['start'])
			->and($res->json('payload.day.end'))->toBe($day['end']);
	});

	it('creates many day', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->days()->delete();
		$days = Day::factory()->days()->make()->toArray();
		$res = $this->postJson(
			"$this->url/createMany/activity/{$activity->id}",
			$days
		)->assertOk();
		expect($res->json('payload.days'))->not->toBeNull()
			->and($res->json('payload.days'))->toHaveCount(count($days['days']));
	});

	it('cant creates new day with invalid data', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$this->postJson("$this->url/create/activity/{$activity->id}", [])->assertStatus(422);
	});

	it('cant creates duplicate day', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$day = $activity->days()->first()->toArray();
		$this->postJson("$this->url/create/activity/{$activity->id}", $day)->assertStatus(400);
	});

	it('can update specific day', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$day = $activity->days()->first();
		$data = Day::factory()->day()->make()->toArray();
		$res = $this->patchJson(
			"$this->url/update/activity/{$activity->id}",
			['day_id' => $day->id, ...$data]
		);
		$res->assertOk();
		expect($res->json('payload.day'))->not->toBeNull();
	});

	it('can toggle activation for specific day', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$day = $activity->days()->first();
		$day->update(['is_active' => false]);
		expect($day->is_active)->toBe(false);
		$this->postJson(
			"$this->url/toggleActivation/activity/{$activity->id}",
			['day_id' => $day->id]
		)->assertOk();
		expect($day->fresh()->is_active)->toBe(true);
	});

	it('can delete specific day', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$day = $activity->days()->first();
		$res = $this->deleteJson(
			"$this->url/delete/activity/{$activity->id}",
			['day_id' => $day->id,]
		);
		$res->assertOk();
		expect(Day::find($day->id))->toBeNull();
	});
});
