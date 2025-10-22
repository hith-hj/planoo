<?php

namespace Database\Factories;

use App\Enums\AdminsRoles;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstNameFemale,
            'email' => fake()->unique()->email,
            'password' => bcrypt('password'),
            'role' => fake()->randomElement(AdminsRoles::values()),
        ];
    }
}
