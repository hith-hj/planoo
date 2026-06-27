<?php

declare(strict_types=1);

use App\Console\Commands\CheckCompletedAppointments;
use App\Console\Commands\DeleteExpiredCodes;
use App\Console\Commands\NotifyCourseBegun;
use App\Console\Commands\NotifyCourseSession;
use App\Console\Commands\NotifyEventBegun;
use App\Console\Commands\NotifyEventSession;
use App\Console\Commands\ResourceConflictDetector;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckCompletedAppointments::class)
    ->everyThirtyMinutes()
    ->runInBackground();
Schedule::command(DeleteExpiredCodes::class)
    ->dailyAt('01:00')
    ->runInBackground();
Schedule::command(NotifyCourseBegun::class)
    ->dailyAt('01:10')
    ->runInBackground();
Schedule::command(NotifyCourseSession::class)
    ->dailyAt('01:20')
    ->runInBackground();
Schedule::command(NotifyEventBegun::class)
    ->dailyAt('01:30')
    ->runInBackground();
Schedule::command(NotifyEventSession::class)
    ->dailyAt('01:40')
    ->runInBackground();
Schedule::command(ResourceConflictDetector::class)
    ->dailyAt('01:50')
    ->runInBackground();
Schedule::command('queue:work --stop-when-empty')
    ->dailyAt('00:01')
    ->runInBackground();
