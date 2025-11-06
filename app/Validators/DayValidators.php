<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\WeekDays;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class DayValidators extends Validators
{
    public static function find($data)
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
        ]);
    }

    public static function create($data)
    {
        return Validator::make($data, [
            'day' => ['required', 'string', Rule::in(WeekDays::names())],
            'start' => ['required', 'regex:/^([01]\d|2[0-3]):(00|30)$/'],
            'end' => ['required', 'regex:/^([01]\d|2[0-3]):(00|30)$/'],
        ]);
    }

    public static function createMany($data)
    {
        return Validator::make($data, [
            'days' => ['required', 'array', 'min:1'],
            'days.*.day' => ['required', 'string', Rule::in(WeekDays::names())],
            'days.*.start' => ['required', 'date_format:H:i'],
            'days.*.end' => ['required', 'date_format:H:i'],
        ]);
    }

    public static function update($data)
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
        ]);
    }

    public static function delete($data)
    {
        return Validator::make($data, [
            'day_id' => ['required', 'exists:days,id'],
        ]);
    }
}
