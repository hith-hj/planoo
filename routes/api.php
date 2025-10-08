<?php

declare(strict_types=1);

use App\Http\Controllers\LabelController;
use App\Http\Middleware\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'label',
        'controller' => LabelController::class,
        'middleware' => ['throttle:10,1'],
    ],
    function (): void {
        Route::get('tags', 'tags');
        Route::get('weekDays', 'weekDays');
        Route::get('categories', 'categories');
        Route::get('usersTypes', 'usersTypes');
        Route::get('activityTypes', 'activityTypes');
        Route::get('sessionDuration', 'sessionDuration');
    }
);

Route::group(
    [
        'prefix' => '/partner',
        'middleware' => [JwtMiddleware::class],
    ],
    function (): void {
        Route::group(
            [
                'prefix' => '/v1',
            ],
            function (): void {
                require 'partner/v1.php';
            }
        );
    }
);
