<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewServices;
use App\Support\Pagination;
use App\Validators\ReviewValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ReviewController extends Controller
{
    public function __construct(public ReviewServices $service) {}

    public function all(Request $request): JsonResponse
    {
        [$page, $perPage] = Pagination::fromRequest($request);
        $reviews = $this->service->all(getModelGlobal(), $page, $perPage);

        return Success(payload: ['reviews' => $reviews->toResourceCollection(), ...($perPage === null ? [] : compact('page', 'perPage'))]);
    }

    public function create(Request $request)
    {
        $validator = ReviewValidators::create($request->all());

        $review = $this->service->create(getModelGlobal(), Auth::user(), $validator->safe()->all());

        return Success(
            msg: 'review created',
            payload: ['review' => $review->toResource()]
        );
    }

    public function update(Request $request)
    {
        $validator = ReviewValidators::create($request->all());

        $review = $this->service->find($validator->safe()->integer('review_id'));
        $this->service->update(getModelGlobal(), $review, $validator->safe()->except('review_id'));

        return Success(
            msg: 'review updated',
            payload: ['review' => $review->toResource()]
        );
    }
}
