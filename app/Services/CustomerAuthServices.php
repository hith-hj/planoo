<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Enums\CodesTypes;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

final class CustomerAuthServices
{
    public function __construct()
    {
        Auth::shouldUse('customer:api');
    }

    public function create(array $data): Customer
    {
        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'firebase_token' => $data['firebase_token'],
            'password' => bcrypt($data['password']),
            'status' => AccountStatus::fresh->value,
            'is_active' => false,
            'is_notifiable' => true,
        ]);

        $customer->fresh()->verify($data['by'] ?? 'fcm');

        return $customer;
    }

    public function verify(Validator $validator): Customer
    {
        $customer = $this->getCustomer($validator);
        $code = $customer->code(CodesTypes::verification->name);
        if ($code === null || $code->code !== $validator->safe()->integer('code')) {
            throw new Exception(__('invalid code'));
        }

        if ($code->expire_at !== null && $code->expire_at->lt(now())) {
            throw new Exception(__('code expired'));
        }
        $customer->verified();

        return $customer;
    }

    public function login(Validator $validator): array
    {
        $credentials = $validator->safe()->only('phone', 'password');
        if (! Auth::attempt($credentials)) {
            throw new Exception(__('invalid credentials'));
        }

        $customer = Auth::user();
        if (! Hash::check($validator->safe()->input('password'), $customer->password)) {
            throw new Exception(__('incorrect password'));
        }

        if ($customer->verified_at === null) {
            throw new Exception(__('unverified account'));
        }

        // if (!$customer->is_active) {
        //     throw new Exception(__('inactive account,wait until activation'));
        // }

        return [$customer->toResource(), JWTAuth::fromUser($customer)];
    }

    public function forgetPassword(Validator $validator): Customer
    {
        $customer = $this->getCustomer($validator);
        $customer->verify();

        return $customer;
    }

    public function resetPassword(Validator $validator): Customer
    {
        $customer = $this->getCustomer($validator);

        if (is_null($customer->verified_at)) {
            throw new Exception(__('verify account'), code: 401);
        }

        $customer->update(['password' => Hash::make($validator->safe()->input('password'))]);

        return $customer;
    }

    public function resendCode(Validator $validator): Customer
    {
        $customer = $this->getCustomer($validator);

        if ($customer->verified_at !== null) {
            throw new Exception(__('invalid operation'), code: 403);
        }

        $customer->verify();

        return $customer;
    }

    public function changePassword(Validator $validator): Customer
    {
        $customer = Auth::customer();
        if (! Hash::check($validator->safe()->input('old_password'), $customer->password)) {
            throw new Exception(__('invalid password'));
        }

        if ($customer->password === Hash::make($validator->safe()->input('new_password'))) {
            throw new Exception(__('passwords are equals'));
        }

        $customer->update(['password' => Hash::make($validator->safe()->input('new_password'))]);

        return $customer;
    }

    public function refreshToken()
    {
        return Auth::refresh();
    }

    public function logout()
    {
        return JWTAuth::invalidate(JWTAuth::getToken());
    }

    private function getCustomer($validator)
    {
        $customer = Customer::where('phone', $validator->safe()->input('phone'))->first();
        NotFound($customer);

        return $customer;
    }
}
