<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return Success('You reached planoo platform');
// });

Route::view('/','home');
Route::view('/two','home_2');
