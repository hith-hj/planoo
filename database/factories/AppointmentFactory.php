<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use App\Models\Course;
use Carbon\Carbon;
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
            'appointable_type' => Course::class,
            'appointable_id' => 1,
            'date' => fake()->randomElement($this->toDate()),
            'time' => $this->toTime(),
            'price' => random_int(1000, 10000),
            'session_duration' => fake()->randomElement(SessionDuration::values()),
            'status' => AppointmentStatus::accepted->value,
            'notes' => fake()->sentence,
            'customer_id' => 1,
        ];
    }

    public function fakerData($owner, array $extras = [])
    {
        Truthy($owner === null, 'Onwer is required for Appointment factory');
        Truthy(! method_exists($owner, 'days'), 'Onwer missing days() method');
        $day = $owner->days()->first();

        return [
            'activity_id' => $owner->id,
            'day_id' => $day->id,
            'date' => fake()->randomElement($this->toDate($day['day'])),
            'session_duration' => fake()->randomElement(SessionDuration::values()),
            'notes' => fake()->word,
            ...$extras,
        ];
    }

    private function toDate(string $day = 'sunday', int $count = 5)
    {
        $dates = [];
        $start = Carbon::tomorrow();
        $end = Carbon::tomorrow()->addDays(10);

        while (count($dates) < $count) {
            $randomTimestamp = random_int($start->timestamp, $end->timestamp);
            $randomDate = Carbon::createFromTimestamp($randomTimestamp);

            if ($randomDate->format('l') === ucfirst(mb_strtolower($day))) {
                $dates[] = $randomDate->toDateString();
            }
        }

        return $dates;
    }

    private function toTime()
    {
        return today()->hour(9)->addHour(random_int(1, 8))->toTimeString();
    }
}
