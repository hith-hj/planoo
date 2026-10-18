<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\CourtServices;
use App\Support\Pagination;
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
        $data = $request->validate(['name' => ['required', 'string', 'max:200']]);
        [$page, $perPage] = Pagination::fromRequest($request);
        $court = $this->services->search($data['name'], $page, $perPage);

        return Success(payload: [
            'court' => $court->toResourceCollection(),
            ...($perPage === null ? [] : compact('page', 'perPage')),
        ]);
    }

    public function find(Request $request)
    {
        $validator = CourtValidators::find($request->all());
        $court = $this->services->find(
            $validator->safe()->input('court_id')
        );

        return Success(payload: [
            'court' => $court->toResource(),
        ]);
    }
}
