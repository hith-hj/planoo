<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ActivityTypes;
use App\Enums\SessionDuration;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

final class LabelController extends Controller
{
    public function categories()
    {
        return Success(payload: ['categories' => Category::all()]);
    }

    public function tags()
    {
        return Success(payload: ['tags' => Tag::all()]);
    }

    public function usersTypes(): JsonResponse
    {
        return Success(payload: [
            'usersTypes' => [
                ['name' => 'stadium'],
                ['name' => 'trainer'],
            ],
        ]);
    }

    public function activityTypes(): JsonResponse
    {
        return Success(payload: [
            'activityTypes' => ActivityTypes::names(),
        ]);
    }

    public function sessionDuration(): JsonResponse
    {
        return Success(payload: [
            'durations' => SessionDuration::values(),
        ]);
    }
}
