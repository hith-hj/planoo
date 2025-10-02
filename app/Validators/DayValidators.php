<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class DayValidators
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
            'day' => ['required', 'string', Rule::in(getWeekDays())],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
        ]);
    }

    public static function createMany($data)
    {
        return Validator::make($data, [
            'days' => ['required', 'array', 'min:1'],
            'days.*.day' => ['required', 'string', Rule::in(getWeekDays())],
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
