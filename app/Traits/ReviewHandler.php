<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Customer;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ReviewHandler
{
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function createReview(Customer $customer, array $data): Review
    {
        throw_if(empty($data), 'missing review data');

        return $this->reviews()->create([
            'customer_id' => $customer->id,
            'content' => $data['content'],
            'rate' => $data['rate'],
        ]);
    }

    public function updateRate(): bool
    {
        if (! isset($this->rate)) {
            return false;
        }
        $sum = $this->reviews()->sum('rate');
        $count = $this->reviews()->count();
        $rate = round(($sum / $count) / 2, 1);

        return $this->update(['rate' => $rate]);
    }
}
