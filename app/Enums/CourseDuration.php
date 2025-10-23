<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseDuration: int
{
    case short = 4;
    case medium = 8;
    case long = 16;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
