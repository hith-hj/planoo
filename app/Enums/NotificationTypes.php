<?php

declare(strict_types=1);

namespace App\Enums;

enum NotificationTypes: int
{
    case normal = 0;
    case verification = 1;
    case activity = 2;
    case appointment = 4;
    case course = 3;
    case session = 5;
    case chat = 10;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
