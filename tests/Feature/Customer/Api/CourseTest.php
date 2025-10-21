<?php

declare(strict_types=1);

use App\Models\Course;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->url = '/api/customer/v1/course';
});

describe('Course Controller Tests', function () {
    it('returns all courses for the authenticated customer', function () {
        Course::truncate();
        Course::factory(2)->create();

        $response = $this->getJson("{$this->url}/all")->assertOk();

        expect($response->json('payload.courses'))->toHaveCount(2);
    });

    it('finds a specific course by ID', function () {
        $course = Course::factory()->create();

        $response = $this->getJson("{$this->url}/find?course_id={$course->id}")
            ->assertOk();

        expect($response->json('payload.course.id'))->toBe($course->id);
    });

    it('fails to find an course with invalid ID', function () {
        $this->getJson("{$this->url}/find?course_id=422")->assertStatus(422);
    });

    it('can attend course', function () {
        $course = Course::factory()->create();

        $response = $this->postJson("{$this->url}/attend?course_id={$course->id}")
            ->assertOk();
    });

});
