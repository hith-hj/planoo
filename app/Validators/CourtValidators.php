<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class CourtValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'court_id' => ['required', 'exists:courts,id'],
        ]);
    }

    public static function create(array $data, bool $update = false)
    {
        return Validator::make($data, [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:1500'],
            'court_id' => [Rule::when($update, ['required', 'exists:courts,id'])],
        ]);
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'court_id' => ['required', 'exists:courts,id'],
        ]);
    }
}
