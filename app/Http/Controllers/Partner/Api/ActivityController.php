<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Validators\ActivityValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class ActivityController extends Controller
{
    public function __construct(public ActivityServices $services) {}

    public function all()
    {
        $activities = $this->services->allByUser(Auth::user());

        return Success(payload: ['activities' => $activities->toResourceCollection()]);
    }

    public function find(Request $request)
    {
        $validator = ActivityValidators::find($request->all());
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('activity_id')
        );

        return Success(payload: ['activity' => $activity->toResource()]);
    }

    public function create(Request $request)
    {
        $validator = ActivityValidators::create($request->all());
        $activity = $this->services->create(
            Auth::user(),
            $validator->safe()->except(['cords', 'days', 'times', 'tags'])
        );
        $request->merge(['owner_type' => 'activity', 'owner_id' => $activity->id]);
        $request->offsetUnset('name');
        app(DayController::class)->createMany($request);
        app(LocationsController::class)->create($request);
        app(TagController::class)->create($request);
        app(MediaController::class)->create($request);

        return Success(payload: ['activity' => $activity->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = ActivityValidators::create($request->all(), true);
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('activity_id')
        );
        // TODO: check if the activity has any appointment prevent update ad diactivate
        $this->services->update(
            Auth::user(),
            $activity,
            $validator->safe()->except('activity_id')
        );

        return Success(payload: ['activity' => $activity->fresh()->toResource()]);
    }

    public function delete(Request $request)
    {
        $validator = ActivityValidators::delete($request->all());
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('activity_id')
        );
        $this->services->delete($activity);

        return Success(msg: 'activity deleted');
    }

    public function toggleActivation(Request $request)
    {
        $validator = ActivityValidators::find($request->all());
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('activity_id')
        );
        $this->services->toggleActivation($activity);

        return Success();
    }
}
