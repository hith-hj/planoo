<?php

declare(strict_types=1);

namespace App\Enums;

enum AdminsRoles: int
{
    case super = 0;
    case manager = 1;
    case editor = 2;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
