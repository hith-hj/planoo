<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\NotificationTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

final class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'belongTo_id' => 1,
            'belongTo_type' => 'user',
            'status' => 0,
            'title' => 'Title',
            'body' => 'Body',
            'type' => fake()->randomElement(NotificationTypes::values()),
            'payload' => json_encode([
                'extra' => 'this is extra shit',
            ]),
        ];
    }
}
