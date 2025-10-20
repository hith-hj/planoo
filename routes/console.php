<?php

declare(strict_types=1);

use App\Console\Commands\CheckCompletedAppointments;
use App\Console\Commands\NotifyCourseCustomer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckCompletedAppointments::class)
    ->everyThirtyMinutes()
    ->runInBackground();
Schedule::command(NotifyCourseCustomer::class)
    ->dailyAt('00:15')
    ->runInBackground();
