<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
final class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['wifi', 'ac', 'restrooms']),
            'icon' => '#',
        ];
    }

    public function tags()
    {
        return $this->state(function () {
            return ['tags' => [1, 2, 3, 4]];
        });
    }
}
