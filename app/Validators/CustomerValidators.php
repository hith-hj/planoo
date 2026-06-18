<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class CustomerValidators extends Validators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'customer_id' => ['required', 'exists:customers,id'],
        ], self::messages());
    }

    public static function update(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
        ], self::messages());
    }

    public static function profileImage(array $data, bool $optional = false)
    {
        return Validator::make($data, [
            'profile_image' => [
                Rule::when($optional === false, ['required']),
                'image',
                'mimetypes:image/jpeg,image/png',
                'max:1024',
            ],
        ], self::messages());
    }

    /**
     * Get the customer validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'customer_id.required' => __('customer.customer_id.required'),
            'customer_id.exists' => __('customer.customer_id.exists'),

            'name.required' => __('customer.name.required'),
            'name.string' => __('customer.name.string'),
            'name.max' => __('customer.name.max'),

            'profile_image.required' => __('customer.profile_image.required'),
            'profile_image.image' => __('customer.profile_image.image'),
            'profile_image.mimetypes' => __('customer.profile_image.mimetypes'),
            'profile_image.max' => __('customer.profile_image.max'),
        ];
    }
}
