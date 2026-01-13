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
        $res = $this->postJson("{$this->url}/all")->assertOk();
        expect($res->json('payload'))->toHaveKeys(['page', 'perPage', 'activities']);
        expect($res->json('payload.activities'))->toHaveCount(2);
    });

    it('returns paginated activities ', function () {
        Activity::truncate();
        Activity::factory(2)->create();
        $res = $this->postJson("{$this->url}/all?page=1&perPage=1")->assertOk();
        expect($res->json('payload'))->toHaveKeys(['page', 'perPage', 'activities']);
        expect($res->json('payload.activities'))->toHaveCount(1)
            ->and($res->json('payload.page'))->toBe(1)
            ->and($res->json('payload.perPage'))->toBe(1);
    });

    it('finds a specific activity by ID', function () {
        $activity = Activity::factory()->create();

        $res = $this->getJson("{$this->url}/find?activity_id={$activity->id}")
            ->assertOk();

        expect($res->json('payload.activity.id'))->toBe($activity->id);
    });

    it('fails to find an activity with invalid ID', function () {
        $this->getJson("{$this->url}/find?activity_id=422")->assertStatus(422);
    });

});
