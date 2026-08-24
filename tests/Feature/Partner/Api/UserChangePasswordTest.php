<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium');
    $this->url = '/api/partner/v1/auth/changePassword';
});

describe('Partner change password security rules', function () {

    it('rejects reusing the same password', function () {
        $res = $this->api()->postJson($this->url, [
            'old_password' => 'password',
            'new_password' => 'password',
            'new_password_confirmation' => 'password',
        ]);

        // Hash::make() based comparison could never detect this before the fix
        expect($res->status())->toBe(400)
            ->and($res->json('message'))->toBe(__('passwords are equals'))
            ->and(Hash::check('password', $this->user->fresh()->password))->toBeTrue();
    });

    it('rejects a wrong old password', function () {
        $res = $this->api()->postJson($this->url, [
            'old_password' => 'wrong-password-1',
            'new_password' => 'new-password-99',
            'new_password_confirmation' => 'new-password-99',
        ]);

        expect($res->status())->toBe(400)
            ->and($res->json('message'))->toBe(__('invalid password'));
    });

    it('updates the password with valid credentials', function () {
        $res = $this->api()->postJson($this->url, [
            'old_password' => 'password',
            'new_password' => 'brand-new-pass-1',
            'new_password_confirmation' => 'brand-new-pass-1',
        ]);

        expect($res->status())->toBe(200)
            ->and(Hash::check('brand-new-pass-1', User::find($this->user->id)->password))->toBeTrue();
    });
});
