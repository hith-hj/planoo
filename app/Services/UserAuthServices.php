<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Enums\CodesTypes;
use App\Http\Resources\UserResource;
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
        $this->setGuard();
    }

    public function create(array $data): User
    {
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

        $user->fresh()->verify($data['by'] ?? 'fcm');

        return $user;
    }

    public function verify(Validator $validator): User
    {
        $user = $this->getUser($validator);
        $code = $user->code(CodesTypes::verification->name);
        if ($code === null || $code->code !== $validator->safe()->integer('code')) {
            throw new Exception(__('invalid code'));
        }

        if ($code->expire_at !== null && $code->expire_at->lt(now())) {
            throw new Exception(__('code expired'));
        }
        $user->verified();

        return $user;
    }

    public function login(Validator $validator): array
    {
        $credentials = $validator->safe()->only('phone', 'password');
        if (! Auth::attempt($credentials)) {
            throw new Exception(__('invalid credentials'));
        }

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

        return [UserResource::make($user), JWTAuth::fromUser($user)];
    }

    public function forgetPassword(Validator $validator): User
    {
        $user = $this->getUser($validator);
        $user->verify();

        return $user;
    }

    public function resetPassword(Validator $validator): User
    {
        $user = $this->getUser($validator);

        if (is_null($user->verified_at)) {
            throw new Exception(__('verify account'), code: 401);
        }

        $user->update(['password' => Hash::make($validator->safe()->input('password'))]);

        return $user;
    }

    public function resendCode(Validator $validator): User
    {
        $user = $this->getUser($validator);

        if ($user->verified_at !== null) {
            throw new Exception(__('invalid operation'), code: 403);
        }

        $user->verify();

        return $user;
    }

    public function changePassword(Validator $validator): User
    {
        $user = Auth::user();
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

    public function logout()
    {
        return JWTAuth::invalidate(JWTAuth::getToken());
    }

    private function setGuard()
    {
        $guard = null;
        if (request()->is('*api/partner/*')) {
            $guard = 'partner:api';
        } elseif (request()->is('*api/customer/*')) {
            $guard = 'customer:api';
        } else {
            throw new Exception('Invalid route guard', 422);
        }
        config(['auth.defaults.guard' => $guard]);

    }

    private function getUser($validator)
    {
        $user = User::where('phone', $validator->safe()->input('phone'))->first();
        NotFound($user);

        return $user;
    }

    private function username($user): string
    {
        $username = $user.mt_rand(1000, 9999);

        $attemps = 0;
        while ($attemps < 5) {
            if (! User::where('username', $username)->exists()) {
                break;
            }
            $username = $user.mt_rand(1000, 9999);
        }

        return mb_strtolower($username);
    }
}
