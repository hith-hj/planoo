<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Services\CourtServices;
use App\Support\Pagination;
use App\Validators\ActivityValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class ActivityController extends Controller
{
    public function __construct(public ActivityServices $services) {}

    public function all(Request $request)
    {
        [$page, $perPage] = Pagination::fromRequest($request);
        $activities = $this->services->allByUser(Auth::user(), $page, $perPage);

        return Success(payload: ['activities' => $activities->toResourceCollection(), ...($perPage === null ? [] : compact('page', 'perPage'))]);
    }

    public function find(Request $request)
    {
        $validator = ActivityValidators::find($request->all());
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->input('activity_id')
        );

        return Success(payload: ['activity' => $activity->toResource()]);
    }

    public function create(Request $request, CourtServices $courtServices)
    {
        $validator = ActivityValidators::create($request->all());
        $court = $courtServices->find($validator->safe()->input('court_id'));
        Truthy($court->user_id !== Auth::id(), 'invalid operation');
        $activity = $this->services->create(
            Auth::user(),
            $validator->safe()->except(['cords', 'days', 'times', 'tags'])
        );
        $request->merge(['owner_type' => 'activity', 'owner_id' => $activity->id]);
        $request->offsetUnset('name');
        app(DayController::class)->createMany($request);
        app(LocationController::class)->create($request);
        app(TagController::class)->create($request);
        app(MediaController::class)->create($request);

        return Success(payload: ['activity' => $activity->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = ActivityValidators::create($request->all(), true);
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->input('activity_id')
        );
        Truthy($validator->safe()->input('court_id') !== $activity->court_id, 'invalid operation');
        DB::transaction(function () use ($request, $validator, $activity): void {
            // Booking takes this same lock before checking the schedule.
            $activity->newQuery()->whereKey($activity->id)->lockForUpdate()->firstOrFail();
            if ($request->has('days')) {
                $request->merge(['owner_type' => 'activity', 'owner_id' => $activity->id]);
                app(DayController::class)->updateMany($request);
            }
            $this->services->update(
                Auth::user(),
                $activity,
                $validator->safe()->except('activity_id')
            );
        });

        return Success(payload: ['activity' => $activity->toResource()]);
    }

    public function delete(Request $request)
    {
        $validator = ActivityValidators::delete($request->all());
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->input('activity_id')
        );
        $this->services->delete($activity);

        return Success(msg: 'activity deleted');
    }

    public function toggleActivation(Request $request)
    {
        $validator = ActivityValidators::find($request->all());
        $activity = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->input('activity_id')
        );
        $this->services->toggleActivation($activity);

        return Success();
    }
}
