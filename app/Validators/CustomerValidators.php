<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class CustomerValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
        ]);
    }
}
