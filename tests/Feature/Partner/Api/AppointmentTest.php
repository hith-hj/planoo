<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;


beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/appointment';
});

describe('media Controller tests', function () {
    it('can check available slots for appointment in specific date', function () {
        $data = Appointment::factory()->fakerData();
        $res = $this->postJson("$this->url/check", $data)->assertOk();
        expect($res->json('payload.slots'))->not->toBeNull()
            ->and($res->json('payload.slots'))->toHaveKeys(['day', 'date', 'slots'])
            ->and($res->json('payload.slots.slots'))->toBeIterable();
    });

    it('cant check available slots for appointment with invalid info', function () {
        $this->postJson("$this->url/check", [])->assertStatus(422);
    });

    it('can create appointment', function () {
        $data = Appointment::factory()->fakerData(['time' => '10:30']);
        $res = $this->postJson("$this->url/create", $data)->assertOk();
        expect($res->json('payload.appointment'))->not->toBeNull()
            ->and($res->json('payload.appointment.time'))->toBe($data['time'])
            ->and($res->json('payload.appointment.date'))->toBe($data['date']);
    });

    it('cant create appointment with invalid data', function () {
        $this->postJson("$this->url/create", [])->assertStatus(422);
    });

    it('cant create duplicate appointment', function () {
        $activity = $this->user->activities->first();

        $appointmentData = [
            'date' => '2025-10-10',
            'time' => '12:00',
            'session_duration' => 120,
            'notes' => 'Recusandae et quis voluptatibus.',
        ];

        $activity->appointments()->create([
            ...$appointmentData,
            'status' => AppointmentStatus::accepted,
            'price' => $activity->price,
        ]);

        $appointmentData['activity_id'] = $activity->id;
        $appointmentData['day_id'] = 1;

        $res = $this->postJson("$this->url/create", $appointmentData)->assertStatus(400);
        $res->assertJson([
            'success' => false,
            'message' => 'Appointment just got booked',
        ]);
    });
});
