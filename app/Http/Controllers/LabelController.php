<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CourseDuration;
use App\Enums\SectionsTypes;
use App\Enums\SessionDuration;
use App\Enums\UsersTypes;
use App\Enums\WeekDays;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

final class LabelController extends Controller
{
    // Cache TTL set to 1 week (604800 seconds) for Enums, Forever for DB records
    private const ENUM_CACHE_TTL = 604800;

    public function categories()
    {
        $categories = cache()->remember('labels.categories', self::ENUM_CACHE_TTL, function () {
            return Category::all();
        });

        return Success(payload: ['categories' => $categories]);
    }

    public function tags()
    {
        $tags = cache()->remember('labels.tags', self::ENUM_CACHE_TTL, function () {
            return Tag::all();
        });

        return Success(payload: ['tags' => $tags]);
    }

    public function usersTypes(): JsonResponse
    {
        $types = cache()->remember('labels.users_types', self::ENUM_CACHE_TTL, function () {
            return UsersTypes::names();
        });

        return Success(payload: ['usersTypes' => $types]);
    }

    public function activityTypes(): JsonResponse
    {
        $types = cache()->remember('labels.activity_types', self::ENUM_CACHE_TTL, function () {
            return SectionsTypes::names();
        });

        return Success(payload: ['activityTypes' => $types]);
    }

    public function sessionDuration(): JsonResponse
    {
        $durations = cache()->remember('labels.session_durations', self::ENUM_CACHE_TTL, function () {
            return SessionDuration::values();
        });

        return Success(payload: ['durations' => $durations]);
    }

    public function courseDuration(): JsonResponse
    {
        $durations = cache()->remember('labels.course_durations', self::ENUM_CACHE_TTL, function () {
            return CourseDuration::values();
        });

        return Success(payload: ['durations' => $durations]);
    }

    public function weekDays(): JsonResponse
    {
        $days = cache()->remember('labels.week_days', self::ENUM_CACHE_TTL, function () {
            return WeekDays::names();
        });

        return Success(payload: ['days' => $days]);
    }
}
