<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationServices;
use App\Validators\LocationValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LocationController extends Controller
{
    public function __construct(public LocationServices $services) {}

    public function get()
    {
        $location = $this->services->get(Auth::user());

        return Success(payload: ['location' => $location->toResource()]);
    }

    public function create(Request $request)
    {
        $validator = LocationValidators::create($request->all());
        if ($this->services->checkLocationExists(Auth::user())) {
            return Error(msg: 'location exists');
        }

        $location = $this->services->create(
            Auth::user(),
            $validator->safe()->all()
        );

        return Success(payload: ['location' => $location->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = LocationValidators::create($request->all(), true);
        $location = $this->services->update(
            Auth::user(),
            $validator->safe()->all()
        );

        return Success(payload: ['location' => $location->toResource()]);
    }

    public function delete()
    {
        $this->services->delete(Auth::user());

        return Success();
    }
}
