<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\UserAuthServices;
use App\Validators\UserAuthValidators;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    public function __construct(public UserAuthServices $services) {}

    public function register(Request $request)
    {
        $validator = UserAuthValidators::register($request->all());
        $this->services->create($validator->safe()->all());

        return Success(
            msg: __('registerd'),
            code: 201
        );
    }

    public function verify(Request $request)
    {
        $validator = UserAuthValidators::verify($request->all());
        $this->services->verify($validator);

        return Success(msg: __('verified'));
    }

    public function login(Request $request)
    {
        $validator = UserAuthValidators::login($request->all());
        [$user, $token] = $this->services->login($validator);

        return Success(payload: ['user' => $user->toResource(), 'token' => $token]);
    }

    public function refreshToken()
    {
        return Success(payload: ['token' => $this->services->refreshToken()]);
    }

    public function logout()
    {
        $this->services->logout();

        return Success(msg: __('logout'));
    }

    public function forgetPassword(Request $request)
    {
        $validator = UserAuthValidators::forgetPassword($request->all());

        $this->services->forgetPassword($validator);

        return Success(msg: __('code sent'));
    }

    public function resetPassword(Request $request)
    {
        $validator = UserAuthValidators::resetPassword($request->all());

        $this->services->resetPassword($validator);

        return Success(msg: __('password updated'));
    }

    public function resendCode(Request $request)
    {
        $validator = UserAuthValidators::resendCode($request->all());
        $this->services->resendCode($validator);

        return Success(msg: __('code sent'));
    }

    public function changePassword(Request $request)
    {
        $validator = UserAuthValidators::changePassword($request->all());
        $this->services->changePassword($validator);

        return Success(msg: __('updated'));
    }
}
