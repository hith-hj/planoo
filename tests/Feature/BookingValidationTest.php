<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Code;
use App\Models\Customer;
use App\Services\AppointmentServices;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    Appointment::query()->delete();
    $this->activity = Activity::firstOrFail();
    $this->activity->update(['is_active' => true]);
    $day = $this->activity->days()->firstOrFail();
    $day->update(['is_active' => true, 'start' => '09:00', 'end' => '17:00']);
    $this->booking = [
        'activity_id' => $this->activity->id,
        'day_id' => $day->id,
        'date' => now()->addWeek()->next($day->day)->toDateString(),
        'session_duration' => 60,
        'time' => '09:00',
    ];
    $response = $this->postJson(route('customer.appointment.check'), $this->booking)->assertOk();
    $this->booking['code'] = $response->json('payload.slots.code');
});

it('consumes a booking code only after successful creation and rejects replay', function () {
    $this->postJson(route('customer.appointment.create'), $this->booking)->assertOk();
    $this->assertDatabaseMissing('codes', ['id' => $this->booking['code']]);
    $this->postJson(route('customer.appointment.create'), $this->booking)->assertStatus(400);
    expect($this->activity->appointments()->count())->toBe(1);
});

it('accepts offered quarter-hour, half-hour and hourly slots', function (int $duration) {
    $data = [...$this->booking, 'session_duration' => $duration];
    $quote = $this->postJson(route('customer.appointment.check'), $data)->assertOk();
    $data['code'] = $quote->json('payload.slots.code');
    $data['time'] = $quote->json('payload.slots.slots.1.start');
    $this->postJson(route('customer.appointment.create'), $data)->assertOk();
})->with([15, 30, 60]);

it('rejects altered booking context without consuming the code', function (string $field) {
    $data = $this->booking;
    $data[$field] = match ($field) {
        'activity_id' => Activity::whereKeyNot($this->activity->id)->firstOrFail()->id,
        'day_id' => Activity::whereKeyNot($this->activity->id)->firstOrFail()->days()->firstOrFail()->id,
        'date' => now()->parse($data['date'])->addWeek()->toDateString(),
        'session_duration' => 120,
        'time' => '08:00',
    };
    $this->postJson(route('customer.appointment.create'), $data)->assertStatus(400);
    $this->assertDatabaseHas('codes', ['id' => $data['code']]);
    expect($this->activity->appointments()->count())->toBe(0);
})->with(['activity_id', 'day_id', 'date', 'session_duration', 'time']);

it('rejects using another customer booking code', function () {
    $this->replaceUser(Customer::factory()->create());
    $this->postJson(route('customer.appointment.create'), $this->booking)->assertStatus(400);
});

it('rejects expired codes and hours closed after checking availability', function (string $change) {
    if ($change === 'expired') {
        Code::findOrFail($this->booking['code'])->update(['expire_at' => now()->subMinute()]);
    } else {
        $this->activity->days()->findOrFail($this->booking['day_id'])->update(['start' => '10:00']);
    }
    $this->postJson(route('customer.appointment.create'), $this->booking)->assertStatus(400);
    expect($this->activity->appointments()->count())->toBe(0);
})->with(['expired', 'closed']);

it('rechecks conflicts after issuing a booking code', function () {
    Appointment::factory()->for($this->activity, 'holder')->create([
        'date' => $this->booking['date'], 'time' => '09:30', 'end_at' => '10:30',
        'session_duration' => 60, 'status' => AppointmentStatus::accepted->value,
    ]);
    $this->postJson(route('customer.appointment.create'), $this->booking)->assertStatus(400);
    $this->assertDatabaseHas('codes', ['id' => $this->booking['code']]);
    expect($this->activity->appointments()->count())->toBe(1);
});

it('serializes eagerly loaded appointments without extra database queries', function () {
    $this->postJson(route('customer.appointment.create'), $this->booking)->assertOk();
    $appointment = app(AppointmentServices::class)->findForActor($this->user, $this->activity->appointments()->firstOrFail()->id);
    DB::enableQueryLog();
    DB::flushQueryLog();
    try {
        $appointment->toResource()->response()->getContent();
        expect(DB::getQueryLog())->toBeEmpty();
    } finally {
        DB::disableQueryLog();
    }
});
