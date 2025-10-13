<?php

declare(strict_types=1);

namespace App\Enums;

enum UsersTypes
{
    case stadium;
    case trainer;

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }

    public static function values()
    {
        return self::names();
    }
}
