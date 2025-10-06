<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function(){
    $this->seed();
    $this->url = '/api/partner/v1/auth';
});

describe('UserAuth Controller test', function () {

    it('registers_a_user', function () {
        $data = User::factory()->password()->make()->toArray();
        $res = $this->postJson(route('register'), $data);
        expect($res->status())->toBe(201)
        ->and($res->json('success'))->toBe(true);
    });

    it('fails_to_registers_a_user', function () {
        $data = User::factory()->make(['acount_type'=>'kiki'])->toArray();
        $res = $this->postJson(route('register'), $data);
        expect($res->status())->toBe(422);
        expect($res->json('success'))->toBe(false);
    });

    it('verifies_user_successfully', function () {
        $user = User::factory()->create(['phone' => '0912345678']);
        $user->createCode('verification');
        $code = $user->code('verification')->code;
        expect($code)->not->toBeNull()->and($code)->toBeInt();

        $res = $this->postJson(route('verify'), [
            'phone' => $user->phone,
            'code' => $code,
        ]);
        expect($res->status())->toBe(200);
        expect($res->json('message'))->toBe(__('verified'));
    });

    it('fails_to_verify_with_incorrect_code', function () {
        $user = User::factory()->create();
        $user->createCode('verification');
        $res = $this->postJson(route('verify'), [
            'phone' => $user->phone,
            'code' => 00000,
        ]);
        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->not->toBeNull();
    });

    it('allow_verified_user_login', function () {
        $user = User::factory()->create(['phone' => '0911112222']);
        $res = $this->postJson(route('login'), [
            'phone' => $user->phone,
            'password' => 'password',
        ]);
        expect($res->status())->toBe(200);
        expect($res->json('payload'))->toHaveKeys(['user', 'token']);
    });

    it('prevent_unverified_user_login', function () {
        $user = User::factory()->create([
            'phone' => '0911112222',
            'verified_at' => null,
        ]);

        $res = $this->postJson(route('login'), [
            'phone' => $user->phone,
            'password' => 'password',
        ]);
        expect($res->status())->toBe(400);
    });

    it('fails_to_login_with_invalid_credentials', function () {
        $res = $this->postJson(route('login'), [
            'phone' => '0912345678',
            'password' => 'wrongpassword',
        ]);
        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->not->toBeNull();
    });

    it('fails_to_login_with_incorrect_credentials', function () {
        $user = User::factory()->create(['phone' => '0911112222']);
        $res = $this->postJson(route('login'), [
            'phone' => $user->phone,
            'password' => 'wrongpassword',
        ]);
        expect($res->status())->toBe(400);
    });

    it('can logout user', function () {
        $this->user('partner', 'stadium')->api();
        $res = $this->postJson(route('logout'));
        $res->assertOk();
    });
});
