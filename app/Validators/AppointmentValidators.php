<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\SessionDuration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class AppointmentValidators extends Validators
{
    public static function check(array $data)
    {
        return Validator::make($data, [
            'activity_id' => ['required', 'exists:activities,id'],
            'day_id' => ['required', 'exists:days,id'],
            'date' => ['required', Rule::date()->afterToday()],
            'session_duration' => ['required', new Enum(SessionDuration::class)],
        ], self::messages());
    }

    public static function create(array $data)
    {
        return Validator::make($data, [
            'code' => ['required'],
            'activity_id' => ['required', 'exists:activities,id'],
            'day_id' => ['required', 'exists:days,id'],
            'date' => ['required', Rule::date()->afterToday()],
            'session_duration' => ['required', new Enum(SessionDuration::class)],
            'time' => ['required', 'regex:/^([01]\d|2[0-3]):(00|30)$/'],
            'notes' => ['nullable', 'string', 'max:500'],
            'customer_id' => ['sometimes', 'required', 'exists:customers,id', 'required_without:customer_phone'],
            'customer_phone' => ['sometimes', 'regex:/^09[1-9]{1}\d{7}$/', 'required_without:customer_id'],
        ], self::messages());
    }

    public static function find(array $data)
    {
        return Validator::make($data, [
            'appointment_id' => ['required', 'exists:appointments,id'],
        ], self::messages());
    }

    /**
     * Get the appointment validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'activity_id.required' => __('appointment.activity_id.required'),
            'activity_id.exists' => __('appointment.activity_id.exists'),

            'day_id.required' => __('appointment.day_id.required'),
            'day_id.exists' => __('appointment.day_id.exists'),

            'date.required' => __('appointment.date.required'),
            'date.date' => __('appointment.date.date'),

            'session_duration.required' => __('appointment.session_duration.required'),

            'code.required' => __('appointment.code.required'),

            'time.required' => __('appointment.time.required'),
            'time.regex' => __('appointment.time.regex'),

            'notes.string' => __('appointment.notes.string'),
            'notes.max' => __('appointment.notes.max'),

            'customer_id.required' => __('appointment.customer_id.required'),
            'customer_id.exists' => __('appointment.customer_id.exists'),
            'customer_id.required_without' => __('appointment.customer_id.required_without'),

            'customer_phone.regex' => __('appointment.customer_phone.regex'),
            'customer_phone.required_without' => __('appointment.customer_phone.required_without'),

            'appointment_id.required' => __('appointment.appointment_id.required'),
            'appointment_id.exists' => __('appointment.appointment_id.exists'),
        ];
    }
}
