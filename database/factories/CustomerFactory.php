<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Models\Customer;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
final class CustomerFactory extends Factory
{
    public function definition(): array
    {
        $minCustomerAge = Setting('minimum_customer_age', 14);

        return [
            'name' => fake()->name,
            'password' => bcrypt('password'),
            'phone' => fake()->regexify("(09)[1-9]{1}\d{7}"),
            'status' => AccountStatus::fresh->value,
            'email' => fake()->email,
            'gender' => fake()->randomElement(['male', 'female']),
            'birthdate' => fake()->dateTimeBetween(endDate: now()->subYears($minCustomerAge))->format('Y-m-d'),
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

    public function configure()
    {
        return $this->afterCreating(function (Customer $customer) {
            Media::factory()->for($customer, 'holder')->create(['name' => 'profile_image']);
        });
    }
}
