<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\CourseDuration;
use App\Enums\SessionDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

final class CourseValidators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'course_id' => ['required', 'exists:courses,id'],
        ]);
    }

    public static function create(array $data, bool $update = false)
    {
        $validator = Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'price' => ['required', 'numeric', 'min:1'],
            'session_duration' => ['required', 'numeric', new Enum(SessionDuration::class)],
            'course_duration' => ['required', 'numeric', new Enum(CourseDuration::class)],
            'capacity' => ['required', 'numeric', 'min:1', 'max:30'],
            'cancellation_fee' => ['nullable', 'numeric', 'min:1'],
        ]);

        $validator->sometimes(
            'course_id',
            ['required', 'exists:courses,id'],
            function () use ($update) {
                return $update;
            }
        );

        return $validator;
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'course_id' => ['required', 'exists:courses,id'],
        ]);
    }

    public static function attend(array $data)
    {
        return Validator::make($data,[
            'course_id' => ['required', 'exists:courses,id'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id', 'required_without:customer_phone'],
            'customer_phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:customers,phone', 'required_without:customer_id'],
        ]);
    }

    public static function cancel(array $data)
    {
        return Validator::make($data,[
            'course_id' => ['required', 'exists:courses,id'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
        ]);
    }
}
