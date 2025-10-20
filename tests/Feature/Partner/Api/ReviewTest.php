<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Review;
use App\Models\User;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/review';
    $this->customer = Customer::factory()->create();
    $this->activity = Activity::factory()->for($this->user,'user')->create();
});

describe('Review Controller Tests', function () {
    it('fetches all reviews', function () {
        $this->activity->reviews()->delete();
        Review::factory()->for($this->activity, 'holder')->create();
        $res = $this->getJson("$this->url/all/activity/{$this->activity->id}");
        expect($res->status())->toBe(200)
            ->and($res->json('payload.reviews'))->not->toBeNull()
            ->and($res->json('payload.reviews'))->toHaveCount(1);
    });

    it('creates a review', function () {
        $this->activity->reviews()->delete();
        $rev = Review::factory()->for($this->customer, 'customer')->make()->toArray();
        $res = $this->postJson("$this->url/create/activity/{$this->activity->id}", $rev);
        expect($res->json('payload.review'))->not->toBeNull()
            ->and($res->json('payload.review.content'))->toBe($rev['content']);
    });
});
