<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationResult;

final class NotificationValidators extends Validators
{
    public static function find(array $data): ValidationResult
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }

    public static function view(array $data): ValidationResult
    {
        return Validator::make($data, [
            'notifications' => ['required', 'array', 'min:1'],
        ]);
    }

    public static function delete(array $data): ValidationResult
    {
        return Validator::make($data, [
            'notification_id' => ['required', 'numeric', 'exists:notifications,id'],
        ]);
    }
}
