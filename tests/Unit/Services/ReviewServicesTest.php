<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Customer;
use App\Services\ReviewServices;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->seed();
    $this->services = new ReviewServices();
    $this->customer = Customer::factory()->create();
});

describe('Review Services Class', function () {

    it('get all reviews for customer', function () {
        Review::factory(2)->for($this->customer, 'holder')->create();
        expect($this->customer->reviews)->toHaveCount(2);
        expect($this->services->all($this->customer))
            ->toBeInstanceOf(Collection::class)
            ->toHaveCount(2);
    });

    it('fail to get all reviews for customer when not found', function () {
        expect(fn () => $this->services->all($this->customer))->toThrow(NotFoundHttpException::class);
    });

    it('fails to retive reviews with invalid object', function () {
        expect(fn () => $this->services->all((object) []))->toThrow(\Exception::class);
    });

    it('can create review for customer', function () {
        $data = Review::factory()->for($this->customer, 'customer')->make()->toArray();
        $activity = Activity::factory()->create();
        $res = $this->services->create($activity,$this->customer, $data);
        expect($res)->toBeInstanceOf(Review::class)
            ->and($res->rate)->toBe($data['rate'])
            ->and($res->content)->toBe($data['content']);
    });

    it('fails to create review for with invalid data', function () {
        $activity = Activity::factory()->create();
        $this->services->create($activity,$this->customer, []);
    })->throws(\Exception::class);

    it('fails to create review for with invalid owner object', function () {
        $data = Review::factory()->make()->toArray();
        $this->services->create((object)[], $this->customer, $data);
    })->throws(\Exception::class);
});
