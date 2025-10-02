<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Services\LocationServices;
use App\Validators\LocationValidators;
use Illuminate\Http\Request;

final class LocationsController extends Controller
{
    public function __construct(public LocationServices $services) {}

    public function get()
    {
        $location = $this->services->get(getModel());

        return Success(payload: ['location' => LocationResource::make($location)]);
    }

    public function create(Request $request)
    {
        $validator = LocationValidators::create($request->all());
        if ($this->services->checkLocationExists(getModel())) {
            return Error(msg: 'location exists');
        }

        $location = $this->services->create(
            getModel(),
            $validator->safe()->all()
        );

        return Success(payload: [
            'location' => LocationResource::make($location),
        ]);
    }

    public function update(Request $request)
    {
        $validator = LocationValidators::create($request->all(), true);
        $location = $this->services->update(
            getModel(),
            $validator->safe()->all()
        );

        return Success(payload: ['location' => LocationResource::make($location)]);
    }

    public function delete()
    {
        $this->services->delete(getModel());

        return Success();
    }
}
