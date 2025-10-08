<?php

declare(strict_types=1);

namespace App\Enums;

enum AppointmentStatus: int
{
    case canceled = -1;
    case accepted = 0;
    case completed = 1;

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function names()
    {
        return array_column(self::cases(), 'name');
    }
}
