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
    it('fetches all reviews for activity', function () {
        $this->activity->reviews()->delete();
        Review::factory()->for($this->activity, 'holder')->create();
        $res = $this->getJson("$this->url/all/activity/{$this->activity->id}");
        expect($res->status())->toBe(200)
            ->and($res->json('payload.reviews'))->not->toBeNull()
            ->and($res->json('payload.reviews'))->toHaveCount(1);
    });
});
