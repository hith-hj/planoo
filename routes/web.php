<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/terms', 'terms')->name('terms');
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }

    return redirect()->back();
})->name('lang');
