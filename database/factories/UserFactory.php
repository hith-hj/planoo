<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AccountStatus;
use App\Enums\UsersTypes;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName,
            'email' => fake()->unique()->email,
            'phone' => fake()->regexify("(09)[1-9]{1}\d{7}"),
            'password' => bcrypt('password'),
            'firebase_token' => Str::random(64),
            'verified_by' => 'phone',
            'verified_at' => now(),
            'status' => AccountStatus::fresh->value,
            'account_type' => fake()->randomElement(UsersTypes::names()),
            'description' => fake()->paragraph(2),
            'is_notifiable' => true,
            'is_active' => true,
            'rate' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            Activity::factory()->for($user, 'user')->create();
            Course::factory()->for($user, 'user')->create();
            Event::factory()->for($user, 'user')->create();
        });
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
