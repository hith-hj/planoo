<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\SessionDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class ActivityValidators extends Validators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
        ]);
    }

    public static function create(array $data, bool $update = false)
    {
        return Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'price' => ['required', 'numeric', 'min:1'],
            'session_duration' => ['required', 'numeric', new Enum(SessionDuration::class)],
            'activity_id' => [Rule::when($update, ['required', 'exists:activities,id'])],
        ]);
    }

    public static function delete($data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
        ]);
    }
}
