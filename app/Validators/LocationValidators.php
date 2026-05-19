<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class LocationValidators extends Validators
{
    public static function create(array $data, bool $update = false)
    {
        return Validator::make($data, [
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
            'name' => ['nullable', 'string', 'max:100'],
            'location_id' => [Rule::when($update, ['required', 'exists:locations,id'])],

        ]);
    }
}
