<?php

declare(strict_types=1);

use App\Http\Controllers\LabelController;
use App\Http\Middleware\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(LabelController::class)
    ->prefix('label')
    ->middleware(['throttle:10,1'])
    ->group(
        function (): void {
            Route::get('tags', 'tags');
            Route::get('weekDays', 'weekDays');
            Route::get('categories', 'categories');
            Route::get('usersTypes', 'usersTypes');
            Route::get('activityTypes', 'activityTypes');
            Route::get('sessionDuration', 'sessionDuration');
            Route::get('courseDuration', 'courseDuration');
        }
    );

Route::middleware(JwtMiddleware::class)->group(function () {

    Route::prefix('partner')->name('partner.')->group(
        function (): void {
            Route::prefix('v1')->group(
                function (): void {
                    require 'partner/v1.php';
                }
            );
        }
    );

    Route::prefix('customer')->name('customer.')->group(
        function (): void {
            Route::prefix('v1')->group(
                function (): void {
                    require 'customer/v1.php';
                }
            );
        }
    );
});
