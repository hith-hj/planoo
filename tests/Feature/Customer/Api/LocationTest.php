<?php

declare(strict_types=1);

use App\Models\Location;

beforeEach(function () {
	$this->seed();
	$this->user('customer')->api();
	$this->url = '/api/customer/v1/location';
	Location::factory()->for($this->user,'holder')->create();
});

describe('Location Controller Tests', function () {
    it('returns location for a valid customer', function () {
        $res = $this->getJson("{$this->url}/get");
        $res->assertOk();
        expect($res->json('payload.location'))->not->toBeNull()
            ->and($res->json('payload.location.long'))->toBe($this->user->location->long);
    });

    it('creates a new location for an customer', function () {
		$customer = $this->user;
        $customer->location()->delete();
        $locationData = Location::factory()->make()->toArray();
        $res = $this->postJson("{$this->url}/create", $locationData)->assertOk();
        expect($res->json('payload.location'))->not->toBeNull();
    });

    it('fails to create location when one already exists', function () {
        $locationData = Location::factory()->make()->toArray();
        $this->postJson("{$this->url}/create", $locationData)->assertStatus(400);
    });

    it('fails to create location with invalid data', function () {
        $customer = $this->user;
        $customer->location()->delete();

        $this->postJson("{$this->url}/create", [])->assertStatus(422);
    });

    it('updates a specific location', function () {
        $customer = $this->user;
        $location = $customer->location;
        $updateData = Location::factory()->make()->toArray();

        $res = $this->patchJson("{$this->url}/update/", [
            'location_id' => $location->id,
            ...$updateData,
        ])->assertOk();

        expect($res->json('payload.location'))->not->toBeNull()
            ->and($res->json('payload.location.long'))->toBe($updateData['long']);
    });

    it('deletes a specific location', function () {
        $customer = $this->user;
        $location = $customer->location;

        $res=$this->deleteJson("{$this->url}/delete", [
            'location_id' => $location->id,
        ]);
        $res->assertOk();

        expect(Location::find($location->id))->toBeNull();
    });
});
