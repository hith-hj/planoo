<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Day;
use App\Models\Location;
use App\Models\Media;
use App\Models\Tag;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/course';
});

describe('Course Controller Tests', function () {
    it('returns all courses for the authenticated partner', function () {
        $this->user->courses()->delete();
        Course::factory(2)->for($this->user, 'user')->create();

        $response = $this->getJson("{$this->url}/all")->assertOk();

        expect($response->json('payload.courses'))->toHaveCount(2);
    });

    it('finds a specific course by ID', function () {
        $course = Course::factory()->for($this->user, 'user')->create();

        $response = $this->getJson("{$this->url}/find?course_id={$course->id}")
            ->assertOk();

        expect($response->json('payload.course.id'))->toBe($course->id);
    });

    it('fails to find an course with invalid ID', function () {
        $this->getJson("{$this->url}/find?course_id=422")->assertStatus(422);
    });

    it('creates a new course with days, location, media, and tags', function () {
        $courseData = Course::factory()->for($this->user, 'user')
            ->make(['user_id' => $this->user->id])->toArray();

        $days = Day::factory()->days();
        $location = Location::factory()->make()->toArray();
        $media = [
            'type' => 'image',
            'media' => Media::factory()->medias(2),
        ];
        $tags = Tag::factory()->tags()->make()->toArray();

        $payload = array_merge($courseData, ['days' => $days], $location, $media, $tags);

        $response = $this->postJson("{$this->url}/create", $payload);
        $response->assertOk();

        expect($response->json('payload.course'))->not->toBeNull();

        $createdId = $response->json('payload.course.id');
        $createdCourse = Course::with(['tags', 'days', 'medias', 'location'])->find($createdId);

        expect($createdCourse->days()->count())->toBe(count($days))
            ->and($createdCourse->location)->not->toBeNull()
            ->and($createdCourse->location->long)->toBe($location['long'])
            ->and($createdCourse->tags)->not->toBeNull()
            ->and($createdCourse->medias)->not->toBeNull()
            ->and($createdCourse->medias->count())->toBe(2);
    });

    it('fails to create an course with invalid data', function () {
        $this->postJson("{$this->url}/create", [])->assertStatus(422);
    });

    it('updates an existing course', function () {
        $course = Course::factory()->for($this->user, 'user')->create();
        $course->update(['name' => 'tido']);

        $updatePayload = ['course_id' => $course->id, ...$course->toArray()];

        $response = $this->patchJson("{$this->url}/update", $updatePayload)->assertOk();

        expect($response->json('payload.course.name'))->toBe('tido');
    });

    it('fails to update an course with invalid data', function () {
        $this->patchJson("{$this->url}/update", [])->assertStatus(422);
    });

    it('deletes an course', function () {
        $course = Course::factory()->for($this->user, 'user')->create();

        $this->deleteJson("{$this->url}/delete", [
            'course_id' => $course->id,
        ])->assertOk();

        expect(Course::find($course->id))->toBeNull();
    });

    it('toggles the activation status of an course', function () {
        $course = Course::factory()
            ->for($this->user, 'user')
            ->create(['is_active' => false]);

        $this->postJson("{$this->url}/toggleActivation", [
            'course_id' => $course->id,
        ])->assertOk();

        expect($course->fresh()->is_active)->toBeTrue();
    });
});
