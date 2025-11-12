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
        ]);
    }

    public static function verify(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
        ]);
    }

    public static function login(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'password' => ['required', 'string', 'min:8'],
            'firebase_token' => ['required'],
        ]);
    }

    public static function forgetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'firebase_token' => ['required'],
        ]);
    }

    public static function resetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
            'firebase_token' => ['required'],
        ]);
    }

    public static function resendCode(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
        ]);
    }

    public static function changePassword(array $data)
    {
        return Validator::make($data, [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
