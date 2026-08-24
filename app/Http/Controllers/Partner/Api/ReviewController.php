<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewServices;
use Illuminate\Http\JsonResponse;

final class ReviewController extends Controller
{
    public function __construct(public ReviewServices $review) {}

    public function all(): JsonResponse
    {
        $reviews = $this->review->all(getModel());

        return Success(payload: ['reviews' => $reviews->toResourceCollection()]);
    }

    // Note: partners cannot create reviews; only customers review sections.
    // See App\Http\Controllers\Customer\Api\ReviewController::create.
}
