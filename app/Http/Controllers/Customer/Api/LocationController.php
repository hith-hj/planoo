<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\LocationServices;
use App\Validators\LocationValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class LocationController extends Controller
{
    public function __construct(public LocationServices $services) {}

    public function get()
    {
        $location = $this->services->get($this->getCustomer());

        return Success(payload: ['location' => $location->toResource()]);
    }

    public function create(Request $request)
    {
        $validator = LocationValidators::create($request->all());
        if ($this->services->checkLocationExists($this->getCustomer())) {
            return Error(msg: 'location exists');
        }

        $location = $this->services->create(
            $this->getCustomer(),
            $validator->safe()->all()
        );

        return Success(payload: ['location' => $location->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = LocationValidators::create($request->all(), true);
        $location = $this->services->update(
            $this->getCustomer(),
            $validator->safe()->all()
        );

        return Success(payload: ['location' => $location->toResource()]);
    }

    public function delete()
    {
        $this->services->delete($this->getCustomer());

        return Success();
    }

    private function getCustomer(): Customer
    {
        /** @return Customer */
        return Auth::user();
    }
}
