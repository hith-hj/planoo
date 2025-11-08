<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Services\CourseServices;
use App\Services\EventServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class HomeController extends Controller
{
    public function feeds(): JsonResponse
    {
        return Success(payload: ['feeds' => [
            'activities' => app(ActivityServices::class)->allByFilter(perPage: 1)->toResourceCollection(),
            'courses' => app(CourseServices::class)->allByFilter(perPage: 1)->toResourceCollection(),
            'events' => app(EventServices::class)->allByFilter(perPage: 1)->toResourceCollection(),
        ]]);
    }

    public function recommended(): JsonResponse
    {
        // $lastAppointment = Auth::user()->appointments()->last();

        return Success(payload: ['feeds' => [
            'activities' => app(ActivityServices::class)->allByFilter(perPage: 3)->toResourceCollection(),
            'courses' => app(CourseServices::class)->allByFilter(perPage: 3)->toResourceCollection(),
            'events' => app(EventServices::class)->allByFilter(perPage: 3)->toResourceCollection(),
        ]]);
    }
}
