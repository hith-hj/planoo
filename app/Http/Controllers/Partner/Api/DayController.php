<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\DayServices;
use App\Validators\DayValidators;
use Illuminate\Http\Request;

final class DayController extends Controller
{
    public function __construct(public DayServices $services) {}

    public function all()
    {
        $days = $this->services->allByObject(getModel());

        return Success(payload: [
            'days' => $days->toResourceCollection(),
        ]);
    }

    public function find(Request $request)
    {
        $validator = DayValidators::find($request->all());
        $day = $this->services->findByObject(
            getModel(),
            $validator->safe()->integer('day_id')
        );

        return Success(payload: [
            'day' => $day->toResource(),
        ]);
    }

    public function create(Request $request)
    {
        $validator = DayValidators::create($request->all());

        $day = $this->services->create(
            getModel(),
            $validator->safe()->all()
        );

        return Success(payload: [
            'day' => $day->fresh()->toResource(),
        ]);
    }

    public function createMany(Request $request)
    {
        $validator = DayValidators::createMany($request->all());
        $days = $this->services->createMany(
            getModel(),
            $validator->safe()->all()
        );

        return Success(payload: [
            'days' => $days->toResourceCollection(),
        ]);
    }

    public function update(Request $request)
    {
        $validator = DayValidators::update($request->all());
        $day = $this->services->findByObject(
            getModel(),
            $validator->safe()->integer('day_id')
        );
        $this->services->update(
            getModel(),
            $day,
            $validator->safe()->except('day_id')
        );

        return Success(payload: [
            'day' => $day->fresh()->toResource(),
        ]);
    }

    public function delete(Request $request)
    {
        $validator = DayValidators::delete($request->all());
        $day = $this->services->findByObject(
            getModel(),
            $validator->safe()->integer('day_id')
        );
        $this->services->delete($day);

        return Success(msg: 'day deleted');
    }

    public function toggleActivation(Request $request)
    {
        $validator = DayValidators::find($request->all());
        $day = $this->services->findByObject(
            getModel(),
            $validator->safe()->integer('day_id')
        );
        $this->services->toggleActivation($day);

        return Success();
    }
}
