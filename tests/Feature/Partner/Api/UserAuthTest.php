<?php

declare(strict_types=1);

use App\Enums\CodesTypes;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function(){
    $this->seed();
    $this->url = '/api/partner/v1/auth';
});

describe('UserAuth Controller test', function () {

    it('registers_a_user', function () {
        $data = User::factory()->password()->make()->toArray();
        $res = $this->postJson(route('partner.register'), $data);
        expect($res->status())->toBe(201);
    });

    it('fails_to_registers_a_user', function () {
        $data = User::factory()->make(['acount_type'=>'kiki'])->toArray();
        $res = $this->postJson(route('partner.register'), $data);
        expect($res->status())->toBe(422);
    });

    it('verifies_user_successfully', function () {
        $user = User::factory()->create(['phone' => '0912345678']);
        $user->createCode(CodesTypes::verification->name);
        $code = $user->code(CodesTypes::verification->name)->code;
        expect($code)->not->toBeNull()->and($code)->toBeInt();

        $res = $this->postJson(route('partner.verify'), [
            'phone' => $user->phone,
            'code' => $code,
        ]);
        expect($res->status())->toBe(200);
    });

    it('fails_to_verify_with_incorrect_code', function () {
        $user = User::factory()->create();
        $user->createCode(CodesTypes::verification->name);
        $res = $this->postJson(route('partner.verify'), [
            'phone' => $user->phone,
            'code' => 00000,
        ]);
        expect($res->status())->toBe(422)
        ->and($res->json('payload.errors'))->not->toBeNull();
    });

    it('allow_verified_user_login', function () {
        $user = User::factory()->create(['phone' => '0911112222']);
        $res = $this->postJson(route('partner.login'), [
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

        $res = $this->postJson(route('partner.login'), [
            'phone' => $user->phone,
            'password' => 'password',
        ]);
        expect($res->status())->toBe(400);
    });

    it('fails_to_login_with_invalid_credentials', function () {
        $res = $this->postJson(route('partner.login'), [
            'phone' => '0912345678',
            'password' => 'wrongpassword',
        ]);
        expect($res->status())->toBe(422);
        expect($res->json('payload.errors'))->not->toBeNull();
    });

    it('fails_to_login_with_incorrect_credentials', function () {
        $user = User::factory()->create(['phone' => '0911112222']);
        $res = $this->postJson(route('partner.login'), [
            'phone' => $user->phone,
            'password' => 'wrongpassword',
        ]);
        expect($res->status())->toBe(400);
    });

    it('can logout user', function () {
        $this->user('partner', 'stadium')->api();
        $res = $this->postJson(route('partner.logout'));
        $res->assertOk();
    });


    it('can resend verification code if user if not verified', function () {
        $user = User::factory()->create(['verified_at' => null, 'verified_by' => null]);
        expect($user->codes()->count())->toBe(0);
        $this->postJson(route('partner.resendCode'), ['phone' => $user->phone])->assertOk();
        expect($user->verified_at)->toBeNull()
            ->and($user->codes()->count())->toBe(1)
            ->and($user->code(CodesTypes::verification->name))->not->ToBeNull();
    });

    it('can\'t resend verification code if user if verified', function () {
        $user = User::factory()->create();
        $res = $this->postJson(route('partner.resendCode'), ['phone' => $user->phone]);
        $res->assertStatus(400);
        expect($user->fresh()->verified_at)->not->toBeNull()
            ->and($user->codes()->count())->toBe(0);
    });

    it('can forget password ', function () {
        $user = User::factory()->create();
        expect($user->codes()->count())->toBe(0);
        $this->postJson(route('partner.forgetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => $user->firebase_token,
        ])->assertOk();
        expect($user->fresh()->verified_at)->toBeNull()
            ->and($user->codes()->count())->toBe(1)
            ->and($user->code(CodesTypes::password->name))->not->ToBeNull();
    });

    it('can reset password ', function () {
        $user = User::factory()->create();
        expect($user->codes()->count())->toBe(0);
        $this->postJson(route('partner.forgetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => $user->firebase_token,
        ])->assertOk();
        expect($user->fresh()->verified_at)->toBeNull();
        $code = $user->code(CodesTypes::password->name);
        $this->postJson(route('partner.resetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => $user->firebase_token,
            'password' => 'password',
            'password_confirmation' => 'password',
            'code' => $code->code
        ])->assertOk();
        expect($user->fresh()->verified_at)->not->toBeNull()
        ->and(Hash::check('password',$user->fresh()->password))->toBeTrue()
        ->and($user->codes()->count())->toBe(0);
    });

    it('cant reset password with invlid code ', function () {
        $user = User::factory()->create();
        expect($user->codes()->count())->toBe(0);
        $this->postJson(route('partner.forgetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => $user->firebase_token,
        ])->assertOk();
        $this->postJson(route('partner.resetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => $user->firebase_token,
            'password' => 'password',
            'password_confirmation' => 'password',
            'code' => 'invalid_code'
        ])->assertStatus(422);
    });

    it('cant reset password with invlid data ', function () {
        $user = User::factory()->create();
        expect($user->codes()->count())->toBe(0);
        $this->postJson(route('partner.forgetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => $user->firebase_token,
        ])->assertOk();
        $this->postJson(route('partner.resetPassword'), [
            'phone' => $user->phone,
            'firebase_token' => 'invalid_token',
            'password' => 'password',
            'password_confirmation' => 'password',
            'code' => 'invalid_code'
        ])->assertStatus(422);
    });

    it('can change password ', function () {
        $user = User::factory()->create();
        $res = $this->replaceUser($user)->postJson(route('partner.changePassword'), [
            'old_password' => 'password',
            'new_password' => 'new_password',
            'new_password_confirmation' => 'new_password',
        ]);
        $res->assertOk();
        expect(Hash::check('new_password',$user->fresh()->password))->toBeTrue();
    });

});
