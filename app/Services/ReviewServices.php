<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Reviewable;
use App\Models\Customer;
use App\Models\Review;
use Illuminate\Support\Collection;

final class ReviewServices
{
    public function allByCustomer(Customer $customer): Collection
    {
        $reviews = $customer->reviews;
        NotFound($reviews, 'reviews');

        return $reviews->sortByDesc('created_at');
    }

    public function all(Reviewable $reviewable): Collection
    {
        $reviews = $reviewable->reviews;
        NotFound($reviews, 'reviews');

        return $reviews->load(['customer'])->sortByDesc('created_at');
    }

    public function create(Reviewable $reviewable, Customer $customer, array $data): Review
    {
        Required($customer, 'customer');
        Required($data, 'data');
        checkAndCastData($data, [
            'rate' => 'int',
            'content' => 'string',
        ]);

        $query = $reviewable->reviews()->where('customer_id', $customer->id);
        Truthy(
            ($query->exists() && date_diff(now(), $query->first()->created_at)->d < 1),
            'reviews not allowed until 24 hours is passed',
        );
        $review = $reviewable->createReview($customer, $data);
        $reviewable->updateRate();

        return $review;
    }
}
