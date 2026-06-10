<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CourseDuration;
use App\Enums\CourseStatus;
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
        $duration = fake()->randomElement(CourseDuration::values());
        $cpacity = random_int(5, 15);
        $start_date = today()->addDays($duration * 2)->toDateString();

        return [
            'user_id' => 1,
            'category_id' => 1,
            'court_id' => 1,
            'name' => fake()->word,
            'description' => fake()->sentence,
            'is_active' => 1,
            'is_full' => false,
            'price' => random_int(1000, 10000),
            'course_duration' => $duration,
            'capacity' => $cpacity,
            'start_date' => $start_date,
            'cancellation_fee' => fake()->optional()->numberBetween(1000, 5000),
            'status' => CourseStatus::pending->value,
            'rate' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Course $course) {
            $course->category()->associate(Category::inRandomOrder()->first())->save();
            $course->tags()->attach(Tag::inRandomOrder()->take(2)->get());
            Media::factory()->for($course, 'holder')->create();
            Review::factory()->for($course, 'holder')->create();
            Location::factory()->for($course, 'holder')->create();
            Day::factory()->day()->for($course, 'holder')->create();
            Appointment::factory()->for($course, 'holder')->create();
            Customer::factory()->hasAttached(
                $course,
                ['remaining_sessions' => $course->course_duration],
                'courses'
            )->create();
        });
    }
}
