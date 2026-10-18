<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CourseDuration;
use App\Enums\CourseStatus;
use App\Enums\NotificationTypes;
use App\Models\Course;
use App\Models\Customer;
use App\Models\User;
use App\Support\Pagination;
use App\Traits\Filters;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class CourseServices
{
    use Filters;

    public function allByFilter(
        int $page = 1,
        int $perPage = 10,
        array $filters = [],
        array $orderBy = [],
        mixed $query = null,
    ) {
        Pagination::validate($page, $perPage);
        if ($query === null) {
            $query = Course::query();
            $query->with($this->toBeLoaded(true));
        } else {
            $query->with([...$this->toBeLoaded(true), 'isFavorite', 'isAttending']);
        }

        $this->applyFilters($query, $filters, [
            'is_active' => [true, false],
            'is_full' => [true, false],
            'course_duration' => CourseDuration::values(),
            'start_date' => [],
            'category_id' => [],
            'court_id' => [],
        ]);

        $this->applyOrderBy($query, $orderBy, ['rate', 'price']);

        $courses = $query->withCount(['reviews', 'customers'])->paginate($perPage, ['*'], 'page', $page);

        NotFound($courses->items(), 'courses');

        return $courses;
    }

    public function all(): Collection
    {
        $courses = Pagination::bounded(Course::query());
        NotFound($courses, 'courses');

        return $courses->load($this->toBeLoaded());
    }

    public function allByUser(User $user, int $page = 1, ?int $perPage = null): Collection
    {
        Required($user, 'user');
        $query = $user->courses()->orderBy('courses.id');
        if ($perPage === null) {
            $items = Pagination::bounded($query);
            NotFound($items, 'courses');

            return $items->load($this->toBeLoaded());
        }
        Pagination::validate($page, $perPage);
        $items = $query->with($this->toBeLoaded(true))->withCount(['reviews', 'customers'])
            ->forPage($page, $perPage)->get();

        return $items;
    }

    public function findByUser(User $user, mixed $id): Course
    {
        Required($user, 'user');
        $course = $user->courses()->whereId($id)->first();
        NotFound($course, 'course');

        return $course->load($this->toBeLoaded());
    }

    public function find(mixed $id): Course
    {
        Required($id, 'id');
        $course = Course::whereId($id)->first();
        NotFound($course, 'course');

        return $course->load([...$this->toBeLoaded(), 'isFavorite', 'isAttending']);
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
        return DB::transaction(function () use ($customer, $course): Course {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            Required($customer, 'customer');
            Required($course, 'course');
            Truthy($this->isAttending($customer, $course), 'already attending this course');
            Truthy($course->is_full || $course->customers()->count() >= $course->capacity, 'course is full');
            Truthy(
                ! in_array($course->status, [
                    CourseStatus::pending->value,
                    CourseStatus::active->value,
                ]),
                'event is canceled'
            );
            Truthy(! $course->is_active, 'course is inactive');

            $remaining_session = $course->course_duration;

            if (
                $course->status === CourseStatus::active->value &&
                Carbon::parse($course->start_date)->lt(today())
            ) {
                $remaining_session = $course->course_duration - $course->appointments()->count();
            }
            $course->customers()
                ->attach($customer->id, [
                    'remaining_sessions' => $remaining_session,
                    'is_complete' => false,
                ]);
            if ($course->customers()->count() >= $course->capacity) {
                $course->update(['is_full' => true]);
            }
            $course->user->notify(
                'New Customer',
                'You have a new customer',
                ['type' => NotificationTypes::course->value, 'course' => $course->id]
            );

            return $course;
        }, 3);
    }

    public function cancel(Customer $customer, Course $course)
    {
        return DB::transaction(function () use ($customer, $course): Course {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            $customer = Customer::query()->lockForUpdate()->findOrFail($customer->id);
            Required($customer, 'customer');
            Required($course, 'course');
            Truthy(! $this->isAttending($customer, $course), 'not attending this course');
            Truthy(! $this->canCancel($customer, $course), 'you can\'t cancel now');
            $course->customers()->detach($customer->id);
            if ($course->customers()->count() < $course->capacity) {
                $course->update(['is_full' => false]);
            }
            $course->user->notify(
                'Customer Left',
                'Customer left course',
                ['type' => NotificationTypes::course->value, 'course' => $course->id]
            );

            return $course;
        }, 3);
    }

    public function getCustomerQuery(Customer $customer)
    {
        return $customer->courses();
    }

    private function toBeLoaded(bool $summary = false): array
    {
        return $summary ? ['court', 'days', 'location', 'tags', 'medias', 'category'] : ['court', 'days', 'location', 'tags', 'medias', 'category', 'reviews', 'customers.medias'];
    }

    private function isAttending(Customer $customer, Course $course): bool
    {
        return $course->customers()->where('customer_id', $customer->id)->exists();
    }

    private function canCancel(Customer $customer, Course $course): bool
    {
        $customerCourse = $course
            ->customers()
            ->where([['customer_id', $customer->id], ['is_complete', false]])
            ->first();
        if ($customerCourse === null) {
            return false;
        }
        $diff = now()
            ->diffInSeconds($customerCourse->pivot->created_at) / 3600;
        if (abs($diff) > config('app.settings.course_cancelation_period', 24)) {
            return false;
        }

        return true;
    }
}
