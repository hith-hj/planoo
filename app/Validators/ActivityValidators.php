<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\SessionDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

final class ActivityValidators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
        ]);
    }

    public static function create(array $data, bool $update = false)
    {
        $validator = Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'price' => ['required', 'numeric', 'min:1'],
            'session_duration' => ['required', 'numeric', new Enum(SessionDuration::class)],
        ]);

        $validator->sometimes(
            'activity_id',
            ['required', 'exists:activities,id'],
            function () use ($update) {
                return $update;
            }
        );

        return $validator;
    }

    public static function delete($data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
        ]);
    }
}
