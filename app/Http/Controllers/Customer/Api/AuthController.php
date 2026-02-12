<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerAuthServices;
use App\Validators\CustomerAuthValidators;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    public function __construct(public CustomerAuthServices $services) {}

    public function register(Request $request)
    {
        $validator = CustomerAuthValidators::register($request->all());
        $this->services->create($validator->safe()->all());

        return Success();
    }

    public function verify(Request $request)
    {
        $validator = CustomerAuthValidators::verify($request->all());
        $this->services->verify($validator);

        return Success(msg: __('verified'));
    }

    public function login(Request $request)
    {
        $validator = CustomerAuthValidators::login($request->all());
        [$customer, $token] = $this->services->login($validator);

        return Success(payload: ['customer' => $customer->toResource(), 'token' => $token]);
    }

    public function refreshToken()
    {
        return Success(payload: ['token' => $this->services->refreshToken()]);
    }

    public function logout(Request $request)
    {
        $clear_token = $request->has('clear_token') && $request->boolean('clear_token') === true ? true : false;
        $this->services->logout($clear_token);

        return Success(msg: __('logout'));
    }

    public function forgetPassword(Request $request)
    {
        $validator = CustomerAuthValidators::forgetPassword($request->all());

        $this->services->forgetPassword($validator);

        return Success(msg: __('code sent'));
    }

    public function resetPassword(Request $request)
    {
        $validator = CustomerAuthValidators::resetPassword($request->all());

        $this->services->resetPassword($validator);

        return Success(msg: __('password updated'));
    }

    public function resendCode(Request $request)
    {
        $validator = CustomerAuthValidators::resendCode($request->all());
        $this->services->resendCode($validator);

        return Success(msg: __('code sent'));
    }

    public function changePassword(Request $request)
    {
        $validator = CustomerAuthValidators::changePassword($request->all());
        $this->services->changePassword($validator);

        return Success(msg: __('updated'));
    }
}
