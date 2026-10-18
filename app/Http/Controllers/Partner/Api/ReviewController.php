<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewServices;
use App\Support\Pagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReviewController extends Controller
{
    public function __construct(public ReviewServices $review) {}

    public function all(Request $request): JsonResponse
    {
        [$page, $perPage] = Pagination::fromRequest($request);
        $reviews = $this->review->all(getModel(), $page, $perPage);

        return Success(payload: ['reviews' => $reviews->toResourceCollection(), ...($perPage === null ? [] : compact('page', 'perPage'))]);
    }

    // Note: partners cannot create reviews; only customers review sections.
    // See App\Http\Controllers\Customer\Api\ReviewController::create.
}
