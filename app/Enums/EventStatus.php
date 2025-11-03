<?php

declare(strict_types=1);

namespace App\Enums;

enum EventStatus: int
{
    case canceled = -1;
    case pending = 0;
    case active = 1;
    case completed = 2;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
