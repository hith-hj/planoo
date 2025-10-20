<?php

declare(strict_types=1);

namespace App\Enums;

enum SectionsTypes: int
{
    case activity = 1;
    case course = 2;
    case event = 3;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
