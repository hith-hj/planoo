<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;

use function Pest\Laravel\artisan;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->seed();
});

describe('test CheckCompletedAppointments Command', function () {

    it('uses the end time and completes older dates regardless of clock time', function () {
        $this->travelTo(Carbon::parse('2026-10-18 10:30:00'));
        $older = Appointment::factory()->create(['date' => '2026-10-17', 'time' => '20:00', 'end_at' => '21:00']);
        $ongoing = Appointment::factory()->create(['date' => '2026-10-18', 'time' => '10:00', 'end_at' => '11:00']);
        $ended = Appointment::factory()->create(['date' => '2026-10-18', 'time' => '09:30', 'end_at' => '10:30']);
        artisan('app:cac')->assertExitCode(0);
        expect($older->fresh()->status)->toBe(AppointmentStatus::completed->value);
        expect($ongoing->fresh()->status)->toBe(AppointmentStatus::accepted->value);
        expect($ended->fresh()->status)->toBe(AppointmentStatus::completed->value);
    });

    it('marks past accepted appointments as completed', function () {
        $pastDate = Carbon::now()->subDay()->toDateString();
        $pastTime = Carbon::now()->subHour()->format('H:i');

        $appointment = Appointment::factory()->create([
            'date' => $pastDate,
            'time' => $pastTime,
            'status' => AppointmentStatus::accepted->value,
        ]);

        artisan('app:cac')
            ->expectsOutput('Past accepted appointments marked as completed.')
            ->assertExitCode(0);

        assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::completed->value,
        ]);
    });

    it('does not update future appointments', function () {
        $futureDate = Carbon::now()->addDay()->toDateString();
        $futureTime = Carbon::now()->addHour()->format('H:i');

        $appointment = Appointment::factory()->create([
            'date' => $futureDate,
            'time' => $futureTime,
            'status' => AppointmentStatus::accepted->value,
        ]);

        artisan('app:cac')->assertExitCode(0);

        assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::accepted->value,
        ]);
    });

    it('ignores non-accepted appointments', function () {
        $pastDate = Carbon::now()->subDay()->toDateString();
        $pastTime = Carbon::now()->subHour()->format('H:i');

        $appointment = Appointment::factory()->create([
            'date' => $pastDate,
            'time' => $pastTime,
            'status' => AppointmentStatus::canceled->value,
        ]);

        artisan('app:cac')->assertExitCode(0);

        assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => AppointmentStatus::canceled->value,
        ]);
    });
});
