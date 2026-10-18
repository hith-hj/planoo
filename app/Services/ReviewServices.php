<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\Reviewable;
use App\Models\Customer;
use App\Models\Review;
use App\Support\Pagination;
use Illuminate\Support\Collection;

final class ReviewServices
{
    public function allByCustomer(Customer $customer, int $page = 1, ?int $perPage = null): Collection
    {
        $query = $customer->reviews()->orderByDesc('created_at')->orderByDesc('id');
        if ($perPage !== null) {
            Pagination::validate($page, $perPage);

            return $query->forPage($page, $perPage)->get();
        }
        $reviews = Pagination::bounded($query);
        NotFound($reviews, 'reviews');

        return $reviews;
    }

    public function all(Reviewable $reviewable, int $page = 1, ?int $perPage = null): Collection
    {
        $query = $reviewable->reviews()->with('customer.medias')->orderByDesc('created_at')->orderByDesc('id');
        if ($perPage !== null) {
            Pagination::validate($page, $perPage);

            return $query->forPage($page, $perPage)->get();
        }
        $reviews = Pagination::bounded($query);
        NotFound($reviews, 'reviews');

        return $reviews;
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
            'content' => 'string|null',
        ]);

        $review = $reviewable->reviews()->where('customer_id', $customer->id)->first();
        if ($review) {
            if (abs(now()->diffInSeconds($review->created_at) / 3600) > 24) {
                return $this->update($reviewable, $review, $data);
            }
            Truthy(true, 'reviews editing allowed after 24 hours');

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
