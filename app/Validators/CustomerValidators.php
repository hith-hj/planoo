<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class CustomerValidators extends Validators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'customer_id' => ['required', 'exists:customers,id'],
        ]);
    }

    public static function update(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
        ]);
    }

    public static function profileImage(array $data)
    {
        return Validator::make($data, [
            'profile_image' => ['required', 'image', 'mimetypes:image/jpeg,image/png,', 'max:1024'],
        ]);
    }
}
