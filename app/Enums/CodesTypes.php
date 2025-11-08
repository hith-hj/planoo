<?php

declare(strict_types=1);

namespace App\Enums;

enum CodesTypes
{
    case test;
    case verification;
    case appointment;
    case fee;

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }

    public static function values()
    {
        return self::names();
    }
}
