<?php

declare(strict_types=1);

use App\Enums\CodesTypes;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

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
        $customer->createCode(CodesTypes::verification->name);
        $code = $customer->code(CodesTypes::verification->name)->code;
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
        $customer->createCode(CodesTypes::verification->name);
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
            'firebase_token' => $customer->firebase_token,
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
            'firebase_token' => $customer->firebase_token,
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
            'firebase_token' => $customer->firebase_token,
        ]);
        expect($res->status())->toBe(400);
    });

    it('can logout customer', function () {
        $this->user('customer')->api();
        $res = $this->postJson(route('customer.logout'));
        $res->assertOk();
    });

    it('can resend verification code if customer if not verified', function () {
        $customer = Customer::factory()->create(['verified_at' => null, 'verified_by' => null]);
        expect($customer->codes()->count())->toBe(0);
        $this->postJson(route('customer.resendCode'), ['phone' => $customer->phone])->assertOk();
        expect($customer->verified_at)->toBeNull()
            ->and($customer->codes()->count())->toBe(1)
            ->and($customer->code(CodesTypes::verification->name))->not->ToBeNull();
    });

    it('can\'t resend verification code if customer if verified', function () {
        $customer = Customer::factory()->create();
        $res = $this->postJson(route('customer.resendCode'), ['phone' => $customer->phone]);
        $res->assertStatus(400);
        expect($customer->fresh()->verified_at)->not->toBeNull()
            ->and($customer->codes()->count())->toBe(0);
    });

    it('can forget password ', function () {
        $customer = Customer::factory()->create();
        expect($customer->codes()->count())->toBe(0);
        $this->postJson(route('customer.forgetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => $customer->firebase_token,
        ])->assertOk();
        expect($customer->fresh()->verified_at)->toBeNull()
            ->and($customer->codes()->count())->toBe(1)
            ->and($customer->code(CodesTypes::password->name))->not->ToBeNull();
    });

    it('can reset password ', function () {
        $customer = Customer::factory()->create();
        expect($customer->codes()->count())->toBe(0);
        $this->postJson(route('customer.forgetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => $customer->firebase_token,
        ])->assertOk();
        expect($customer->fresh()->verified_at)->toBeNull();
        $code = $customer->code(CodesTypes::password->name);
        $this->postJson(route('customer.resetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => $customer->firebase_token,
            'password' => 'password',
            'password_confirmation' => 'password',
            'code' => $code->code
        ])->assertOk();
        expect($customer->fresh()->verified_at)->not->toBeNull()
        ->and(Hash::check('password',$customer->fresh()->password))->toBeTrue()
        ->and($customer->codes()->count())->toBe(0);
    });

    it('cant reset password with invlid code ', function () {
        $customer = Customer::factory()->create();
        expect($customer->codes()->count())->toBe(0);
        $this->postJson(route('customer.forgetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => $customer->firebase_token,
        ])->assertOk();
        $this->postJson(route('customer.resetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => $customer->firebase_token,
            'password' => 'password',
            'password_confirmation' => 'password',
            'code' => 'invalid_code'
        ])->assertStatus(422);
    });

    it('cant reset password with invlid data ', function () {
        $customer = Customer::factory()->create();
        expect($customer->codes()->count())->toBe(0);
        $this->postJson(route('customer.forgetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => $customer->firebase_token,
        ])->assertOk();
        $this->postJson(route('customer.resetPassword'), [
            'phone' => $customer->phone,
            'firebase_token' => 'invalid_token',
            'password' => 'password',
            'password_confirmation' => 'password',
            'code' => 'invalid_code'
        ])->assertStatus(422);
    });

    it('can change password ', function () {
        $customer = Customer::factory()->create();
        $res = $this->replaceUser($customer)->postJson(route('customer.changePassword'), [
            'old_password' => 'password',
            'new_password' => 'new_password',
            'new_password_confirmation' => 'new_password',
        ]);
        $res->assertOk();
        expect(Hash::check('new_password',$customer->fresh()->password))->toBeTrue();
    });
});
