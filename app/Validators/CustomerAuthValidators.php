<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;

final class CustomerAuthValidators
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
        ]);
    }

    public static function forgetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
        ]);
    }

    public static function resetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public static function resendCode(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:customers'],
        ]);
    }

    public static function changePAssword(array $data)
    {
        return Validator::make($data, [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }
}
