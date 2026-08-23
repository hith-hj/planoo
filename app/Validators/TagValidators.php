<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationResult;

final class TagValidators extends Validators
{
    public static function create(array $data, bool $update = false): ValidationResult
    {
        return Validator::make($data, [
            'tags' => ['nullable', 'array', 'min:1'],
            'tags.*' => ['required', 'exists:tags,id'],
        ]);
    }

    public static function delete(array $data): ValidationResult
    {
        return Validator::make($data, [
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['required', 'exists:tags,id'],
        ]);
    }
}
