<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Day;
use App\Models\Location;
use App\Models\Media;
use App\Models\Review;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
final class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'category_id' => 1,
            'name' => fake()->colorName,
            'description' => fake()->paragraph(2),
            'price' => mt_rand(500, 1000),
            'session_duration' => fake()->randomElement([30, 60, 90, 120]),
            'is_active' => fake()->boolean(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Activity $activity) {
            $category = Category::inRandomOrder()->take(1)->first();
            $activity->category()->associate($category)->save();
            $tags = Tag::inRandomOrder()->take(2)->get();
            $activity->tags()->attach($tags);
            Day::factory()->day()->for($activity, 'holder')->create();
            Location::factory()->for($activity, 'holder')->create();
            Media::factory()->for($activity, 'holder')->create();
            Review::factory()->for($activity, 'holder')->create();
        });
    }
}
