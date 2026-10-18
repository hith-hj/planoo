<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerAuthServices;
use App\Services\UserAuthServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

it('constructs both authentication services without changing the default guard', function () {
    Auth::shouldUse('web');
    app(CustomerAuthServices::class);
    app(UserAuthServices::class);
    expect(Auth::getDefaultDriver())->toBe('web');
});

it('authenticates using its own provider regardless of the default guard', function (string $role) {
    $this->seed();
    $model = $role === 'customer' ? Customer::firstOrFail() : User::firstOrFail();
    $model->update(['password' => bcrypt('correct-password'), 'is_active' => true, 'verified_at' => now()]);
    Auth::shouldUse('web');
    $customerService = app(CustomerAuthServices::class);
    $partnerService = app(UserAuthServices::class);
    $service = $role === 'customer' ? $customerService : $partnerService;
    $validator = Validator::make([
        'country_code' => $model->country_code,
        'phone' => $model->phone,
        'password' => 'correct-password',
        'firebase_token' => 'device-token',
    ], array_fill_keys(['country_code', 'phone', 'password', 'firebase_token'], 'required'));
    [$resource, $token] = $service->login($validator);
    expect($resource->resource)->toBeInstanceOf($model::class);
    expect($resource->resource->id)->toBe($model->id);
    expect($token)->toBeString()->not->toBeEmpty();
    expect(Auth::guard($role.':api')->user()->id)->toBe($model->id);
    expect(Auth::getDefaultDriver())->toBe('web');
})->with(['customer', 'partner']);
