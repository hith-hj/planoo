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
