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
        ]);
    }

    public static function profileImage(array $data)
    {
        return Validator::make($data, [
            'profile_image' => ['required', 'image', 'mimetypes:image/jpeg,image/png,', 'max:1024'],
        ]);
    }
}
