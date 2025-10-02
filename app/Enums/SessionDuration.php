<?php

declare(strict_types=1);

namespace App\Enums;

enum SessionDuration: int
{
    case HalfHour = 30;
    case OneHour = 60;
    case HourAndHalf = 90;
    case TwoHours = 120;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
