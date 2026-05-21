<?php

declare(strict_types=1);

namespace App\Enums;

enum WeekDays: string
{
    case saturday = 'saturday';
    case sunday = 'sunday';
    case monday = 'monday';
    case tuesday = 'tuesday';
    case wednesday = 'wednesday';
    case thursday = 'thursday';
    case friday = 'friday';

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
