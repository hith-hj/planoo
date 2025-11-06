<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewServices;
use App\Validators\ReviewValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ReviewController extends Controller
{
    public function __construct(public ReviewServices $service) {}

    public function all(): JsonResponse
    {
        $reviews = $this->service->all(getModel());

        return Success(payload: ['reviews' => $reviews->toResourceCollection()]);
    }

    public function create(Request $request)
    {
        $validator = ReviewValidators::create($request->all());

        $review = $this->service->create(getModel(), Auth::user(), $validator->safe()->all());

        return Success(
            msg: 'review created',
            payload: ['review' => $review->toResource()]
        );
    }
}
