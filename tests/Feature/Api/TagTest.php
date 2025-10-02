<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Tag;



beforeEach(function () {
	$this->seed();
	$this->user('partner', 'stadium')->api();
	$this->url = '/api/partner/v1/tag';
});

describe('tag Controller tests', function () {
	it('returns tag for activity', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$res = $this->getJson("$this->url/all/activity/$activity->id")->assertOk();
		expect($res->json('payload.tags'))->not->toBeNull();
	});

	it('fails returns tag for invalid activity', function () {
		$this->getJson("$this->url/get/activity/1000")->assertStatus(404);
	});

	it('assign new tag', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->tags()->delete();
		$tags = Tag::inRandomOrder()->take(2)->pluck('id')->toArray();
		$this->postJson(
			"$this->url/create/activity/$activity->id",
			['tags'=>$tags]
		)->assertOk();
		expect($activity->fresh()->tags->contains($tags))->not->toBeNull();
	});

	it('cant creates tag when tag exists', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->tags()->detach();
		$activity->tags()->attach(1);
		expect($activity->fresh()->tags()->count())->toBe(1);
		$this->postJson(
			"$this->url/create/activity/$activity->id",
			['tags'=>[1,2]]
		)->assertOk();
		expect($activity->fresh()->tags()->count())->toBe(2);
	});

	it('can delete tag', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->tags()->detach();
		$activity->tags()->attach(3);
		expect($activity->fresh()->tags()->count())->toBe(1);
		$this->deleteJson(
			"$this->url/delete/activity/$activity->id",
			['tags'=>[3]]
		)->assertOk();
		expect($activity->fresh()->tags()->count())->toBe(0);
	});
});
