<?php

declare(strict_types=1);

namespace Database\Factories;

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

    public function days()
    {
        return $this->state(function () {
            $days = [];
            for ($i = 0; $i < 2; $i++) {
                $days[] = $this->getDay();
            }

            return ['days' => $days];
        });
    }

    private function getDay()
    {
        return [
            'day' => fake()->randomElement(getWeekDays()),
            'start' => fake()->time('H:i'),
            'end' => fake()->time('H:i'),
        ];
    }
}
