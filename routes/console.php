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
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(DeleteExpiredCodes::class)
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(NotifyCourseBegun::class)
    ->dailyAt('01:10')
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(NotifyCourseSession::class)
    ->dailyAt('01:20')
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(NotifyEventBegun::class)
    ->dailyAt('01:30')
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(NotifyEventSession::class)
    ->dailyAt('01:40')
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(ResourceConflictDetector::class)
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command('queue:work --tries=3 --timeout=60 --max-time=300 --stop-when-empty')
    ->everyThirtySeconds()
    ->withoutOverlapping(10)
    ->appendOutputTo(storage_path('logs/queue-worker.log'))
    ->runInBackground();
