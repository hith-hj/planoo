<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Customer;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ReviewHandler
{
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'belongTo');
    }

    public function createReview(Customer $customer, array $data): Review
    {
        Truthy(empty($data), 'missing review data');

        return $this->reviews()->create([
            'customer_id' => $customer->id,
            'content' => $data['content'],
            'rate' => $data['rate'],
        ]);
    }

    public function updateRate(): bool
    {
        if (! array_key_exists('rate', $this->getAttributes())) {
            return false;
        }

        $reviews = $this->reviews;
        if ($reviews->isEmpty()) {
            return false;
        }

        return $this->update(['rate' => round($reviews->avg('rate') / 2, 1)]);
    }
}
