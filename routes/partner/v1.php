<?php

declare(strict_types=1);

use App\Http\Controllers\Partner\Api\ActivityController;
use App\Http\Controllers\Partner\Api\AppointmentController;
use App\Http\Controllers\Partner\Api\AuthController;
use App\Http\Controllers\Partner\Api\DayController;
use App\Http\Controllers\Partner\Api\LocationsController;
use App\Http\Controllers\Partner\Api\MediaController;
use App\Http\Controllers\Partner\Api\ReviewController;
use App\Http\Controllers\Partner\Api\TagController;
use App\Http\Controllers\Partner\Api\UserController;
use App\Http\Middleware\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'auth',
        'controller' => AuthController::class,
    ],
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

Route::group(
    [
        'prefix' => 'user',
        'controller' => UserController::class,
        'name' => 'user.',
    ],
    function (): void {
        Route::get('get', 'get')->name('get');
        Route::post('update', 'update')->name('update');
        Route::post('uploadProfileImage', 'uploadProfileImage')->name('uploadProfileImage');
        Route::post('deleteProfileImage', 'deleteProfileImage')->name('deleteProfileImage');
        Route::delete('delete', 'delete')->name('delete');
    }
);

Route::group(
    [
        'prefix' => 'activity',
        'controller' => ActivityController::class,
        'name' => 'activity.',
    ],
    function (): void {
        Route::get('all', 'all')->name('all');
        Route::get('find', 'find')->name('find');
        Route::post('create', 'create')->name('create');
        Route::patch('update', 'update')->name('update');
        Route::delete('delete', 'delete')->name('delete');
        Route::post('toggleActivation', 'toggleActivation')->name('toggleActivation');
    }
);

Route::group(
    [
        'prefix' => 'day',
        'controller' => DayController::class,
        'name' => 'day.',
    ],
    function (): void {
        Route::get('all/{owner_type}/{owner_id}', 'all')->name('all');
        Route::get('find/{owner_type}/{owner_id}', 'find')->name('find');
        Route::post('create/{owner_type}/{owner_id}', 'create')->name('create');
        Route::post('createMany/{owner_type}/{owner_id}', 'createMany')->name('createMany');
        Route::patch('update/{owner_type}/{owner_id}', 'update')->name('update');
        Route::delete('delete/{owner_type}/{owner_id}', 'delete')->name('delete');
        Route::post('toggleActivation/{owner_type}/{owner_id}', 'toggleActivation')->name('toggleActivation');
    }
);

Route::group(
    [
        'prefix' => 'location',
        'controller' => LocationsController::class,
        'name' => 'location.',
    ],
    function (): void {
        Route::get('get/{owner_type}/{owner_id}', 'get')->name('get');
        Route::post('create/{owner_type}/{owner_id}', 'create')->name('create');
        Route::patch('update/{owner_type}/{owner_id}', 'update')->name('update');
        Route::delete('delete/{owner_type}/{owner_id}', 'delete')->name('delete');
    }
);

Route::group(
    [
        'prefix' => 'tag',
        'controller' => TagController::class,
        'name' => 'tag.',
    ],
    function (): void {
        Route::get('all/{owner_type}/{owner_id}', 'all')->name('all');
        Route::post('create/{owner_type}/{owner_id}', 'create')->name('create');
        Route::delete('delete/{owner_type}/{owner_id}', 'delete')->name('delete');
    }
);

Route::group(
    [
        'prefix' => 'media',
        'controller' => MediaController::class,
        'name' => 'media.',
    ],
    function (): void {
        Route::get('all/{owner_type}/{owner_id}', 'all')->name('all');
        Route::post('create/{owner_type}/{owner_id}', 'create')->name('create');
        Route::delete('delete/{owner_type}/{owner_id}', 'delete')->name('delete');
    }
);

Route::group(
    [
        'prefix' => 'appointment',
        'controller' => AppointmentController::class,
        'name' => 'appointment.',
    ],
    function (): void {
        Route::post('all/{owner_type?}/{owner_id?}', 'all')->name('all');
        Route::post('check', 'check')->name('check');
        Route::post('create', 'create')->name('create');
        Route::post('cancel', 'cancel')->name('cancel');
    }
);

Route::group(
    [
        'prefix' => 'review',
        'controller' => ReviewController::class,
        'name' => 'review.',
    ],
    function (): void {
        Route::get('all/{owner_type}/{owner_id}', 'all')->name('all');
        Route::post('create/{owner_type}/{owner_id}', 'create')->name('create');
    }
);
