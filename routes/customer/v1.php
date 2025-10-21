<?php

declare(strict_types=1);

use App\Http\Controllers\Customer\Api\ActivityController;
use App\Http\Controllers\Customer\Api\AppointmentController;
use App\Http\Controllers\Customer\Api\AuthController;
use App\Http\Controllers\Customer\Api\CourseController;
use App\Http\Controllers\Customer\Api\ReviewController;
use App\Http\Middleware\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(
        function (): void {
            Route::withoutMiddleware([JwtMiddleware::class])->group(function (): void {
                Route::post('register', 'register')->name('register')->middleware(['throttle:3,1']);
                Route::post('verify', 'verify')->name('verify')->middleware(['throttle:3,1']);
                Route::post('login', 'login')->name('login')->middleware(['throttle:3,1']);
                Route::post('forgetPassword', 'forgetPassword')->name('forgetPassword')->middleware(['throttle:3,1']);
                Route::post('resetPassword', 'resetPassword')->name('resetPassword')->middleware(['throttle:3,1']);
                Route::post('resendCode', 'resendCode')->name('resendCode')->middleware(['throttle:1,10']);
                Route::get('refreshToken', 'refreshToken')->name('refreshToken')->middleware(['throttle:1,10']);
            });

            Route::post('logout', 'logout')->name('logout');
            Route::post('changePassword', 'changePassword')->name('changePassword');
        }
    );

Route::controller(ActivityController::class)
    ->prefix('activity')
    ->name('activity.')
    ->group(
        function (): void {
            Route::get('all', 'all')->name('all');
            Route::get('find', 'find')->name('find');
        }
    );

Route::controller(AppointmentController::class)
    ->prefix('appointment')
    ->name('appointment.')
    ->group(
        function (): void {
            Route::post('all/{owner_type}/{owner_id?}', 'all')->name('all');
            Route::post('check', 'check')->name('check');
            Route::post('create', 'create')->name('create');
            Route::post('cancel', 'cancel')->name('cancel');
        }
    );

Route::controller(ReviewController::class)
    ->prefix('review')
    ->name('review.')
    ->group(
        function (): void {
            Route::get('all/{owner_type}/{owner_id}', 'all')->name('all');
            Route::post('create/{owner_type}/{owner_id}', 'create')->name('create');
        }
    );

Route::controller(CourseController::class)
    ->prefix('course')
    ->name('course.')
    ->group(
        function (): void {
            Route::get('all', 'all')->name('all');
            Route::get('find', 'find')->name('find');
            Route::post('attend', 'attend')->name('attend');
            Route::post('cancel', 'cancel')->name('cancel');
        }
    );
