<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class UserValidators extends Validators
{
    public static function update(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1500'],
        ], self::messages());
    }

    public static function profileImage(array $data)
    {
        return Validator::make($data, [
            'profile_image' => ['required', 'image', 'mimetypes:image/jpeg,image/png', 'max:1024'],
        ], self::messages());
    }

    /**
     * Get the user validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'name.required' => __('user.name.required'),
            'name.string' => __('user.name.string'),
            'name.max' => __('user.name.max'),

            'description.required' => __('user.description.required'),
            'description.string' => __('user.description.string'),
            'description.max' => __('user.description.max'),

            'profile_image.required' => __('user.profile_image.required'),
            'profile_image.image' => __('user.profile_image.image'),
            'profile_image.mimetypes' => __('user.profile_image.mimetypes'),
            'profile_image.max' => __('user.profile_image.max'),
        ];
    }
}
