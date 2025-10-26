<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Customer;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/appointment';
});

describe('Appointment Controller Tests', function () {

    it('retrive all appointments for activity', function () {
        $activity = $this->user->activities()->inRandomOrder()->first();
        $activity->appointments()->delete();
        $appointments = Appointment::factory(5)
            ->for($activity, 'holder')
            ->create();

        $response = $this->postJson("{$this->url}/all/activity/{$activity->id}");
        $response->assertOk();
        expect($response->json('payload.appointments'))->not->toBeNull()
            ->and($response->json('payload.appointments'))->toBeIterable()
            ->and($response->json('payload.appointments'))->toHaveCount(count($appointments));
    });

    it('checks available slots for a specific date', function () {
        $activity = $this->user->activities()->inRandomOrder()->first();
        $data = Appointment::factory()->fakerData(owner: $activity);

        $response = $this->postJson("{$this->url}/check", $data);
        $response->assertOk();
        expect($response->json('payload.slots'))->not->toBeNull()
            ->and($response->json('payload.slots'))->toHaveKeys(['day', 'date', 'slots'])
            ->and($response->json('payload.slots.slots'))->toBeIterable();
    });

    it('fails to check slots with invalid data', function () {
        $this->postJson("{$this->url}/check", [])
            ->assertStatus(422);
    });

    it('creates a new appointment with user phone', function () {
        $activity = $this->user->activities()->inRandomOrder()->first();
        $data = Appointment::factory()
            ->fakerData(
                $activity,
                [
                    'time' => '10:30',
                    'customer_phone' => '0987654321'
                ]
            );

        $response = $this->postJson("{$this->url}/create", $data);
        $response->assertOk();

        expect($response->json('payload.appointment'))->not->toBeNull()
            ->and($response->json('payload.appointment.time'))->toBe($data['time'])
            ->and($response->json('payload.appointment.date'))->toBe($data['date']);
    });

    it('creates a new appointment with user id', function () {
        $activity = $this->user->activities()->inRandomOrder()->first();
        $customer = Customer::factory()->create();
        $data = Appointment::factory()
            ->fakerData($activity, ['time' => '10:30', 'customer_id' => $customer->id,]);

        $response = $this->postJson("{$this->url}/create", $data);
        $response->assertOk();
        expect($response->json('payload.appointment'))->not->toBeNull()
            ->and($response->json('payload.appointment.time'))->toBe($data['time'])
            ->and($response->json('payload.appointment.date'))->toBe($data['date'])
            ->and($response->json('payload.appointment.customer'))->not->toBeNull()
            ->and($response->json('payload.appointment.holder'))->not->toBeNull();
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
            'customer_id' => '1'
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
            ->and($response->json('payload.appointment.status'))->toBe(AppointmentStatus::canceled->name);
    });

    it('fails to cancel an already canceled appointment', function () {
        $appointment = Appointment::factory()
            ->for($this->user->activities()->first(), 'holder')
            ->create(['status' => AppointmentStatus::canceled->value]);

        $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ])->assertStatus(400);
    });

    it('fails to cancel an appointment created more than one hour', function () {
        $appointment = Appointment::factory()
            ->for($this->user->activities()->first(), 'holder')
            ->create();
        $appointment->update(['created_at' => $appointment->created_at->subHours(2)]);
        $response = $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ]);
        $response->assertStatus(400);
    });
});
