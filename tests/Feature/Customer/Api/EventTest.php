<?php

declare(strict_types=1);

use App\Models\Event;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->url = '/api/customer/v1/event';
});

describe('Event Controller Tests', function () {
    it('returns all events for the authenticated customer', function () {
        Event::truncate();
        Event::factory(2)->create();

        $res = $this->postJson("{$this->url}/all")->assertOk();

        expect($res->json('payload.events'))->toHaveCount(2);
    });

    it('returns paginated events ', function () {
        Event::truncate();
        Event::factory(2)->create();
        $res = $this->postJson("{$this->url}/all?page=1&perPage=1");
        $res->assertOk();
        expect($res->json('payload'))->toHaveKeys(['page','perPage','events']);
        expect($res->json('payload.events'))->toHaveCount(1)
        ->and($res->json('payload.page'))->toBe(1)
        ->and($res->json('payload.perPage'))->toBe(1);
    });

    it('finds a specific event by ID', function () {
        $event = Event::factory()->create();

        $res = $this->getJson("{$this->url}/find?event_id={$event->id}")
            ->assertOk();

        expect($res->json('payload.event.id'))->toBe($event->id);
    });

    it('fails to find an event with invalid ID', function () {
        $this->getJson("{$this->url}/find?event_id=422")->assertStatus(422);
    });

    it('can attend event', function () {
        $event = Event::factory()->create();
        $this->postJson("{$this->url}/attend?event_id={$event->id}")->assertOk();
        $customerEvent = $this->user->events()->wherePivot('event_id',$event->id)->first();
        expect($this->user->events()->count())->toBe(1);
    });

    it('can not attend full event', function () {
        $event = Event::factory()->create(['is_full'=>true]);
        $res = $this->postJson("{$this->url}/attend?event_id={$event->id}");
        $res->assertStatus(400);
    });

    it('can cancel event attend', function () {
        $event = Event::factory()->create();
        $this->postJson("{$this->url}/attend?event_id={$event->id}");
        $res = $this->postJson("{$this->url}/cancel?event_id={$event->id}");
        $res->assertOk();
    });

    it('can not cancel event after specific time', function () {
        $event = Event::factory()->create();
        $this->postJson("{$this->url}/attend?event_id={$event->id}");
        $customer = $event->customers()->where('customer_id',$this->user->id)->first();
        $customer->pivot->update(['created_at'=>now()->subDays(2)]);
        $res = $this->postJson("{$this->url}/cancel?event_id={$event->id}");
        $res->assertStatus(400);
    });

});
