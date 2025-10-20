<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class CourseServices
{
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

        return $course->load($this->toBeLoaded());
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
        return $course->delete();
    }

    public function toggleActivation(Course $course): bool
    {
        return $course->update(['is_active' => ! $course->is_active]);
    }

    private function toBeLoaded()
    {
        return ['days', 'location', 'tags', 'medias', 'category', 'customers'];
    }
}
