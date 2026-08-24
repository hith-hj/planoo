<?php

declare(strict_types=1);

beforeEach(function () {
    $this->seed();
    $this->user('customer');
    $this->url = '/api/customer/v1/auth/changePassword';
});

describe('Customer change password security rules', function () {

    it('rejects reusing the same password', function () {
        $res = $this->api()->postJson($this->url, [
            'old_password' => 'password',
            'new_password' => 'password',
            'new_password_confirmation' => 'password',
        ]);

        expect($res->status())->toBe(400)
            ->and($res->json('message'))->toBe(__('passwords are equals'));
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

        expect($res->status())->toBe(200);
    });
});
