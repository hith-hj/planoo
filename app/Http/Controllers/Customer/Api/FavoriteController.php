<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\FavoriteServices;
use App\Validators\FavoriteValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class FavoriteController extends Controller
{
    public function __construct(public FavoriteServices $services) {}

    public function all()
    {
        $favorites = $this->services->get($this->getCustomer());

        return Success(payload: ['favorites' => $favorites]);
    }

    public function find(Request $request)
    {
        $validator = FavoriteValidators::find($request->all());
        $favorite = $this->services->find(
            $this->getCustomer(),
            $validator->safe()->integer('favorite_id')
        );

        return Success(payload: ['favorite' => $favorite]);
    }

    public function create()
    {
        $exists = $this->services->favoriteExists(
            $this->getCustomer(),
            getModelGlobal()
        );
        if ($exists) {
            return Error('favorite exists');
        }
        $favorite = $this->services->create(
            $this->getCustomer(),
            getModelGlobal()
        );

        return Success(payload: ['favorite' => $favorite]);
    }

    public function delete(Request $request)
    {
        // $this->services->delete(getModelGlobal()->id);
        $validator = FavoriteValidators::delete($request->all());
        $this->services->delete($validator->safe()->integer('favorite_id'));

        return Success();
    }

    private function getCustomer(): Customer
    {
        /** @return Customer */
        return Auth::user();
    }
}
