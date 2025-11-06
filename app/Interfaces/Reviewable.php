<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Customer;

interface Reviewable
{
    public function reviews();

    public function createReview(Customer $customer, array $data);

    public function updateRate();
}
