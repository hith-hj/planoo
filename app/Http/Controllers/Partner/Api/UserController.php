<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserServices;
use App\Validators\UserValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class UserController extends Controller
{
    public function __construct(public UserServices $services) {}

    public function get()
    {
        $user = $this->services->get(Auth::id());

        return Success(payload: ['user' => UserResource::make($user)]);
    }

    public function update(Request $request)
    {
        $validator = UserValidators::update($request->all());
        $user = $this->services->get(Auth::id());
        $this->services->update($user, $validator->safe()->all());

        return Success(payload: ['user' => UserResource::make($user->fresh())]);
    }

    public function delete()
    {
        return Success('To be implemented');
    }

    public function uploadProfileImage(Request $request)
    {
        $validator = UserValidators::profileImage($request->all());
        $user = $this->services->get(Auth::id());
        $res = $this->services->uploadProfileImage($user, $validator->safe()->all());

        return Success(payload: ['profile_image' => $res]);
    }

    public function deleteProfileImage(Request $request)
    {
        $user = $this->services->get(Auth::id());
        $media = $user->mediaByName('profile_image');
        $res = $this->services->deleteProfileImage($media);

        return Success(payload: ['profile_image' => $res]);
    }
}
