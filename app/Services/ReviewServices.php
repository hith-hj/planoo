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

    public function find(int $id)
    {
        $review = Review::find($id);
        NotFound($review, 'review');

        return $review;
    }

    public function create(Reviewable $reviewable, Customer $customer, array $data): Review
    {
        Required($customer, 'customer');
        Required($data, 'data');
        checkAndCastData($data, [
            'rate' => 'int',
            'content' => 'string',
        ]);

        $review = $reviewable->reviews()->where('customer_id', $customer->id)->first();
        if ($review) {
            if (abs(now()->diffInSeconds($review->created_at) / 3600) > 24) {
                return $this->update($reviewable, $review, $data);
            }
            Truthy(true, 'reviews editing allowed after 24 hours ');

        }
        $review = $reviewable->createReview($customer, $data);
        $reviewable->updateRate();

        return $review;
    }

    public function update(Reviewable $reviewable, Review $review, array $data): Review
    {
        Truthy(empty($data), 'missing review data');

        $review->update([
            'content' => $data['content'],
            'rate' => $data['rate'],
        ]);

        $reviewable->updateRate();

        return $review;
    }
}
