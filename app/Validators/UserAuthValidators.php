<?php

declare(strict_types=1);

namespace App\Validators;

use App\Enums\UsersTypes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final class UserAuthValidators extends Validators
{
    public static function register(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['required', Rule::in(UsersTypes::cases())],
            'description' => ['required', 'string', 'max:1500'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function verify(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
        ], self::messages());
    }

    public static function login(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function forgetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function resetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function resendCode(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
        ], self::messages());
    }

    public static function changePassword(array $data)
    {
        return Validator::make($data, [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], self::messages());
    }

    /**
     * Get the user validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'name.required' => __('user.name.required'),
            'name.string' => __('user.name.string'),
            'name.max' => __('user.name.max'),

            'email.required' => __('user.email.required'),
            'email.string' => __('user.email.string'),
            'email.email' => __('user.email.email'),
            'email.max' => __('user.email.max'),
            'email.unique' => __('user.email.unique'),

            'phone.required' => __('user.phone.required'),
            'phone.regex' => __('user.phone.regex'),
            'phone.unique' => __('user.phone.unique'),
            'phone.exists' => __('user.phone.exists'),

            'password.required' => __('user.password.required'),
            'password.string' => __('user.password.string'),
            'password.min' => __('user.password.min'),
            'password.confirmed' => __('user.password.confirmed'),

            'account_type.required' => __('user.account_type.required'),
            'account_type.in' => __('user.account_type.in'),

            'description.required' => __('user.description.required'),
            'description.string' => __('user.description.string'),
            'description.max' => __('user.description.max'),

            'firebase_token.required' => __('user.firebase_token.required'),

            'code.required' => __('user.code.required'),
            'code.numeric' => __('user.code.numeric'),
            'code.exists' => __('user.code.exists'),

            'old_password.required' => __('user.old_password.required'),
            'old_password.string' => __('user.old_password.string'),
            'old_password.min' => __('user.old_password.min'),

            'new_password.required' => __('user.new_password.required'),
            'new_password.string' => __('user.new_password.string'),
            'new_password.min' => __('user.new_password.min'),
            'new_password.confirmed' => __('user.new_password.confirmed'),
        ];
    }
}
