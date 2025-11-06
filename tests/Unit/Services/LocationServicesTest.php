<?php

declare(strict_types=1);

use App\Services\LocationServices;
use App\Models\Activity;
use App\Models\Location;


beforeEach(function () {
    $this->seed();
    $this->locationServices = new LocationServices();
    $this->owner = Activity::factory()->create();
    $this->data = [
            'long' => 35.0, 'lat' => 40.0,
        ];
});


describe('Location Service', function () {

    it('creates a location', function () {
        $this->owner->location()->delete();
        expect($this->owner->location)->toBeNull();
        $location = $this->locationServices->create($this->owner, $this->data);
        expect($location)->toBeInstanceOf(Location::class);
        expect($this->owner->fresh()->location)->not->toBeNull();
        expect($this->owner->fresh()->location->long)->toBe($this->data['long']);
    });

    it('fails to create location when missing location method', function () {
        $this->locationServices->create((object) [], $this->data);
    })->throws(TypeError::class);

    it('can update location', function () {
        expect($this->owner->location)->not->toBeNull();
        $result = $this->locationServices->update($this->owner, $this->data);

        expect($result)->toBeInstanceOf(Location::class);
        expect($this->owner->fresh()->location->long)->toBe($this->data['long']);
    });

    it('fails to update location when badge missing location method', function () {
        $this->locationServices->update((object) [], $this->data);
    })->throws(TypeError::class);

});
