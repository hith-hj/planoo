<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseDuration: int
{
    case short = 10;
    case medium = 15;
    case long = 30;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
