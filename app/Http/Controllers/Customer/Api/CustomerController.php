<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Services\CustomerServices;
use App\Validators\CustomerValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CustomerController extends Controller
{
    public function __construct(public CustomerServices $services) {}

    public function get()
    {
        $customer = $this->services->get(Auth::id());

        return Success(payload: ['customer' => $customer->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = CustomerValidators::update($request->all());
        $customer = $this->services->get(Auth::id());
        $this->services->update($customer, $validator->safe()->all());

        return Success(payload: ['customer' => $customer->fresh()->toResource()]);
    }

    public function delete()
    {
        return Success('To be implemented');
    }

    public function uploadProfileImage(Request $request)
    {
        $validator = CustomerValidators::profileImage($request->all());
        $customer = $this->services->get(Auth::id());
        $res = $this->services->uploadProfileImage($customer, $validator->safe()->all());

        return Success(payload: ['profile_image' => MediaResource::make($res)]);
    }

    public function deleteProfileImage(Request $request)
    {
        $customer = $this->services->get(Auth::id());
        $media = $customer->mediaByName('profile_image');
        if ($media === null) {
            return Error('profile image missing');
        }
        $this->services->deleteProfileImage($media);

        return Success('profile image deleted');
    }
}
