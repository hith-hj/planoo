<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Customer;
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
        $courseData = Course::factory()->for($this->user, 'user')->make()->toArray();

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

    it('can attend customer by id for course', function () {
        $course = Course::factory()->for($this->user,'user')->create();
        $res = $this->postJson("{$this->url}/attend?course_id={$course->id}",['customer_id'=>1]);
        $res->assertOk();
        $customerCourse = $course->customers()->wherePivot('customer_id',1)->first();
        expect($customerCourse->pivot->remaining_sessions)->toBe($course->course_duration);
    });

    it('can attend customer by phone for course', function () {
        $course = Course::factory()->for($this->user,'user')->create();
        $res = $this->postJson("{$this->url}/attend?course_id={$course->id}",['customer_phone'=>'0987654321']);
        $res->assertOk();
        $customer = Customer::where('phone','0987654321')->first();
        $customerCourse = $course->customers()->wherePivot('customer_id',$customer->id)->first();
        expect($customerCourse->pivot->remaining_sessions)->toBe($course->course_duration);
    });

    it('can not attend full course', function () {
        $course = Course::factory()->for($this->user,'user')->create(['is_full'=>true]);
        $res = $this->postJson("{$this->url}/attend?course_id={$course->id}",['customer_id'=>1]);
        $res->assertStatus(400);
    });

    it('can cancel course attend by customer id', function () {
        $course = Course::factory()->for($this->user,'user')->create();
        $this->postJson("{$this->url}/attend?course_id={$course->id}",['customer_id'=>1]);
        $res = $this->postJson("{$this->url}/cancel?course_id={$course->id}",['customer_id'=>1]);
        $res->assertOk();
        expect($course->customers()->wherePivot('customer_id',1)->first())->toBeNull();
    });

    it('can not cancel course after specific time', function () {
        $course = Course::factory()->for($this->user,'user')->create();
        $this->postJson("{$this->url}/attend?course_id={$course->id}",['customer_id'=>1]);
        $customer = $course->customers()->where('customer_id',1)->first();
        $customer->pivot->update(['created_at'=>now()->subDays(2)]);
        $res = $this->postJson("{$this->url}/cancel?course_id={$course->id}",['customer_id'=>1]);
        $res->assertStatus(400);
    });
});
