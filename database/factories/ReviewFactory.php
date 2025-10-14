<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
final class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'belongTo_id' => 1,
            'belongTo_type' => Activity::class,
            'customer_id' => 1,
            'content' => fake()->paragraph(4),
            'rate' => fake()->numberBetween(1, 10),
        ];
    }
}
