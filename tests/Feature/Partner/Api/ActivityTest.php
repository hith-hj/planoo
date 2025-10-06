<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Day;
use App\Models\Location;
use App\Models\Media;
use App\Models\Tag;

beforeEach(function () {
	$this->seed();
	$this->user('partner', 'stadium')->api();
	$this->url = '/api/partner/v1/activity';
});

describe('Activity Controller tests', function () {
	it('returns all activities for the authenticated partner', function () {
		$this->user->activities()->delete();
		Activity::factory(2)->for($this->user, 'user')->create();
		$res = $this->getJson("$this->url/all")->assertOk();
		expect($res->json('payload.activities'))->toHaveCount(2);
	});

	it('finds a specific activity for user by ID', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();

		$res = $this->getJson("$this->url/find?activity_id=$activity->id");

		$res->assertOk();
		expect($res->json('payload.activity.id'))->toBe($activity->id);
	});

	it('cant finds a specific activity for user by invalid ID', function () {
		$this->getJson("$this->url/find?activity_id=422")->assertStatus(422);
	});

	it('creates a new activity', function () {
		$activity = Activity::factory()->for($this->user, 'user')
			->make(['user_id' => $this->user->id])->toArray();
		$days = Day::factory()->days()->make()->toArray();
		$location = Location::factory()->make()->toArray();
		$media = [
			'type' => 'image',
			'media' => [Media::factory()->fakeMedia(), Media::factory()->fakeMedia()]
		];
		$tags = Tag::factory()->tags()->make()->toArray();
		$data = array_merge($activity, $days, $location, $media, $tags);
		$res = $this->postJson(
			"$this->url/create",
			$data
		);
		$res->assertOk();
		expect($res->json('payload.activity'))->not->toBeNull();
		$id = $res->json('payload.activity.id');
		$activity = Activity::with(['tags', 'days', 'medias', 'location'])->find($id);
		expect($activity->days()->count())->toBe(count($days['days']))
			->and($activity->location->long)->toBe($location['long']);
	});

	it('cant creates a new activity with invalid data', function () {
		$this->postJson("$this->url/create", [])->assertStatus(422);
	});

	it('updates an existing activity', function () {
		$activity = Activity::factory()
			->for($this->user, 'user')
			->create();
		$activity->update(['name' => 'tido']);
		$data = ['activity_id' => $activity->id, ...$activity->toArray()];
		$res = $this->patchJson("$this->url/update", $data);
		$res->assertOk();
		expect($res->json('payload.activity.name'))->toBe($activity->name);
	});

	it('cant updates an existing activity with invalid data', function () {
		$this->patchJson("$this->url/update", [])->assertStatus(422);
	});

	it('deletes an activity', function () {
		$activity = Activity::factory()
			->for($this->user, 'user')
			->create();

		$res = $this->deleteJson("$this->url/delete", [
			'activity_id' => $activity->id,
		]);

		$res->assertOk();
		expect(Activity::find($activity->id))->toBeNull();
	});

	it('toggles activation of an activity', function () {
		$activity = Activity::factory()
			->for($this->user, 'user')
			->create(['is_active' => false]);

		$res = $this->postJson("$this->url/toggleActivation", [
			'activity_id' => $activity->id,
		]);

		$res->assertOk();
		$activity->refresh();
		expect($activity->is_active)->toBeTrue();
	});
});
