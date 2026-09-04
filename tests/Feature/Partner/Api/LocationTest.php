<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Location;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
});

describe('Location Controller Tests', function () {
    it('returns location for a valid activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $response = $this->getJson(route('partner.location.get', ['owner_type' => 'activity', 'owner_id' => $activity->id]))
            ->assertOk();

        expect($response->json('payload.location'))->not->toBeNull()
            ->and($response->json('payload.location.long'))->toBe($activity->location->long);
    });

    it('returns 404 for location of an invalid activity', function () {
        $this->getJson(route('partner.location.get', ['owner_type' => 'activity', 'owner_id' => 12312]))->assertStatus(404);
    });

    it('creates a new location for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->location()->delete();

        $locationData = Location::factory()->make()->toArray();

        $response = $this->postJson(route('partner.location.create', ['owner_type' => 'activity', 'owner_id' => $activity->id]), $locationData)
            ->assertOk();

        expect($response->json('payload.location'))->not->toBeNull();
    });

    it('fails to create location when one already exists', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $locationData = Location::factory()->make()->toArray();

        $this->postJson(route('partner.location.create', ['owner_type' => 'activity', 'owner_id' => $activity->id]), $locationData)
            ->assertStatus(400);
    });

    it('fails to create location with invalid data', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->location()->delete();

        $this->postJson(route('partner.location.create', ['owner_type' => 'activity', 'owner_id' => $activity->id]), [])
            ->assertStatus(422);
    });

    it('fails to create location for an invalid activity', function () {
        $this->postJson(route('partner.location.create', ['owner_type' => 'activity', 'owner_id' => 123]))->assertStatus(404);
    });

    it('updates a specific location', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $location = $activity->location;
        $updateData = Location::factory()->make()->toArray();

        $response = $this->patchJson(route('partner.location.update', ['owner_type' => 'activity', 'owner_id' => $activity->id]), [
            'location_id' => $location->id,
            ...$updateData,
        ])->assertOk();

        expect($response->json('payload.location'))->not->toBeNull()
            ->and($response->json('payload.location.long'))->toBe($updateData['long']);
    });

    it('deletes a specific location', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $location = $activity->location;

        $this->deleteJson(route('partner.location.delete', ['owner_type' => 'activity', 'owner_id' => $activity->id]), [
            'location_id' => $location->id,
        ])->assertOk();

        expect(Location::find($location->id))->toBeNull();
    });
});
