<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Review;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->customer = Customer::factory()->create();
    $this->replaceUser($this->customer);
    $this->activity = Activity::factory()->create();
});

describe('Review Controller Tests', function () {
    it('fetches all reviews for activity', function () {
        $this->activity->reviews()->delete();
        Review::factory()->for($this->activity, 'holder')->create();
        $res = $this->getJson(route('customer.review.all', ['owner_type' => 'activity', 'owner_id' => $this->activity->id]));
        $res->assertOk();
        expect($res->json('payload.reviews'))->not->toBeNull()
            ->and($res->json('payload.reviews'))->toHaveCount(1);
    });

    it('creates a review', function () {
        $this->activity->reviews()->delete();
        $rev = Review::factory()->for($this->customer, 'customer')->make()->toArray();
        $res = $this->postJson(route('customer.review.create', ['owner_type' => 'activity', 'owner_id' => $this->activity->id]), $rev);
        $res->assertOk();
        expect($res->json('payload.review'))->not->toBeNull()
            ->and($res->json('payload.review.content'))->toBe($rev['content']);
    });

    it('creates a review without content', function () {
        $this->activity->reviews()->delete();
        $rev = Review::factory()->for($this->customer, 'customer')->make()->toArray();
        $rev['content'] = null;
        $res = $this->postJson(route('customer.review.create', ['owner_type' => 'activity', 'owner_id' => $this->activity->id]), $rev);
        $res->assertOk();
        expect($res->json('payload.review'))->not->toBeNull()
            ->and($res->json('payload.review.content'))->toBeNull();
    });

    it('cant update a review for same activity before 24 hour has passed', function () {
        $this->activity->reviews()->delete();
        $rev = Review::factory()->for($this->customer, 'customer')->make()->toArray();
        $this->postJson(route(
            'customer.review.create',
            ['owner_type' => 'activity', 'owner_id' => $this->activity->id]
        ), $rev)->assertOk();
        $res = $this->postJson(route(
            'customer.review.create',
            ['owner_type' => 'activity', 'owner_id' => $this->activity->id]
        ), $rev);
        $res->assertStatus(400);
    });

    it('can update review for same activity after 24 hour has passed', function () {
        $this->activity->reviews()->delete();
        $rev = Review::factory()->for($this->customer, 'customer')->make()->toArray();
        $res = $this->postJson(
            route(
                'customer.review.create',
                ['owner_type' => 'activity', 'owner_id' => $this->activity->id]
            ),
            $rev
        )->assertOk();
        Review::find($res->json('payload.review.id'))->forceFill(['created_at' => now()->subDays(3)])->save();
        $rev['content'] = 'horayyyy';
        $res = $this->postJson(
            route(
                'customer.review.create',
                ['owner_type' => 'activity', 'owner_id' => $this->activity->id]
            ),
            $rev
        );
        $res->assertOk();
        expect($this->activity->reviews()->count())->toBe(1)
            ->and($res->json('payload.review.content'))->toBe('horayyyy');
    });
});
