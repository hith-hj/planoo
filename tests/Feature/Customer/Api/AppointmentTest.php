<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Enums\CodesTypes;
use App\Models\Activity;
use App\Models\Appointment;
use App\Services\CodeServices;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->url = '/api/customer/v1/appointment';
});

describe('Appointment Controller Tests', function () {
    it('returns paginated appointments ', function () {
        Appointment::truncate();
        Appointment::factory(2)->for($this->user, 'customer')->create();
        $res = $this->postJson("{$this->url}/all/activity?page=1&perPage=1");
        $res->assertOk();
        expect($res->json('payload'))->toHaveKeys(['page', 'perPage', 'appointments']);
        expect($res->json('payload.appointments'))->toHaveCount(1)
            ->and($res->json('payload.page'))->toBe(1)
            ->and($res->json('payload.perPage'))->toBe(1);
    });

    it('find appointment by id ', function () {
        Appointment::truncate();
        $appointment = Appointment::factory()->create();
        $res = $this->getJson("{$this->url}/find?appointment_id={$appointment->id}");
        $res->assertOk();
        expect($res->json('payload'))->toHaveKeys(['appointment']);
        expect($res->json('payload.appointment'))->not->toBeNull()
        ->and($res->json('payload.appointment.holder'))->toHaveKeys(['type','id','name','image']);
    });

    it('cant find appointment by invalid id ', function () {
        Appointment::truncate();
        $appointment = Appointment::factory()->create();
        $res = $this->getJson("{$this->url}/find?appointment_id=19090");
        $res->assertStatus(422);
    });

    it('checks available slots for a specific date', function () {
        $activity = Activity::inRandomOrder()->first();
        $data = Appointment::factory()->fakerData(owner: $activity);

        $response = $this->postJson("{$this->url}/check", $data);
        $response->assertOk();
        expect($response->json('payload.slots'))->not->toBeNull()
            ->and($response->json('payload.slots'))->toHaveKeys(['day', 'date', 'slots', 'code'])
            ->and($response->json('payload.slots.slots'))->toBeIterable();
    });

    it('fails to check slots with invalid data', function () {
        $this->postJson("{$this->url}/check", [])->assertStatus(422);
    });

    it('creates a new appointment for customer', function () {
        $activity = Activity::inRandomOrder()->first();
        $data = Appointment::factory()->fakerData($activity, [
            'time' => '10:30',
            'code' => app(CodeServices::class)->createCode(
                CodesTypes::appointment->name,
                timeToExpire: '1:m'
            )
        ]);
        $response = $this->postJson("{$this->url}/create", $data);
        $response->assertOk();

        expect($response->json('payload.appointment'))->not->toBeNull()
            ->and($response->json('payload.appointment.customer.id'))->toBe($this->user->id)
            ->and($response->json('payload.appointment.time'))->toBe($data['time'])
            ->and($response->json('payload.appointment.date'))->toBe($data['date']);
    });

    it('cant creates a new appointment for customer with invalid code', function () {
        $activity = Activity::inRandomOrder()->first();
        $data = Appointment::factory()->fakerData($activity, [
            'time' => '10:30',
            'code' => app(CodeServices::class)->createCode(
                CodesTypes::appointment->name,
                timeToExpire: '-10:s'
            )
        ]);
        $response = $this->postJson("{$this->url}/create", $data);
        dump($response->json());
        $response->assertStatus(400);
    });

    it('fails to create appointment with invalid data', function () {
        $this->postJson("{$this->url}/create", [])
            ->assertStatus(422);
    });

    it('fails to create a duplicate appointment', function () {
        $activity = Activity::inRandomOrder()->first();
        $appointmentData = [
            'date' => now()->tomorrow()->toDateString(),
            'time' => '12:00',
            'end_at' => '14:00',
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
        $appointmentData['code'] = app(CodeServices::class)->createCode(
            CodesTypes::appointment->name,
            timeToExpire: '1:m'
        );

        $this->postJson("{$this->url}/create", $appointmentData)
            ->assertStatus(400);
    });

    it('cancels an accepted appointment', function () {
        $activity = Activity::inRandomOrder()->first();
        $appointment = Appointment::factory()->for($activity, 'holder')
            ->create(['status' => AppointmentStatus::accepted->value]);

        $response = $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ])->assertOk();

        expect($response->json('payload.appointment'))->not->toBeNull()
            ->and($response->json('payload.appointment.status'))->toBe(AppointmentStatus::canceled->name);
    });

    it('fails to cancel an already canceled appointment', function () {
        $activity = Activity::inRandomOrder()->first();
        $appointment = Appointment::factory()->for($activity, 'holder')
            ->create(['status' => AppointmentStatus::canceled->value]);

        $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ])->assertStatus(400);
    });

    it('fails to cancel an appointment created more than one hour', function () {
        $activity = Activity::inRandomOrder()->first();
        $appointment = Appointment::factory()->for($activity, 'holder')->create();
        $appointment->update(['created_at' => $appointment->created_at->subHours(2)]);
        $response = $this->postJson("{$this->url}/cancel", [
            'appointment_id' => $appointment->id,
        ]);
        $response->assertStatus(400);
    });
});
