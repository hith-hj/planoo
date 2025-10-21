<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
final class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'password' => bcrypt('password'),
            'phone' => fake()->regexify("(09)[1-9]{1}\d{7}"),
            'status' => AccountStatus::fresh->value,
            'firebase_token' => str()->random(32),
            'verified_by' => 'phone',
            'verified_at' => now(),
            'is_notifiable' => true,
            'is_active' => true,
        ];
    }

    public function password()
    {
        return $this->state(function () {
            return [
                'password' => 'password',
                'password_confirmation' => 'password',
            ];
        });
    }
}
