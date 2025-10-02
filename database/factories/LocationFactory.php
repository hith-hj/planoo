<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location>
 */
final class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'long' => fake()->longitude(31.00000001, 31.55555555),
            'lat' => fake()->latitude(31.00000001, 31.55555555),
            'name' => fake()->sentence(3),
        ];
    }
}
