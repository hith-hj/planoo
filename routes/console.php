<?php

declare(strict_types=1);

use App\Console\Commands\CheckCompletedAppointments;
use App\Console\Commands\NotifyCourseCustomer;
use App\Console\Commands\NotifyEventSession;
use App\Console\Commands\NotifyEventStart;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckCompletedAppointments::class)
    ->everyThirtyMinutes()
    ->runInBackground();
Schedule::command(NotifyCourseCustomer::class)
    ->dailyAt('02:00')
    ->runInBackground();
Schedule::command(NotifyEventStart::class)
    ->dailyAt('02:00')
    ->runInBackground();
Schedule::command(NotifyEventSession::class)
    ->dailyAt('02:00')
    ->runInBackground();
