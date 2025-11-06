<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class ReviewValidators extends Validators
{
    public static function create($data)
    {
        return Validator::make($data, [
            'content' => ['nullable', 'string', 'max:700'],
            'rate' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);
    }

    public static function createFromUser($data)
    {
        return Validator::make($data, [
            'customer_id' => ['required', 'exists:customers,id'],
            'content' => ['nullable', 'string', 'max:700'],
            'rate' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);
    }
}
