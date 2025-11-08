<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\EventServices;
use App\Validators\EventValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class EventController extends Controller
{
    public function __construct(public EventServices $services) {}

    public function all(Request $request)
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('perPage', 10);
        $filters = $request->input('filters', []);
        $orderBy = $request->input('orderBy', []);
        $events = $this->services->allByFilter(
            $page,
            $perPage,
            $filters,
            $orderBy
        );

        return Success(payload: [
            'page' => $page,
            'perPage' => $perPage,
            'events' => $events->toResourceCollection(),
        ]);
    }

    public function find(Request $request)
    {
        $validator = EventValidators::find($request->all());
        $event = $this->services->find($validator->safe()->integer('event_id'));

        return Success(payload: ['event' => $event->toResource()]);
    }

    public function attend(Request $request)
    {
        $validator = EventValidators::attend($request->all());
        $event = $this->services->find($validator->safe()->integer('event_id'));

        $this->services->attend(Auth::user(), $event);

        return Success();
    }

    public function cancel(Request $request)
    {
        $validator = EventValidators::cancel($request->all());
        $event = $this->services->find($validator->safe()->integer('event_id'));

        $this->services->cancel(Auth::user(), $event);

        return Success();
    }
}
