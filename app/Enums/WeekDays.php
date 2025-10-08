<?php

declare(strict_types=1);

namespace App\Enums;

enum WeekDays
{
    case saturday;
    case sunday;
    case monday;
    case tuesday;
    case wednesday;
    case thursday;
    case friday;

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
}
