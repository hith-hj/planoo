<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class FavoriteValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'favorite_id' => ['required', 'numeric', 'exists:favorites,id'],
        ]);
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'favorite_id' => ['required', 'numeric', 'exists:favorites,id'],
        ]);
    }
}
