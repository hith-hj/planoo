<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Services\CourseServices;
use App\Services\EventServices;
use App\Support\Pagination;
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
            // ...$this->getFeatured(),
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
        return Success(payload: $this->getFeatured());
    }

    public function search(Request $request)
    {
        $request->validate(['search' => ['nullable', 'string', 'max:200']]);
        $page = $request->integer('page', 1);
        $perPage = $request->integer('perPage', 10);
        Pagination::validate($page, $perPage);
        $result = app(ActivityServices::class)->getter(
            model: $request->query('owner'),
            callable: [
                'limit' => $perPage,
                'offset' => ($page - 1) * $perPage,
                'orderBy' => ['id', 'asc'],
                'where' => [
                    'name',
                    'like',
                    '%'.$request->query('search').'%',
                ],
            ],
            // columns: ['id', 'name', 'rate'],
        ) ?? [];

        return Success(payload: ['result' => $result, 'page' => $page, 'perPage' => $perPage]);
    }

    private function getFeatured()
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
