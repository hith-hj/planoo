<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\SessionDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class AppointmentValidators
{
    public static function check(array $data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
            'day_id' => ['required', 'exists:days,id'],
            'date' => ['required', Rule::date()->afterToday()],
            'session_duration' => ['required', new Enum(SessionDuration::class)],
        ]);
    }

    public static function create(array $data)
    {
        $validators = Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
            'day_id' => ['required', 'exists:days,id'],
            'date' => ['required', Rule::date()->afterToday()],
            'session_duration' => ['required', new Enum(SessionDuration::class)],
            'time' => ['required', 'regex:/^([01]\d|2[0-3]):(00|30)$/'],
            'notes' => ['nullable', 'string', 'max:500'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id', 'required_without:customer_phone'],
            'customer_phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:customers,phone', 'required_without:customer_id'],
        ]);

        return $validators;
    }

    public static function find(array $data)
    {
        return Validator::make($data, [
            'appointment_id' => ['required', 'exists:appointments,id'],
        ]);
    }
}
