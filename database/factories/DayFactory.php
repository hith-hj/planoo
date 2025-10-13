<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\WeekDays;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DayFactory extends Factory
{
    public function definition(): array
    {
        return [];
    }

    public function day()
    {
        return $this->state(function () {
            return $this->getDay();
        });
    }

    public function days(int $count = 2)
    {
        $days = [];
        for ($i = 0; $i < $count; $i++) {
            $days[] = $this->getDay();
        }

        return $days;
    }

    private function getDay()
    {
        return [
            'day' => fake()->randomElement(WeekDays::names()),
            'start' => fake()->regexify('([01]\d|2[0-3]):(00|30)'),
            'end' => fake()->regexify('([01]\d|2[0-3]):(00|30)'),
        ];
    }
}
