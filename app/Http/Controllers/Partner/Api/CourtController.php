<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\CourtServices;
use App\Support\Pagination;
use App\Validators\CourtValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CourtController extends Controller
{
    public function __construct(public CourtServices $services) {}

    public function all(Request $request)
    {
        [$page, $perPage] = Pagination::fromRequest($request);
        $courts = $this->services->allByUser(Auth::user(), $page, $perPage);

        return Success(payload: [
            'courts' => $courts->toResourceCollection(), ...($perPage === null ? [] : compact('page', 'perPage')),
        ]);
    }

    public function find(Request $request)
    {
        $validator = CourtValidators::find($request->all());
        $court = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->input('court_id')
        );

        return Success(payload: [
            'court' => $court->toResource(),
        ]);
    }

    public function create(Request $request)
    {
        $validator = CourtValidators::create($request->all());

        $court = $this->services->create(
            Auth::user(),
            $validator->safe()->all()
        );

        return Success(payload: [
            'court' => $court->toResource(),
        ]);
    }

    public function update(Request $request)
    {
        $validator = CourtValidators::create($request->all(), true);
        $model = Auth::user();
        $court = $this->services->findByUser(
            $model,
            $validator->safe()->input('court_id')
        );
        $this->services->update(
            $court,
            $validator->safe()->except('court_id')
        );

        return Success(payload: [
            'court' => $court->fresh()->toResource(),
        ]);
    }

    public function delete(Request $request)
    {
        $validator = CourtValidators::delete($request->all());
        $court = $this->services->findByUser(Auth::user(), $validator->safe()->input('court_id'));
        if (Auth::user()->courts()->count() === 1) {
            return Error('last court can not be deleted');
        }
        if ($court->hasChildren()) {
            return Error('Court has children can not be deleted');
        }
        $this->services->delete($court);

        return Success(msg: 'court deleted');
    }

    public function hasCourt(Request $request)
    {
        return Success(payload: ['hasCourt' => $this->services->hasCourt(Auth::user())]);
    }
}
