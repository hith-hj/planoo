<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class CustomerAuthValidators extends Validators
{
    public static function register(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'unique:customers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function verify(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
        ], self::messages());
    }

    public static function login(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'password' => ['required', 'string', 'min:8'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function forgetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function resetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
            'firebase_token' => ['required'],
        ], self::messages());
    }

    public static function resendCode(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
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
     * Get the customer validation translation messages.
     */
    private static function messages(): array
    {
        return [
            'name.required' => __('customer.name.required'),
            'name.max' => __('customer.name.max'),

            'phone.required' => __('customer.phone.required'),
            'phone.regex' => __('customer.phone.regex'),
            'phone.unique' => __('customer.phone.unique'),
            'phone.exists' => __('customer.phone.exists'),

            'password.required' => __('customer.password.required'),
            'password.min' => __('customer.password.min'),
            'password.confirmed' => __('customer.password.confirmed'),

            'firebase_token.required' => __('customer.firebase_token.required'),

            'code.required' => __('customer.code.required'),
            'code.numeric' => __('customer.code.numeric'),
            'code.exists' => __('customer.code.exists'),

            'old_password.required' => __('customer.old_password.required'),
            'old_password.min' => __('customer.old_password.min'),

            'new_password.required' => __('customer.new_password.required'),
            'new_password.min' => __('customer.new_password.min'),
            'new_password.confirmed' => __('customer.new_password.confirmed'),
        ];
    }
}
