<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->seed();
});

/**
 * Covers improvement report 4.3: JWT failures must return a generic error to
 * the client; internal token details stay in the server log only.
 */
it('returns a generic authentication error for a missing token', function () {
    Log::spy();

    $res = $this->getJson(route('partner.user.get'));

    expect($res->status())->toBe(401)
        ->and($res->json('success'))->toBeFalse()
        ->and($res->json('message'))->toBe(__('Token Authentication Error'));

    Log::shouldHaveReceived('error')->once();
});

it('returns a generic authentication error for a malformed token', function () {
    Log::spy();

    $res = $this->withHeaders(['Authorization' => 'Bearer not.a.real.jwt'])
        ->getJson(route('partner.user.get'));

    expect($res->status())->toBe(401)
        ->and($res->json('message'))->toBe(__('Token Authentication Error'))
        // the raw exception internals must never reach the client
        ->and($res->json('message'))->not->toContain('token could not be parsed');
});

it('authenticates a valid partner token', function () {
    $user = User::factory()->create();

    $this->user = $user;
    auth()->shouldUse('partner:api');

    $res = $this->api()->getJson(route('partner.user.get'));

    expect($res->status())->toBe(200)
        ->and($res->json('success'))->toBeTrue();
});
