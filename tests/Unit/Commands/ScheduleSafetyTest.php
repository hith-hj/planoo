<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Enums\EventStatus;
use App\Models\Course;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event as EventDispatcher;

beforeEach(function () {
    $this->seed();
    Carbon::setTestNow('2026-05-19 08:00:00');
    $this->scheduledOwner = function (string $type, bool $beginning) {
        $model = $type === 'course' ? Course::class : Event::class;
        $status = $type === 'course'
            ? ($beginning ? CourseStatus::pending->value : CourseStatus::active->value)
            : ($beginning ? EventStatus::pending->value : EventStatus::active->value);
        $owner = $model::factory()->create([
            'start_date' => today()->toDateString(), 'status' => $status, 'is_active' => true,
        ]);
        $owner->appointments()->delete();
        $owner->days()->delete();
        $owner->days()->create(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00']);

        return $owner;
    };
});

afterEach(function () {
    Carbon::setTestNow();
});

it('skips inactive days for session and beginning commands', function (string $type, string $command, bool $beginning) {
    $owner = ($this->scheduledOwner)($type, $beginning);
    $owner->days()->update(['is_active' => false]);

    Artisan::call($command, ['date' => today()->toDateString()]);

    expect($owner->appointments()->count())->toBe(0);
})->with([
    ['course', 'app:ncs', false], ['event', 'app:nes', false],
    ['course', 'app:ncb', true], ['event', 'app:neb', true],
]);

it('rechecks the schedule after locking instead of using cached day data', function (string $type, string $change) {
    $owner = ($this->scheduledOwner)($type, false);
    $model = $owner::class;
    $retrievals = 0;
    $model::retrieved(function ($lockedOwner) use ($owner, &$retrievals, $change): void {
        if ($lockedOwner->id !== $owner->id || ++$retrievals !== 2) {
            return;
        }
        // Simulate a schedule change between the initial scan and lock acquisition.
        $days = $lockedOwner->days()->where('day', 'tuesday');
        match ($change) {
            'time' => $days->update(['start' => '14:00', 'end' => '15:00']),
            'disabled' => $days->update(['is_active' => false]),
            'removed' => $days->delete(),
        };
    });

    try {
        Artisan::call($type === 'course' ? 'app:ncs' : 'app:nes', ['date' => today()->toDateString()]);
        if ($change === 'time') {
            expect($owner->appointments()->sole()->time)->toStartWith('14:00');
        } else {
            expect($owner->appointments()->count())->toBe(0);
        }
    } finally {
        EventDispatcher::forget('eloquent.retrieved: '.$model);
    }
})->with(['course', 'event'])->with(['time', 'disabled', 'removed']);

it('creates overnight sessions with a positive duration', function (string $type, string $command, bool $beginning) {
    $owner = ($this->scheduledOwner)($type, $beginning);
    $owner->days()->update(['start' => '22:00', 'end' => '02:00']);

    Artisan::call($command, ['date' => today()->toDateString()]);

    $session = $owner->appointments()->sole();
    expect($session->session_duration)->toBe(240)
        ->and($session->end_at)->toStartWith('02:00');
})->with([
    ['course', 'app:ncs', false], ['event', 'app:nes', false],
    ['course', 'app:ncb', true], ['event', 'app:neb', true],
]);
