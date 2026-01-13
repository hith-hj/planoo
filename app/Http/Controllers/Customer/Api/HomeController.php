<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Services\CourseServices;
use App\Services\EventServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class HomeController extends Controller
{
    public function feeds(): JsonResponse
    {
        return Success(payload: ['feeds' => [
            'activities' => app(ActivityServices::class)->allByFilter(perPage: 10)->toResourceCollection(),
            'courses' => app(CourseServices::class)->allByFilter(perPage: 10)->toResourceCollection(),
            'events' => app(EventServices::class)->allByFilter(perPage: 10)->toResourceCollection(),
            // ...$this->getFeatered(),
        ]]);
    }

    public function recommended(): JsonResponse
    {
        return Success(payload: ['recommended' => [
            'activities' => app(ActivityServices::class)->allByFilter(perPage: 3)->toResourceCollection(),
            'courses' => app(CourseServices::class)->allByFilter(perPage: 3)->toResourceCollection(),
            'events' => app(EventServices::class)->allByFilter(perPage: 3)->toResourceCollection(),
        ]]);
    }

    public function featured()
    {
        return Success(payload: $this->getFeatered());
    }

    public function search(Request $request)
    {
        $result = app(ActivityServices::class)->getter(
            model: $request->query('owner'),
            callable: [
                'where' => [
                    'name',
                    'like',
                    '%' . $request->query('search') . '%',
                ],
            ],
            // columns: ['id', 'name', 'rate'],
        ) ?? [];

        return Success(payload: ['result' => $result]);
    }

    private function getFeatered()
    {
        return ['featured' => [
            'activity' => app(ActivityServices::class)
                ->allByFilter(perPage: 1, orderBy: ['rate' => 'desc'])->toResourceCollection(),
            'course' => app(CourseServices::class)
                ->allByFilter(perPage: 1, orderBy: ['rate' => 'desc'])->toResourceCollection(),
            'event' => app(EventServices::class)
                ->allByFilter(perPage: 1, orderBy: ['rate' => 'desc'])->toResourceCollection(),
        ]];
    }
}
