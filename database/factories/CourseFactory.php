<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SessionDuration;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Day;
use App\Models\Location;
use App\Models\Media;
use App\Models\Review;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
final class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'category_id' => 1,
            'name' => fake()->word,
            'description' => fake()->sentence,
            'is_active' => 1,
            'is_full' => false,
            'price' => random_int(1000, 10000),
            'session_duration' => fake()->randomElement(SessionDuration::values()),
            'capacity' => random_int(1, 30),
            'cancellation_fee' => fake()->optional()->numberBetween(1000, 5000),
            'rate' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Course $course) {
            $category = Category::inRandomOrder()->first();
            $course->category()->associate($category)->save();
            $tags = Tag::inRandomOrder()->take(2)->get();
            $course->tags()->attach($tags);
            Media::factory()->for($course, 'holder')->create();
            Review::factory()->for($course, 'holder')->create();
            Location::factory()->for($course, 'holder')->create();
            Day::factory()->day()->for($course, 'holder')->create();
            Appointment::factory(2)->for($course, 'holder')->create();
            Customer::factory(2)->hasAttached($course, relationship: 'courses')->create();
        });
    }
}
