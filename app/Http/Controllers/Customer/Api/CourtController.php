<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\CourtServices;
use App\Validators\CourtValidators;
use Illuminate\Http\Request;

final class CourtController extends Controller
{
    public function __construct(public CourtServices $services) {}

    public function all(Request $request)
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('perPage', 10);
        $courts = $this->services->all($page, $perPage);

        return Success(payload: [
            'page' => $page,
            'perPage' => $perPage,
            'courts' => $courts->toResourceCollection(),
        ]);
    }

    public function search(Request $request)
    {
        $court = $this->services->search($request->input('name'));

        return Success(payload: [
            'court' => $court->toResourceCollection(),
        ]);
    }

    public function find(Request $request)
    {
        $validator = CourtValidators::find($request->all());
        $court = $this->services->find(
            $validator->safe()->integer('court_id')
        );

        return Success(payload: [
            'court' => $court->toResource(),
        ]);
    }
}
