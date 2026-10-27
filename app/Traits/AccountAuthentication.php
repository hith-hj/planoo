<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\CodesTypes;
use App\Models\Customer;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

trait AccountAuthentication
{
    protected function guard(): JWTGuard
    {
        return Auth::guard(static::GUARD);
    }

    public function verify(Validator $validator): User|Customer
    {
        $user = $this->getAccount($validator);
        $code = $user->code(CodesTypes::verification->name);
        Truthy($code->code !== $validator->safe()->integer('code'), 'invalid code');
        Truthy($code->expire_at !== null && ! $code->isValid(), 'code expired');
        $user->verified();

        return $user;
    }

    public function login(Validator $validator): array
    {
        $credentials = $validator->safe()->only(['country_code', 'phone', 'password']);
        $token = $this->guard()->attempt($credentials);
        if (! $token) {
            throw new DomainException(__('invalid credentials'));
        }

        /** @var User|Customer $user */
        $user = $this->guard()->user();
        if ($user->verified_at === null) {
            throw new DomainException(__('unverified account'));
        }

        if (! $user->is_active) {
            throw new DomainException(__('Your request will be processed within 24 hours'));
        }

        if ($user->firebase_token === null || ($user instanceof Customer && $user->firebase_token === 'not-set')) {
            $user->update(['firebase_token' => $validator->safe()->input('firebase_token')]);
        }

        return [$user->toResource(), $token];
    }

    public function forgetPassword(Validator $validator): User|Customer
    {
        $user = $this->getAccount($validator);
        Truthy($user->firebase_token !== $validator->safe()->input('firebase_token'), 'invalid operation');
        $user->verify(CodesTypes::password->name);

        return $user;
    }

    public function resetPassword(Validator $validator): User|Customer
    {
        $user = $this->getAccount($validator);
        Truthy($user->firebase_token !== $validator->safe()->input('firebase_token'), 'invalid operation');
        Truthy($user->verified_at !== null, 'verify account');

        $code = $user->code(CodesTypes::password->name);
        Truthy($code->code !== $validator->safe()->integer('code'), 'invalid code');
        Truthy($code->expire_at !== null && ! $code->isValid(), 'code expired');

        $user->verified(CodesTypes::password->name)
            ->update(['password' => Hash::make($validator->safe()->input('password'))]);

        return $user;
    }

    public function resendCode(Validator $validator): User|Customer
    {
        $user = $this->getAccount($validator);
        Truthy($user->verified_at !== null, 'invalid operation');

        $user->verify();

        return $user;
    }

    public function changePassword(Validator $validator): User|Customer
    {
        /** @var User|Customer $user */
        $user = $this->guard()->user();

        if ($user->verified_at === null) {
            throw new DomainException(__('verify your account'));
        }

        if (! Hash::check($validator->safe()->input('old_password'), $user->password)) {
            throw new DomainException(__('invalid password'));
        }

        if (Hash::check($validator->safe()->input('new_password'), $user->password)) {
            throw new DomainException(__('passwords are equals'));
        }

        $user->update(['password' => Hash::make($validator->safe()->input('new_password'))]);

        return $user;
    }

    public function refreshToken(): string
    {
        return $this->guard()->refresh();
    }

    public function logout(bool $clear_token = false): void
    {
        if ($clear_token) {
            /** @var User|Customer $user */
            $user = $this->guard()->user();
            $user->update(['firebase_token' => null]);
        }

        $this->guard()->logout();
    }

    public function updateFirebaseToken(?string $token): bool
    {
        /** @var User|Customer $user */
        $user = $this->guard()->user();
        if ($user && $token !== null && $user->firebase_token !== $token) {
            return $user->update(['firebase_token' => $token]);
        }

        return false;
    }

    private function getAccount(Validator $validator): User|Customer
    {
        $user = static::MODEL::where([
            ['country_code', $validator->safe()->input('country_code')],
            ['phone', $validator->safe()->input('phone')],
        ])->first();
        NotFound($user, 'user');

        return $user;
    }
}
