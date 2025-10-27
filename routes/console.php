<?php

declare(strict_types=1);

use App\Console\Commands\CheckCompletedAppointments;
use App\Console\Commands\NotifyCourseCustomer;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckCompletedAppointments::class)
    ->everyThirtyMinutes()
    ->runInBackground();
Schedule::command(NotifyCourseCustomer::class)
    ->dailyAt('00:15')
    ->runInBackground();
