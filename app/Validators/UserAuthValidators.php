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
        ]);
    }

    public static function verify(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'code' => ['required', 'numeric', 'exists:codes,code'],
        ]);
    }

    public static function login(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    public static function forgetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
        ]);
    }

    public static function resetPassword(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public static function resendCode(array $data)
    {
        return Validator::make($data, [
            'phone' => ['required', 'regex:/^09[1-9]{1}\d{7}$/', 'exists:users'],
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
