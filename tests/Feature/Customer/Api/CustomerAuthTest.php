<?php

declare(strict_types=1);

use App\Models\Customer;

beforeEach(function () {
    $this->seed();
    $this->url = '/api/customer/v1/auth';
});

describe('CustomerAuth Controller test', function () {

    it('registers_a_customer', function () {
        Customer::truncate();
        $data = Customer::factory()->password()->make()->toArray();
        $res = $this->postJson(route('customer.register'), $data);
        $res->assertOk();
        $this->assertDatabaseCount('customers', 1);
    });

    it('fails_to_registers_a_customer', function () {
        $data = Customer::factory()->make()->toArray();
        $res = $this->postJson(route('customer.register'), $data);
        expect($res->status())->toBe(422);
        expect($res->json('success'))->toBe(false);
    });

    it('verifies_customer_successfully', function () {
        $customer = Customer::factory()->create(['phone' => '0912345678']);
        $customer->createCode('verification');
        $code = $customer->code('verification')->code;
        expect($code)->not->toBeNull()->and($code)->toBeInt();

        $res = $this->postJson(route('customer.verify'), [
            'phone' => $customer->phone,
            'code' => $code,
        ]);
        $res->assertOk();
        expect($res->json('message'))->toBe(__('verified'));
    });

    it('fails_to_verify_with_incorrect_code', function () {
        $customer = Customer::factory()->create();
        $customer->createCode('verification');
        $res = $this->postJson(route('customer.verify'), [
            'phone' => $customer->phone,
            'code' => 00000,
        ]);
        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->not->toBeNull();
    });

    it('allow_verified_customer_login', function () {
        $customer = Customer::factory()->create(['phone' => '0911112222']);
        $res = $this->postJson(route('customer.login'), [
            'phone' => $customer->phone,
            'password' => 'password',
        ]);
        $res->assertOk();
        expect($res->json('payload'))->toHaveKeys(['customer', 'token']);
    });

    it('prevent_unverified_customer_login', function () {
        $customer = Customer::factory()->create([
            'phone' => '0911112222',
            'verified_at' => null,
        ]);

        $res = $this->postJson(route('customer.login'), [
            'phone' => $customer->phone,
            'password' => 'password',
        ]);
        expect($res->status())->toBe(400);
    });

    it('fails_to_login_with_invalid_credentials', function () {
        $res = $this->postJson(route('customer.login'), [
            'phone' => '0912345678',
            'password' => 'wrongpassword',
        ]);
        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->not->toBeNull();
    });

    it('fails_to_login_with_incorrect_credentials', function () {
        $customer = Customer::factory()->create(['phone' => '0911112222']);
        $res = $this->postJson(route('customer.login'), [
            'phone' => $customer->phone,
            'password' => 'wrongpassword',
        ]);
        expect($res->status())->toBe(400);
    });

    it('can logout customer', function () {
        $this->user('customer')->api();
        $res = $this->postJson(route('customer.logout'));
        $res->assertOk();
    });
});
