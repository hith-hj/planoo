<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
final class CourtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->userId(),
            'name' => fake()->colorName,
            'description' => fake()->sentence,
        ];
    }

    /**
     * Resolve a valid partner user id for the court owner.
     */
    private function userId(): int|string
    {
        $user = User::query()->orderBy('id')->first();
        if ($user) {
            return $user->id;
        }

        return User::factory()->create()->id;
    }
}
