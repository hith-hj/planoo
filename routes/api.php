<?php

declare(strict_types=1);

use App\Http\Controllers\LabelController;
use App\Http\Controllers\WhatsAppWebhooksController;
use App\Http\Middleware\Auth\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::controller(LabelController::class)
    ->prefix('label')
    ->name('label.')
    ->middleware(['throttle:50,1'])
    ->group(
        function (): void {
            Route::get('tags', 'tags')->name('tags');
            Route::get('weekDays', 'weekDays')->name('weekDays');
            Route::get('categories', 'categories')->name('categories');
            Route::get('usersTypes', 'usersTypes')->name('usersTypes');
            Route::get('activityTypes', 'activityTypes')->name('activityTypes');
            Route::get('courseDuration', 'courseDuration')->name('courseDuration');
            Route::get('sessionDuration', 'sessionDuration')->name('sessionDuration');
        }
    );

Route::middleware([JwtMiddleware::class, 'throttle:api'])->group(function () {
    Route::prefix('partner')->name('partner.')->group(function (): void {
        Route::prefix('v1')->group(function (): void {
            require 'partner/v1.php';
        });
    });

    Route::prefix('customer')->name('customer.')->group(function (): void {
        Route::prefix('v1')->group(function (): void {
            require 'customer/v1.php';
        });
    });
});

Route::controller(WhatsAppWebhooksController::class)
    ->prefix('webhooks')
    ->middleware(['throttle:120,1'])
    ->group(function () {
        Route::get('whatsapp', 'verify');
        Route::post('whatsapp', 'handle');
    });
