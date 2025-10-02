<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Location;

beforeEach(function () {
	$this->seed();
	$this->user('partner', 'stadium')->api();
	$this->url = '/api/partner/v1/location';
});

describe('location Controller tests', function () {
	it('returns location for activity', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$res = $this->getJson("$this->url/get/activity/{$activity->id}")->assertOk();
		expect($res->json('payload.location'))->not->toBeNull()
			->and($res->json('payload.location.long'))->toBe($activity->location->long);
	});

	it('fails returns location for invalid activity', function () {
		$this->getJson("$this->url/get/activity/1000")->assertStatus(404);
	});

	it('creates new location', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->location()->delete();
		$location = Location::factory()->make()->toArray();
		$res = $this->postJson(
			"$this->url/create/activity/{$activity->id}",
			$location
		);
		$res->assertOk();
		expect($res->json('payload.location'))->not->toBeNull();
	});

	it('cant creates location when location exists', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$location = Location::factory()->make()->toArray();
		$this->postJson(
			"$this->url/create/activity/{$activity->id}",
			$location
		)->assertStatus(400);
	});

	it('cant creates location with invalid data', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->location()->delete();
		$this->postJson(
			"$this->url/create/activity/{$activity->id}",
			[]
		)->assertStatus(422);
	});

	it('cant creates location with invalid activity', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$this->postJson(
			"$this->url/create/activity/1000",
			[]
		)->assertStatus(404);
	});

	it('can update specific location', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$location = $activity->location;
		$data = Location::factory()->make()->toArray();
		$res = $this->patchJson(
			"$this->url/update/activity/{$activity->id}",
			['location_id' => $location->id, ...$data]
		);
		$res->assertOk();
		expect($res->json('payload.location'))->not->toBeNull()
		->and($res->json('payload.location.long'))->toBe($data['long']);
	});

	it('can delete specific location', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$location = $activity->location;
		$res = $this->deleteJson(
			"$this->url/delete/activity/{$activity->id}",
			['location_id' => $location->id,]
		);
		$res->assertOk();
		expect(Location::find($location->id))->toBeNull();
	});
});
