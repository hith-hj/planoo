<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\CourseDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class CourseValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'course_id' => ['required', 'exists:courses,id'],
        ], self::messages());
    }

    public static function create(array $data, bool $update = false)
    {
        $maxCapacity = Setting('course_capacity', 30);

        return Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'court_id' => ['required', 'exists:courts,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'price' => ['required', 'numeric', 'min:1'],
            'course_duration' => ['required', 'numeric', new Enum(CourseDuration::class)],
            'capacity' => ['required', 'numeric', 'min:1', "max:{$maxCapacity}"],
            'cancellation_fee' => ['nullable', 'numeric', 'min:1'],
            'start_date' => ['required', Rule::when($update === false, [Rule::date()->afterToday()]), 'date-format:Y-m-d'],
            'course_id' => [Rule::when($update, ['required', 'exists:courses,id'])],
        ], self::messages());
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'course_id' => ['required', 'exists:courses,id'],
        ], self::messages());
    }

    public static function attend(array $data)
    {
        return Validator::make($data, [
            'course_id' => ['required', 'exists:courses,id'],
            'customer_id' => [
                'sometimes',
                'required',
                'exists:customers,id',
                'required_without:customer_phone',
            ],
            'customer_phone' => [
                'sometimes',
                'regex:/^09[1-9]{1}\d{7}$/',
                'unique:customers,phone',
                'required_without:customer_id',
            ],
        ], self::messages());
    }

    public static function cancel(array $data)
    {
        return Validator::make($data, [
            'course_id' => ['required', 'exists:courses,id'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
        ], self::messages());
    }

    /**
     * Get the course validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'course_id.required' => __('course.course_id.required'),
            'course_id.exists' => __('course.course_id.exists'),

            'category_id.required' => __('course.category_id.required'),
            'category_id.exists' => __('course.category_id.exists'),

            'name.required' => __('course.name.required'),

            'description.required' => __('course.description.required'),
            'description.max' => __('course.description.max'),

            'price.required' => __('course.price.required'),
            'price.numeric' => __('course.price.numeric'),
            'price.min' => __('course.price.min'),

            'course_duration.required' => __('course.course_duration.required'),

            'capacity.required' => __('course.capacity.required'),
            'capacity.numeric' => __('course.capacity.numeric'),
            'capacity.min' => __('course.capacity.min'),
            'capacity.max' => __('course.capacity.max'),

            'cancellation_fee.numeric' => __('course.cancellation_fee.numeric'),
            'cancellation_fee.min' => __('course.cancellation_fee.min'),

            'start_date.required' => __('course.start_date.required'),
            'start_date.date_format' => __('course.start_date.date_format'),

            'customer_id.required' => __('course.customer_id.required'),
            'customer_id.exists' => __('course.customer_id.exists'),
            'customer_id.required_without' => __('course.customer_id.required_without'),

            'customer_phone.regex' => __('course.customer_phone.regex'),
            'customer_phone.unique' => __('course.customer_phone.unique'),
            'customer_phone.required_without' => __('course.customer_phone.required_without'),
        ];
    }
}
