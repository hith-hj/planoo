<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Day;
use App\Models\Event;
use App\Models\Location;
use App\Models\Media;
use App\Models\Review;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
final class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $duration = random_int(1, 15);
        $cpacity = random_int(5, 15);
        $start_date = today()->addDays($duration * 2)->toDateString();
        $end_date = $start_date;
        if ($duration > 1) {
            $end_date = Carbon::createFromDate($start_date)->addDays($duration)->toDateString();
        }

        return [
            'user_id' => 1,
            'category_id' => 1,
            'name' => fake()->name,
            'description' => fake()->sentence,
            'is_active' => 1,
            'is_full' => 0,
            'event_duration' => $duration,
            'capacity' => $cpacity,
            'admission_fee' => fake()->numberBetween(1000, 5000),
            'withdrawal_fee' => fake()->optional()->numberBetween(100, 500),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => EventStatus::pending->value,
            'rate' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Event $event) {
            $category = Category::inRandomOrder()->first();
            $event->category()->associate($category)->save();
            $tags = Tag::inRandomOrder()->take(2)->get();
            $event->tags()->attach($tags);
            Day::factory()->day()->for($event, 'holder')->create();
            Location::factory()->for($event, 'holder')->create();
            Media::factory()->for($event, 'holder')->create();
            Review::factory()->for($event, 'holder')->create();
            Appointment::factory(2)->for($event, 'holder')->create();
            Customer::factory(2)->hasAttached($event, relationship: 'events')->create();
        });
    }
}
