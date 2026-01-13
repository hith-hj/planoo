<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Day;
use App\Models\Event;
use App\Models\Location;
use App\Models\Media;
use App\Models\Tag;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/event';
});

describe('Event Controller Tests', function () {
    it('returns all events for the authenticated partner', function () {
        $this->user->events()->delete();
        Event::factory(2)->for($this->user, 'user')->create();

        $response = $this->getJson("{$this->url}/all")->assertOk();

        expect($response->json('payload.events'))->toHaveCount(2);
    });

    it('finds a specific event by ID', function () {
        $event = Event::factory()->for($this->user, 'user')->create();

        $response = $this->getJson("{$this->url}/find?event_id={$event->id}")
            ->assertOk();

        expect($response->json('payload.event.id'))->toBe($event->id);
    });

    it('fails to find an event with invalid ID', function () {
        $this->getJson("{$this->url}/find?event_id=422")->assertStatus(422);
    });

    it('creates a new event with days, location, media, and tags', function () {
        $eventData = Event::factory()->for($this->user, 'user')->make()->toArray();
        $days = Day::factory()->days();
        $location = Location::factory()->make()->toArray();
        $media = [
            'type' => 'image',
            'media' => Media::factory()->medias(2),
        ];
        $tags = Tag::factory()->tags()->make()->toArray();

        $payload = array_merge($eventData, ['days' => $days], $location, $media, $tags);

        $response = $this->postJson("{$this->url}/create", $payload);
        $response->assertOk();

        expect($response->json('payload.event'))->not->toBeNull();

        $createdId = $response->json('payload.event.id');
        $createdEvent = Event::with(['tags', 'days', 'medias', 'location'])->find($createdId);

        expect($createdEvent->days()->count())->toBe(count($days))
            ->and($createdEvent->location)->not->toBeNull()
            ->and($createdEvent->location->long)->toBe($location['long'])
            ->and($createdEvent->tags)->not->toBeNull()
            ->and($createdEvent->medias)->not->toBeNull()
            ->and($createdEvent->medias->count())->toBe(2);
    });

    it('fails to create an event with invalid data', function () {
        $this->postJson("{$this->url}/create", [])->assertStatus(422);
    });

    it('updates an existing event', function () {
        $event = Event::factory()->for($this->user, 'user')->create();
        $event->update(['name' => 'tido']);

        $updatePayload = ['event_id' => $event->id, ...$event->toArray()];

        $response = $this->patchJson("{$this->url}/update", $updatePayload);
        $response->assertOk();

        expect($response->json('payload.event.name'))->toBe('tido');
    });

    it('fails to update an event with invalid data', function () {
        $this->patchJson("{$this->url}/update", [])->assertStatus(422);
    });

    it('deletes an event', function () {
        $event = Event::factory()->for($this->user, 'user')->create();

        $this->deleteJson("{$this->url}/delete", [
            'event_id' => $event->id,
        ])->assertOk();

        expect(Event::find($event->id))->toBeNull();
    });

    it('toggles the activation status of an event', function () {
        $event = Event::factory()
            ->for($this->user, 'user')
            ->create(['is_active' => false]);

        $this->postJson("{$this->url}/toggleActivation", [
            'event_id' => $event->id,
        ])->assertOk();

        expect($event->fresh()->is_active)->toBeTrue();
    });

    it('can attend customer by id for event', function () {
        $event = Event::factory()->for($this->user, 'user')->create();
        $res = $this->postJson("{$this->url}/attend?event_id={$event->id}", ['customer_id' => 1]);
        $res->assertOk();
        $customerEvent = $event->customers()->wherePivot('customer_id', 1)->first();
        expect($customerEvent)->not->toBeNull();
    });

    it('can attend customer by phone for event', function () {
        $event = Event::factory()->for($this->user, 'user')->create();
        $res = $this->postJson("{$this->url}/attend?event_id={$event->id}", ['customer_phone' => '0987654321']);
        $res->assertOk();
        $customer = Customer::where('phone', '0987654321')->first();
        $customerEvent = $event->customers()->wherePivot('customer_id', $customer->id)->first();
        expect($customerEvent)->not->toBeNull();
    });

    it('can not attend full event', function () {
        $event = Event::factory()->for($this->user, 'user')->create(['is_full' => true]);
        $res = $this->postJson("{$this->url}/attend?event_id={$event->id}", ['customer_id' => 1]);
        $res->assertStatus(400);
    });

    it('can cancel event attend by customer id', function () {
        $event = Event::factory()->for($this->user, 'user')->create();
        $this->postJson("{$this->url}/attend?event_id={$event->id}", ['customer_id' => 1]);
        $res = $this->postJson("{$this->url}/cancel?event_id={$event->id}", ['customer_id' => 1]);
        $res->assertOk();
        expect($event->customers()->wherePivot('customer_id', 1)->first())->toBeNull();
    });

    it('can not cancel event after specifc time', function () {
        $event = Event::factory()->for($this->user, 'user')->create();
        $this->postJson("{$this->url}/attend?event_id={$event->id}", ['customer_id' => 1]);
        $customer = $event->customers()->where('customer_id', 1)->first();
        $customer->pivot->update(['created_at' => now()->subDays(2)]);
        $res = $this->postJson("{$this->url}/cancel?event_id={$event->id}", ['customer_id' => 1]);
        $res->assertStatus(400);
    });

});
