<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Enums\EventStatus;
use App\Jobs\DeliverNotification;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Event;
use App\Models\User;
use App\Services\CourseServices;
use App\Services\EventServices;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed();
});

it('rejects existing tokens for disabled or unverified accounts', function (string $role, string $field) {
    $this->user($role, 'stadium')->api();
    $this->user->update([$field => $field === 'is_active' ? false : null]);
    $route = $role === 'partner' ? 'partner.user.get' : 'customer.customer.get';
    $this->getJson(route($route))->assertForbidden();
})->with(['customer', 'partner'])->with(['is_active', 'verified_at']);

it('checks real capacity and serializes enrollment changes despite stale models and flags', function (string $type) {
    $owner = $type === 'course' ? Course::firstOrFail() : Event::firstOrFail();
    $service = app($type === 'course' ? CourseServices::class : EventServices::class);
    $owner->customers()->detach();
    $owner->update(['capacity' => 1, 'is_active' => true, 'is_full' => false,
        'status' => $type === 'course' ? CourseStatus::pending->value : EventStatus::pending->value]);
    $first = Customer::factory()->create();
    $second = Customer::factory()->create();
    $service->attend($first, $owner);
    expect($owner->fresh()->is_full)->toBeTruthy();
    $owner->fresh()->update(['is_full' => false]);
    expect(fn () => $service->attend($second, $owner))->toThrow(Exception::class);
    expect($owner->customers()->count())->toBe(1);
    $service->cancel($first, $owner);
    $service->attend($second, $owner);
    expect($owner->customers()->whereKey($second->id)->exists())->toBeTrue();
    expect(fn () => $service->attend($second, $owner))->toThrow(Exception::class);
})->with(['course', 'event']);

it('enforces unique enrollment pairs in the database', function (string $type) {
    $owner = $type === 'course' ? Course::firstOrFail() : Event::firstOrFail();
    $customer = Customer::factory()->create();
    $pivot = $type === 'course' ? ['remaining_sessions' => 1, 'is_complete' => false] : [];
    $owner->customers()->attach($customer->id, $pivot);
    expect(fn () => DB::transaction(fn () => $owner->customers()->attach($customer->id, $pivot)))
        ->toThrow(QueryException::class);
})->with(['course', 'event']);

it('rejects unsigned or incorrectly signed WhatsApp callbacks', function (?string $signature) {
    config(['services.whatsapp.app_secret' => 'test-secret']);
    $headers = $signature ? ['X-Hub-Signature-256' => $signature] : [];
    $this->postJson('/api/webhooks/whatsapp', ['entry' => []], $headers)->assertForbidden();
})->with([null, 'sha256=invalid']);

it('accepts only signatures of the exact raw webhook body', function () {
    config(['services.whatsapp.app_secret' => 'test-secret']);
    $body = '{"entry": []}';
    $signature = 'sha256='.hash_hmac('sha256', $body, 'test-secret');
    $server = ['CONTENT_TYPE' => 'application/json', 'HTTP_X_HUB_SIGNATURE_256' => $signature];
    $this->call('POST', '/api/webhooks/whatsapp', [], [], [], $server, $body)->assertOk();
    $this->call('POST', '/api/webhooks/whatsapp', [], [], [], $server, '{"entry":[]}')->assertForbidden();
});

it('fails closed without webhook secrets and verifies configured challenges', function () {
    config(['services.whatsapp.app_secret' => null, 'services.whatsapp.verify_token' => null]);
    $this->postJson('/api/webhooks/whatsapp', [])->assertForbidden();
    $this->get('/api/webhooks/whatsapp?hub_mode=subscribe')->assertForbidden();
    config(['services.whatsapp.verify_token' => 'test-verify']);
    $this->get('/api/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=test-verify&hub_challenge=123')
        ->assertOk()->assertSeeText('123');
});

it('queues encrypted notifications only after commit and discards rolled back deliveries', function () {
    $customer = Customer::factory()->create();
    config(['queue.default' => 'database', 'services.notifications.queued' => true]);
    app()->instance('env', 'production');
    try {
        DB::beginTransaction();
        $customer->notify('Rollback', 'Should not send');
        expect(DB::table('jobs')->count())->toBe(0);
        DB::rollBack();
        expect(DB::table('jobs')->count())->toBe(0);
        DB::transaction(fn () => $customer->notify('Accepted', 'Deliver after commit'));
        expect(DB::table('jobs')->count())->toBe(1);
        $payload = json_decode(DB::table('jobs')->value('payload'), true);
        expect($payload['data']['command'])->not->toContain('Deliver after commit');
    } finally {
        app()->instance('env', 'testing');
    }
});

it('delivers queued notifications without queueing them again', function () {
    $customer = Customer::factory()->create();
    (new DeliverNotification($customer, 'Test title', 'Test body', [], 'fcm'))->handle();
    $this->assertDatabaseHas('notifications', ['belongTo_id' => $customer->id, 'title' => 'Test title']);
});

it('clears only the rejected Firebase token and preserves a refreshed token', function () {
    $customer = Customer::factory()->create(['firebase_token' => 'old-token']);
    $method = new ReflectionMethod(Customer::class, 'handleInvalidToken');
    Customer::whereKey($customer->id)->update(['firebase_token' => 'new-token']);
    $method->invoke($customer, 'old-token', 'unregistered');
    expect($customer->fresh()->firebase_token)->toBe('new-token');
    $method->invoke($customer, 'new-token', 'unregistered');
    expect($customer->fresh()->firebase_token)->toBeNull();
});

it('hides unexpected exception details while preserving domain validation messages', function () {
    Route::get('/api/test-internal-error', fn () => throw new RuntimeException('SQL secret connection details'));
    Route::get('/api/test-type-error', fn () => throw new TypeError('Internal class details'));
    Route::get('/api/test-domain-error', fn () => throw new DomainException('Invalid booking slot'));
    $this->getJson('/api/test-internal-error')->assertStatus(500)->assertJson(['message' => __('Internal server error.')]);
    $this->getJson('/api/test-type-error')->assertStatus(500)->assertJson(['message' => __('Internal server error.')]);
    $this->get('/api/test-internal-error')->assertStatus(500)->assertJson(['message' => __('Internal server error.')]);
    $this->getJson('/api/test-domain-error')->assertStatus(400)->assertJson(['message' => __('Invalid booking slot')]);
});

it('excludes credentials from default account serialization', function (string $role) {
    $model = $role === 'customer' ? Customer::class : User::class;
    $account = $model::factory()->make(['password' => 'secret-password', 'firebase_token' => 'secret-token']);
    expect($account->toArray())->not->toHaveKeys(['password', 'firebase_token', 'remember_token']);
    expect($account->password)->toBe('secret-password');
})->with(['customer', 'partner']);
