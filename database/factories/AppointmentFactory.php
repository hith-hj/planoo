<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use App\Models\Activity;
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
            'status' => fake()->randomElement(AppointmentStatus::values()),
            'notes' => fake()->sentence,
        ];
    }

    public function fakerData(array $extras = [])
    {
        $activity = Activity::inRandomOrder()->first();

        return [
            'activity_id' => $activity->id,
            'day_id' => $activity->days->first()->id,
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
