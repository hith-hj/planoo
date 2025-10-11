<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Appointment;


beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/appointment';
});

describe('Appointment Controller Tests', function () {
    it('checks available slots for a specific date', function () {
        $data = Appointment::factory()->fakerData();

        $response = $this->postJson("{$this->url}/check", $data)
            ->assertOk();

        expect($response->json('payload.slots'))->not->toBeNull()
            ->and($response->json('payload.slots'))->toHaveKeys(['day', 'date', 'slots'])
            ->and($response->json('payload.slots.slots'))->toBeIterable();
    });

    it('fails to check slots with invalid data', function () {
        $this->postJson("{$this->url}/check", [])
            ->assertStatus(422);
    });

    it('creates a new appointment', function () {
        $data = Appointment::factory()->fakerData(['time' => '10:30']);

        $response = $this->postJson("{$this->url}/create", $data)
            ->assertOk();

        expect($response->json('payload.appointment'))->not->toBeNull()
            ->and($response->json('payload.appointment.time'))->toBe($data['time'])
            ->and($response->json('payload.appointment.date'))->toBe($data['date']);
    });

    it('fails to create appointment with invalid data', function () {
        $this->postJson("{$this->url}/create", [])
            ->assertStatus(422);
    });

    it('fails to create a duplicate appointment', function () {
        $activity = $this->user->activities->first();

        $appointmentData = [
            'date' => now()->tomorrow()->toDateString(),
            'time' => '12:00',
            'session_duration' => 120,
            'notes' => 'Recusandae et quis voluptatibus.',
        ];

        $activity->appointments()->create([
            ...$appointmentData,
            'status' => AppointmentStatus::accepted->value,
            'price' => $activity->price,
        ]);

        $appointmentData['activity_id'] = $activity->id;
        $appointmentData['day_id'] = 1;

        $this->postJson("{$this->url}/create", $appointmentData)
            ->assertStatus(400);
    });

    it('cancels an accepted appointment', function () {
        $appointment = Appointment::factory()
            ->for($this->user->activities()->first(), 'holder')
            ->create(['status' => AppointmentStatus::accepted->value]);

        $response = $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ])->assertOk();

        expect($response->json('payload.appointment'))->not->toBeNull()
            ->and($response->json('payload.appointment.status'))->toBe(AppointmentStatus::canceled->value);
    });

    it('fails to cancel an already canceled appointment', function () {
        $appointment = Appointment::factory()
            ->for($this->user->activities()->first(), 'holder')
            ->create(['status' => AppointmentStatus::canceled->value]);

        $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ])->assertStatus(400);
    });
});
