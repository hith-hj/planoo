<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationResult;

final class FavoriteValidators extends Validators
{
    public static function find(array $data): ValidationResult
    {
        return Validator::make($data, [
            'favorite_id' => ['required', 'numeric', 'exists:favorites,id'],
        ]);
    }

    public static function delete(array $data): ValidationResult
    {
        return Validator::make($data, [
            'favorite_id' => ['required', 'numeric', 'exists:favorites,id'],
        ]);
    }
}
