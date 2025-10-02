<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Collection;

final class ReviewServices
{
    public function all(object $object): Collection
    {
        Truthy(! method_exists($object, 'reviews'), 'missing reviews()');
        $reviews = $object->reviews;
        NotFound($reviews, 'reviews');

        return $reviews->load(['reviewer'])->sortByDesc('created_at');
    }

    public function create(object $reviewer, array $data): Review
    {
        Required($reviewer, 'reviewer');
        Required($data, 'data');
        $this->checkAndCastData($data, [
            'belongTo_id' => 'int',
            'belongTo_type' => 'string',
            'rate' => 'int',
            'content' => 'string',
        ]);

        $model = $this->getPreparedModel($data);
        Truthy($model::class === $reviewer::class, 'You cant review this');

        $query = Review::where([
            ['belongTo_id', $model->id],
            ['belongTo_type', $model::class],
            ['reviewer_id', $reviewer->id],
            ['reviewer_type', $reviewer::class],
        ]);
        Truthy(
            ($query->exists() && date_diff(now(), $query->first()->created_at)->d < 1),
            'reviews not allowed until 24 hours is passed',
        );

        $review = $model->createReview($reviewer, $data);
        $model->updateRate();

        return $review;
    }

    private function getPreparedModel(array $data)
    {
        $id = $data['belongTo_id'];
        $class = $data['belongTo_type'];
        if (! str_contains($class, 'App\\Models')) {
            $class = 'App\\Models\\'.ucfirst($class);
        }
        Truthy(! class_exists($class), 'invalid class type');
        $model = $class::find($id);
        NotFound($model, "$class id $id");
        Truthy(! method_exists($model, 'reviews'), 'model missing reviews()');

        return $model;
    }

    private function checkAndCastData(array $data = [], $requiredFields = []): array
    {
        Truthy(empty($data), 'data is empty');
        if (empty($requiredFields)) {
            return $data;
        }
        $missing = array_diff(array_keys($requiredFields), array_keys($data));
        Falsy(empty($missing), 'fields missing: '.implode(', ', $missing));
        foreach ($requiredFields as $key => $value) {
            settype($data[$key], $value);
        }

        return $data;
    }
}
