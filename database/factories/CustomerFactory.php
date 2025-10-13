<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
final class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'password' => bcrypt('password'),
            'phone' => fake()->regexify("(09)[1-9]{1}\d{7}"),
            'status' => CustomerStatus::fresh->value,
        ];
    }
}
