<?php

declare(strict_types=1);

use App\Console\Commands\CheckCompletedAppointments;
use App\Console\Commands\DeleteExpiredCodes;
use App\Console\Commands\NotifyCourseBegun;
use App\Console\Commands\NotifyCourseSession;
use App\Console\Commands\NotifyEventBegun;
use App\Console\Commands\NotifyEventSession;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckCompletedAppointments::class)
    ->everyThirtyMinutes()
    ->runInBackground();
Schedule::command(DeleteExpiredCodes::class)
    ->dailyAt('02:00')
    ->runInBackground();
Schedule::command(NotifyCourseBegun::class)
    ->dailyAt('02:00')
    ->runInBackground();
Schedule::command(NotifyCourseSession::class)
    ->dailyAt('02:00')
    ->runInBackground();
Schedule::command(NotifyEventBegun::class)
    ->dailyAt('02:00')
    ->runInBackground();
Schedule::command(NotifyEventSession::class)
    ->dailyAt('02:00')
    ->runInBackground();
