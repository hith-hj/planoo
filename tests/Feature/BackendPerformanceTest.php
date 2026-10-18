<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Activity;
use App\Models\Appointment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
});

it('rejects oversized or invalid page requests across list endpoints', function (string $endpoint, array $parameters) {
    if ($endpoint === 'court') {
        $this->getJson(route('customer.court.all', $parameters))->assertUnprocessable();
    } else {
        $this->postJson(route('customer.'.$endpoint.'.all', $endpoint === 'appointment' ? ['owner_type' => 'activity'] : []), $parameters)->assertUnprocessable();
    }
})->with(['activity', 'course', 'event', 'court', 'appointment'])->with([
    [['perPage' => 101]], [['perPage' => 0]], [['perPage' => -1]], [['page' => 0]],
]);

it('pages search results without changing the result array format', function () {
    Activity::query()->update(['name' => 'Matched search']);
    $first = $this->getJson(route('customer.home.search', ['owner' => 'activity', 'search' => 'Matched', 'perPage' => 1]))->assertOk();
    $second = $this->getJson(route('customer.home.search', ['owner' => 'activity', 'search' => 'Matched', 'perPage' => 1, 'page' => 2]))->assertOk();
    expect($first->json('payload.result'))->toHaveCount(1);
    expect($second->json('payload.result'))->toHaveCount(1);
    expect($first->json('payload.result.0.id'))->not->toBe($second->json('payload.result.0.id'));
    $this->getJson(route('customer.home.search', ['owner' => 'activity', 'perPage' => 101]))->assertUnprocessable();
});

it('rejects overflowing unpaginated appointment lists instead of returning partial data', function () {
    $activity = Activity::firstOrFail();
    Appointment::query()->delete();
    $base = Appointment::factory()->make(['customer_id' => $this->user->id,
        'appointable_type' => Activity::class, 'appointable_id' => $activity->id,
        'status' => AppointmentStatus::accepted->value])->getAttributes();
    $rows = [];
    for ($i = 0; $i < 501; $i++) {
        $rows[] = [...$base, 'id' => (string) Str::ulid()];
    }
    DB::table('appointments')->insert($rows);
    $this->postJson(route('customer.appointment.all', ['owner_type' => 'activity']), ['paginate' => false, 'filters' => ['status' => AppointmentStatus::accepted->value]])
        ->assertUnprocessable()->assertJsonValidationErrors('paginate', 'payload.errors');
    $this->postJson(route('customer.appointment.accepted', ['owner_type' => 'activity']))->assertUnprocessable();
    $this->postJson(route('customer.appointment.all', ['owner_type' => 'activity']), ['perPage' => 10])->assertOk();
});

it('installs and rolls back query indexes without changing booking data', function () {
    $count = Appointment::count();
    $migration = require database_path('migrations/2026_10_18_000003_add_booking_query_indexes.php');
    expect(Schema::hasIndex('appointments', 'appointments_owner_slot_index'))->toBeTrue();
    $migration->down();
    expect(Schema::hasIndex('appointments', 'appointments_owner_slot_index'))->toBeFalse();
    $migration->up();
    expect(Schema::hasIndex('appointments', 'appointments_customer_slot_index'))->toBeTrue();
    expect(Schema::hasIndex('appointments', 'appointments_completion_index'))->toBeTrue();
    expect(Schema::hasIndex('notifications', 'notifications_recipient_unread_index'))->toBeTrue();
    expect(Appointment::count())->toBe($count);
});

