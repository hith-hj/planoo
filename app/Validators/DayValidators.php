<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\WeekDays;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidationResult;

final class DayValidators extends Validators
{
    public static function find(array $data): ValidationResult
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
        ], self::messages());
    }

    public static function create(array $data): ValidationResult
    {
        return Validator::make($data, [
            'day' => ['required', 'string', Rule::in(WeekDays::names())],
            'start' => ['required', 'regex:/^([01]\d|2[0-3]):(00|30)$/'],
            'end' => ['required', 'regex:/^([01]\d|2[0-3]):(00|30)$/'],
        ], self::messages());
    }

    public static function createMany(array $data): ValidationResult
    {
        return Validator::make($data, [
            'days' => ['required', 'array', 'min:1'],
            'days.*.day' => ['required', 'string', Rule::in(WeekDays::names())],
            'days.*.start' => ['required', 'date_format:H:i'],
            'days.*.end' => ['required', 'date_format:H:i'],
        ], self::messages());
    }

    public static function update(array $data): ValidationResult
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
        ], self::messages());
    }

    public static function updateMany(array $data): ValidationResult
    {
        // Accept the H:i:s values returned by SQL-backed day resources.
        if (isset($data['days']) && is_array($data['days'])) {
            foreach ($data['days'] as &$day) {
                if (! is_array($day)) {
                    continue;
                }
                foreach (['start', 'end'] as $field) {
                    if (isset($day[$field]) && is_string($day[$field])
                        && preg_match('/^([01]\d|2[0-3]):[0-5]\d:00$/', $day[$field])) {
                        $day[$field] = mb_substr($day[$field], 0, 5);
                    }
                }
            }
            unset($day);
        }

        return Validator::make($data, [
            'days' => ['required', 'array', 'min:1', 'max:7'],
            'replace_days' => ['sometimes', 'boolean'],
            'days.*' => ['required', 'array:id,day,start,end,is_active'],
            'days.*.id' => ['sometimes', 'integer', 'distinct'],
            'days.*.day' => ['required', 'string', Rule::in(WeekDays::names()), 'distinct'],
            'days.*.start' => ['required', 'date_format:H:i'],
            'days.*.end' => ['required', 'date_format:H:i', 'different:days.*.start'],
            'days.*.is_active' => ['sometimes', 'boolean'],
        ], self::messages());
    }

    public static function delete(array $data): ValidationResult
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
        ], self::messages());
    }

    /**
     * Get the day validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'day_id.required' => __('day.day_id.required'),
            'day_id.exists' => __('day.day_id.exists'),

            'day.required' => __('day.day.required'),
            'day.string' => __('day.day.string'),
            'day.in' => __('day.day.in'),

            'start.required' => __('day.start.required'),
            'start.regex' => __('day.start.regex'),
            'start.date_format' => __('day.start.date_format'),

            'end.required' => __('day.end.required'),
            'end.regex' => __('day.end.regex'),
            'end.date_format' => __('day.end.date_format'),

            'days.required' => __('day.days.required'),
            'days.array' => __('day.days.array'),
            'days.min' => __('day.days.min'),

            'days.*.day.required' => __('day.days.*.day.required'),
            'days.*.day.string' => __('day.days.*.day.string'),
            'days.*.day.in' => __('day.days.*.day.in'),

            'days.*.start.required' => __('day.days.*.start.required'),
            'days.*.start.date_format' => __('day.days.*.start.date_format'),

            'days.*.end.required' => __('day.days.*.end.required'),
            'days.*.end.date_format' => __('day.days.*.end.date_format'),
        ];
    }
}
