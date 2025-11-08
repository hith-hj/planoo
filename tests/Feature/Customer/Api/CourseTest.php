<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Customer;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->url = '/api/customer/v1/course';
});

describe('Course Controller Tests', function () {
    it('returns all courses for the authenticated customer', function () {
        Course::truncate();
        Course::factory(2)->create();

        $response = $this->postJson("{$this->url}/all")->assertOk();

        expect($response->json('payload.courses'))->toHaveCount(2);
    });

    it('returns paginated courses ', function () {
        Course::truncate();
        Course::factory(2)->create();
        $res = $this->postJson("{$this->url}/all?page=1&perPage=1");
        $res->assertOk();
        expect($res->json('payload'))->toHaveKeys(['page','perPage','courses']);
        expect($res->json('payload.courses'))->toHaveCount(1)
        ->and($res->json('payload.page'))->toBe(1)
        ->and($res->json('payload.perPage'))->toBe(1);
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
        $this->postJson("{$this->url}/attend?course_id={$course->id}")->assertOk();
        $customerCourse = $this->user->courses()->wherePivot('course_id',$course->id)->first();
        expect($this->user->courses()->count())->toBe(1)
        ->and($customerCourse->pivot->remaining_sessions)->toBe($course->course_duration);
    });

    it('can not attend full course', function () {
        $course = Course::factory()->create(['is_full'=>true]);
        $res = $this->postJson("{$this->url}/attend?course_id={$course->id}");
        $res->assertStatus(400);
    });

    it('can cancel course attend', function () {
        $course = Course::factory()->create();
        $this->postJson("{$this->url}/attend?course_id={$course->id}");
        $res = $this->postJson("{$this->url}/cancel?course_id={$course->id}");
        $res->assertOk();
    });

});
