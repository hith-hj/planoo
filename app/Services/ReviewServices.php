<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Review;
use Illuminate\Support\Collection;

final class ReviewServices
{
    public function all(object $reviewable): Collection
    {
        Truthy(! method_exists($reviewable, 'reviews'), 'missing reviews()');
        $reviews = $reviewable->reviews;
        NotFound($reviews, 'reviews');

        return $reviews->load(['customer'])->sortByDesc('created_at');
    }

    public function create(object $owner, Customer $customer, array $data): Review
    {
        Required($owner, 'owner');
        Required($customer, 'customer');
        Required($data, 'data');
        checkAndCastData($data, [
            'rate' => 'int',
            'content' => 'string',
        ]);

        Truthy(! method_exists($owner, 'reviews'), 'missing reviews() method');

        $query = Review::where([
            ['belongTo_id', $owner->id],
            ['belongTo_type', $owner::class],
            ['customer_id', $customer->id],
        ]);
        Truthy(
            ($query->exists() && date_diff(now(), $query->first()->created_at)->d < 1),
            'reviews not allowed until 24 hours is passed',
        );
        $review = $owner->createReview($customer, $data);
        $owner->updateRate();

        return $review;
    }
}
