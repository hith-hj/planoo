<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\SessionDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class ActivityValidators extends Validators
{
    public static function find(array $data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
        ], self::messages());
    }

    public static function create(array $data, bool $update = false)
    {
        return Validator::make($data, [
            'category_id' => ['required', 'exists:categories,id'],
            'court_id' => ['required', 'exists:courts,id'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:1500'],
            'price' => ['required', 'numeric', 'min:1'],
            'session_duration' => ['required', 'numeric', new Enum(SessionDuration::class)],
            'activity_id' => [Rule::when($update, ['required', 'exists:activities,id'])],
        ], self::messages());
    }

    public static function delete(array $data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
        ], self::messages());
    }

    /**
     * Get the activity validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'activity_id.required' => __('activity.activity_id.required'),
            'activity_id.exists' => __('activity.activity_id.exists'),
            'category_id.required' => __('activity.category_id.required'),
            'category_id.exists' => __('activity.category_id.exists'),
            'name.required' => __('activity.name.required'),
            'description.required' => __('activity.description.required'),
            'description.max' => __('activity.description.max'),
            'price.required' => __('activity.price.required'),
            'price.numeric' => __('activity.price.numeric'),
            'price.min' => __('activity.price.min'),
            'session_duration.required' => __('activity.session_duration.required'),
        ];
    }
}
