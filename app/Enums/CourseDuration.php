<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseDuration: int
{
    case one = 1;
    case two = 2;
    case four = 4;
    case six = 6;
    case eight = 8;
    case ten = 10;
    case twelve = 12;
    case fourteen = 14;
    case fifteen = 15;
    case sixteen = 16;
    case eighteen = 18;
    case twenty = 20;
    case twentyfive = 25;
    case thirty = 30;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
