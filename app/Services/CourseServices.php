<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NotificationTypes;
use App\Models\Course;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class CourseServices
{
    public function all(): Collection
    {
        $courses = Course::all();
        NotFound($courses, 'courses');

        return $courses->load($this->toBeLoaded());
    }

    public function allByUser(User $user): Collection|Model
    {
        Required($user, 'user');
        $courses = $user->courses;
        NotFound($courses, 'courses');

        return $courses->load($this->toBeLoaded());
    }

    public function findByUser(User $user, int $id): Course
    {
        Required($user, 'user');
        $course = $user->courses()->whereId($id)->first();
        NotFound($course, 'course');

        return $course->load($this->toBeLoaded());
    }

    public function find(int $id): Course
    {
        Required($id, 'id');
        $course = Course::whereId($id)->first();
        NotFound($course, 'course');

        return $course->load($this->toBeLoaded());
    }

    public function create(User $user, array $data): Course
    {
        Required($user, 'user');
        Required($data, 'course data');
        $course = $user->courses()->create($data);

        return $course->fresh()->load($this->toBeLoaded());
    }

    public function update(User $user, Course $course, array $data): Course
    {
        Required($user, 'user');
        Required($data, 'course data');
        $course->update($data);

        return $course->load($this->toBeLoaded());
    }

    public function delete(Course $course): bool
    {
        Required($course, 'course');

        return $course->delete();
    }

    public function toggleActivation(Course $course): bool
    {
        Required($course, 'course');

        return $course->update(['is_active' => ! $course->is_active]);
    }

    public function attend(Customer $customer, Course $course)
    {
        Required($customer, 'customer');
        Required($course, 'course');
        Truthy($this->isAttending($customer, $course), 'Already attending this course.');
        Truthy($course->is_full, 'Course is full');

        $course->customers()
            ->attach($customer->id, [
                'remaining_sessions' => $course->course_duration,
                'is_complete' => false,
            ]);
        if ($course->customers()->count() === $course->capacity) {
            $course->update(['is_full' => true]);
        }
        $course->user->notify(
            'New Customer',
            'You have a new customer',
            ['type' => NotificationTypes::course->value, 'course' => $course->id]
        );

        return $course;
    }

    public function cancel(Customer $customer, Course $course)
    {
        Required($customer, 'customer');
        Required($course, 'course');
        Truthy(! $this->isAttending($customer, $course), 'Not attending this course.');
        $course->customers()->detach($customer->id);
        if ($course->customers()->count() < $course->capacity) {
            $course->update(['is_full' => false]);
        }
        $course->user->notify(
            'Customer Left',
            'Customer left corse',
            ['type' => NotificationTypes::course->value, 'course' => $course->id]
        );

        return $course;
    }

    private function toBeLoaded()
    {
        return ['days', 'location', 'tags', 'medias', 'category', 'customers'];
    }

    private function isAttending(Customer $customer, Course $course): bool
    {
        return $course->customers()->where('customer_id', $customer->id)->exists();
    }
}
