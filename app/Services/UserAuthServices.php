<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Enums\CodesTypes;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

final class UserAuthServices
{
    public function __construct()
    {
        Auth::shouldUse('partner:api');
    }

    public function create(array $data): User
    {
        /** @var User $user */
        $user = User::create([
            'name' => $data['name'],
            'account_type' => $data['account_type'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'firebase_token' => $data['firebase_token'],
            'description' => $data['description'],
            'password' => bcrypt($data['password']),
            'status' => AccountStatus::fresh->value,
            'is_active' => false,
            'is_notifiable' => true,
        ]);

        $user->fresh()->verify(by: $data['by'] ?? 'fcm');

        return $user;
    }

    public function verify(Validator $validator): User
    {
        $user = $this->getUser($validator);
        $code = $user->code(CodesTypes::verification->name);
        Truthy($code->code !== $validator->safe()->integer('code'), __('invalid code'));
        Truthy($code->expire_at !== null && ! $code->isValid(), __('code expired'));
        $user->verified();

        return $user;
    }

    public function login(Validator $validator): array
    {
        $credentials = $validator->safe()->only('phone', 'password');
        if (! Auth::attempt($credentials)) {
            throw new Exception(__('invalid credentials'));
        }

        /** @var User $user */
        $user = Auth::user();
        if (! Hash::check($validator->safe()->input('password'), $user->password)) {
            throw new Exception(__('incorrect password'));
        }

        if ($user->verified_at === null) {
            throw new Exception(__('unverified account'));
        }

        // if (!$user->is_active) {
        //     throw new Exception(__('inactive account,wait until activation'));
        // }

        if ($user->firebase_token === null) {
            $user->update(['firebase_token' => $validator->safe()->input('firebase_token')]);
        }

        return [$user->toResource(), JWTAuth::fromUser($user)];
    }

    public function forgetPassword(Validator $validator): User
    {
        $user = $this->getUser($validator);
        Truthy($user->firebase_token !== $validator->safe()->input('firebase_token'), 'invalid operation');
        $user->verify(CodesTypes::password->name);

        return $user;
    }

    public function resetPassword(Validator $validator): User
    {
        $user = $this->getUser($validator);
        Truthy($user->firebase_token !== $validator->safe()->input('firebase_token'), 'invalid operation');
        Truthy($user->verified_at !== null, __('verify account'));

        $code = $user->code(CodesTypes::password->name);
        Truthy($code->code !== $validator->safe()->integer('code'), __('invalid code'));
        Truthy($code->expire_at !== null && ! $code->isValid(), __('code expired'));

        $user->verified(CodesTypes::password->name)
            ->update(['password' => Hash::make($validator->safe()->input('password'))]);

        return $user;
    }

    public function resendCode(Validator $validator): User
    {
        $user = $this->getUser($validator);
        Truthy($user->verified_at !== null, __('invalid operation'));

        $user->verify();

        return $user;
    }

    public function changePassword(Validator $validator): User
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->verified_at === null) {
            throw new Exception(__('verify your account'));
        }

        if (! Hash::check($validator->safe()->input('old_password'), $user->password)) {
            throw new Exception(__('invalid password'));
        }

        if ($user->password === Hash::make($validator->safe()->input('new_password'))) {
            throw new Exception(__('passwords are equals'));
        }

        $user->update(['password' => Hash::make($validator->safe()->input('new_password'))]);

        return $user;
    }

    public function refreshToken()
    {
        return Auth::refresh();
    }

    public function logout($clear_token = false)
    {
        if ($clear_token) {
            /** @var User $user */
            $user = Auth::user();
            $user->update(['firebase_token' => null]);
        }

        return JWTAuth::invalidate(JWTAuth::getToken());
    }

    private function getUser($validator): User
    {
        $user = User::where('phone', $validator->safe()->input('phone'))->first();
        NotFound($user);

        return $user;
    }
}
