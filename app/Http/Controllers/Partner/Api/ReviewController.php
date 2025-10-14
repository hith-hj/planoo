<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerServices;
use App\Services\ReviewServices;
use App\Validators\ReviewValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReviewController extends Controller
{
    public function __construct(public ReviewServices $review) {}

    public function all(): JsonResponse
    {
        $reviews = $this->review->all(getModel());

        return Success(payload: ['reviews' => $reviews->toResourceCollection()]);
    }

    public function create(Request $request)
    {
        $validator = ReviewValidators::createFromUser($request->all());

        $customer = app(CustomerServices::class)->find(1); // should be removed
        $review = $this->review->create(getModel(), $customer, $validator->safe()->all());

        return Success(
            msg: 'review created',
            payload: ['review' => $review->toResource()]
        );
    }
}
