<?php

declare(strict_types=1);

use App\Models\Activity;

beforeEach(function () {
	$this->seed();
	$this->user('customer')->api();
	$this->url = '/api/customer/v1/activity';
});

describe('Activity Controller Tests', function () {
    it('returns all activities for the authenticated customer', function () {
        Activity::truncate();
        Activity::factory(2)->create();
        $response = $this->getJson("{$this->url}/all")->assertOk();

        expect($response->json('payload.activities'))->toHaveCount(2);
    });

    it('finds a specific activity by ID', function () {
        $activity = Activity::factory()->create();

        $response = $this->getJson("{$this->url}/find?activity_id={$activity->id}")
            ->assertOk();

        expect($response->json('payload.activity.id'))->toBe($activity->id);
    });

    it('fails to find an activity with invalid ID', function () {
        $this->getJson("{$this->url}/find?activity_id=422")->assertStatus(422);
    });

});
