<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class EventValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
        ]);
    }

    public static function create(array $data, bool $update = false)
    {
        $validator = Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'event_duration' => ['required', 'numeric', 'min:1', 'max:30'],
            'capacity' => ['required', 'numeric', 'min:1', 'max:30'],
            'admission_fee' => ['nullable', 'numeric', 'min:1'],
            'withdrawal_fee' => ['nullable', 'numeric', 'min:1'],
            'start_date' => ['required', 'date-format:Y-m-d'],
        ]);

        $validator->sometimes(
            'event_id',
            ['required', 'exists:events,id'],
            function () use ($update) {
                return $update;
            }
        );

        return $validator;
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
        ]);
    }

    public static function attend(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id', 'required_without:customer_phone'],
            'customer_phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:customers,phone', 'required_without:customer_id'],
        ]);
    }

    public static function cancel(array $data)
    {
        return Validator::make($data, [
            'event_id' => ['required', 'exists:events,id'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
        ]);
    }
}
