<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Favorite;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->customer = Customer::factory()->create();
    $this->activity = Activity::factory()->create();
});

describe('Favorite Controller Tests', function () {
    it('fetches all favorites for customer', function () {
        Favorite::factory(4)->for($this->user, 'customer')->create();
        $res = $this->getJson(route('customer.favorite.all'));
        $res->assertOk();
        expect($res->json('payload.favorites'))->not->toBeNull()
            ->and($res->json('payload.favorites.0.customer_id'))->toBe($this->user->id)
            ->and($res->json('payload.favorites'))->toHaveCount(4);
    });

    it('cant fetches favorites for customer when empty', function () {
        $this->getJson(route('customer.favorite.all'))->assertStatus(404);
    });

    it('find single favorite for customer', function () {
        $fav = Favorite::factory()->for($this->user, 'customer')->create();
        $res = $this->getJson(route('customer.favorite.find', ['favorite_id' => $fav->id]));
        $res->assertOk();
        expect($res->json('payload.favorite'))->not->toBeNull()
            ->and($res->json('payload.favorite.customer_id'))->toBe($this->user->id)
            ->and($res->json('payload'))->toHaveCount(1);
    });

    it('cant find single favorite for customer with invalid id', function () {
        $res = $this->getJson(route('customer.favorite.find', ['favorite_id' => 1231]))->assertStatus(422);
    });

    it('creates a favorite', function () {
        $this->user->favorites()->delete();
        $res = $this->postJson(route('customer.favorite.create', [
            'owner_type' => 'activity',
            'owner_id' => $this->activity->id
        ]));
        $res->assertOk();
        expect($res->json('payload.favorite'))->not->toBeNull()
            ->and($res->json('payload.favorite.customer_id'))->toBe($this->user->id);
    });

    it('cant creates a duplicate favorite', function () {
        $fav = Favorite::factory()->for($this->user, 'customer')
            ->for($this->activity, 'holder')
            ->create();
        $res = $this->postJson(route('customer.favorite.create', [
            'owner_type' => 'activity',
            'owner_id' => $this->activity->id
        ]));
        $res->assertStatus(400);
    });

    it('cant creates a favorite with invalid owner type', function () {
        $this->postJson(
            route('customer.favorite.create', [
                'owner_type' => 'invalid_activity',
                'owner_id' => $this->activity->id
            ])
        )->assertStatus(400);
    });

    it('delete a favorite', function () {
        $fav = Favorite::factory()->for($this->user, 'customer')->create();
        $res = $this->deleteJson(route('customer.favorite.delete'), ['favorite_id' => $fav->id]);
        $res->assertOk();
        expect($this->user->favorites()->count())->toBe(0);
    });

    it('cant delete a favorite with invalid id', function () {
        $res = $this->deleteJson(route('customer.favorite.delete'), ['favorite_id' => 'invalid_id']);
        $res->assertStatus(422);
    });
});
