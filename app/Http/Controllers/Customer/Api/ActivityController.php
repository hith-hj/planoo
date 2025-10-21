<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Validators\ActivityValidators;
use Illuminate\Http\Request;

final class ActivityController extends Controller
{
    public function __construct(public ActivityServices $services) {}

    public function all()
    {
        $activities = $this->services->all();

        return Success(payload: ['activities' => $activities->toResourceCollection()]);
    }

    public function find(Request $request)
    {
        $validator = ActivityValidators::find($request->all());
        $activity = $this->services->find($validator->safe()->integer('activity_id'));

        return Success(payload: ['activity' => $activity->toResource()]);
    }
}
