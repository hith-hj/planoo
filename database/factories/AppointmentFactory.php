<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
final class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->toDate(),
            'time' => $this->toTime(),
            'price' => mt_rand(1000, 10000),
            'session_duration' => fake()->randomElement(SessionDuration::values()),
            'status' => AppointmentStatus::accepted->value,
            'notes' => fake()->sentence,
        ];
    }

    public function fakerData($owner, array $extras = [])
    {
        Truthy($owner === null, 'Onwer is required for Appointment factory');
        Truthy(! method_exists($owner, 'days'), 'Onwer missing days() method');

        return [
            'activity_id' => $owner->id,
            'day_id' => $owner->days()->first()->id,
            'date' => $this->toDate(),
            'session_duration' => fake()->randomElement(SessionDuration::values()),
            'notes' => fake()->word,
            ...$extras,
        ];
    }

    private function toDate()
    {
        return today()->addDays(mt_rand(1, 5))->toDateString();
    }

    private function toTime()
    {
        return today()->hour(9)->addHour(mt_rand(1, 8))->toTimeString();
    }
}
