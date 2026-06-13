<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class EventValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
        ], self::messages());
    }

    public static function create(array $data, bool $update = false)
    {
        $maxDuration = Setting('event_duration', 30);
        $maxCapacity = Setting('event_capacity', 30);

        return Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'court_id' => ['required', 'exists:courts,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'event_duration' => ['required', 'numeric', 'min:1', "max:{$maxDuration}"],
            'capacity' => ['required', 'numeric', 'min:1',  "max:{$maxCapacity}"],
            'admission_fee' => ['nullable', 'numeric', 'min:1'],
            'withdrawal_fee' => ['nullable', 'numeric', 'min:1'],
            'start_date' => ['required', Rule::when($update === false, [Rule::date()->afterToday()]), 'date-format:Y-m-d'],
            'event_id' => [Rule::when($update, ['required', 'exists:events,id'])],
        ], self::messages());
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
        ], self::messages());
    }

    public static function attend(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
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
            'event_id' => ['required', 'exists:events,id'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
        ], self::messages());
    }

    /**
     * Get the event validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'event_id.required' => __('event.event_id.required'),
            'event_id.exists' => __('event.event_id.exists'),

            'category_id.required' => __('event.category_id.required'),
            'category_id.exists' => __('event.category_id.exists'),

            'name.required' => __('event.name.required'),
            'name.string' => __('event.name.string'),

            'description.required' => __('event.description.required'),
            'description.string' => __('event.description.string'),
            'description.max' => __('event.description.max'),

            'event_duration.required' => __('event.event_duration.required'),
            'event_duration.numeric' => __('event.event_duration.numeric'),
            'event_duration.min' => __('event.event_duration.min'),
            'event_duration.max' => __('event.event_duration.max'),

            'capacity.required' => __('event.capacity.required'),
            'capacity.numeric' => __('event.capacity.numeric'),
            'capacity.min' => __('event.capacity.min'),
            'capacity.max' => __('event.capacity.max'),

            'admission_fee.numeric' => __('event.admission_fee.numeric'),
            'admission_fee.min' => __('event.admission_fee.min'),

            'withdrawal_fee.numeric' => __('event.withdrawal_fee.numeric'),
            'withdrawal_fee.min' => __('event.withdrawal_fee.min'),

            'start_date.required' => __('event.start_date.required'),
            'start_date.date_format' => __('event.start_date.date_format'),

            'customer_id.required' => __('event.customer_id.required'),
            'customer_id.exists' => __('event.customer_id.exists'),
            'customer_id.required_without' => __('event.customer_id.required_without'),

            'customer_phone.regex' => __('event.customer_phone.regex'),
            'customer_phone.unique' => __('event.customer_phone.unique'),
            'customer_phone.required_without' => __('event.customer_phone.required_without'),
        ];
    }
}
