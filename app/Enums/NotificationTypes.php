<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationTypes: int
{
    case normal = 0;
    case verification = 1;
    case appointment = 2;
    case chat = 3;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
