<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerServices;
use App\Services\EventServices;
use App\Validators\EventValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class EventController extends Controller
{
    public function __construct(public EventServices $services) {}

    public function all()
    {
        $events = $this->services->allByUser(Auth::user());

        return Success(payload: ['events' => $events->toResourceCollection()]);
    }

    public function find(Request $request)
    {
        $validator = EventValidators::find($request->all());
        $event = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('event_id')
        );

        return Success(payload: ['event' => $event->toResource()]);
    }

    public function create(Request $request)
    {
        $validator = EventValidators::create($request->all());
        $event = $this->services->create(
            Auth::user(),
            $validator->safe()->except(['cords', 'days', 'times', 'tags'])
        );
        $request->merge(['owner_type' => 'event', 'owner_id' => $event->id]);
        $request->offsetUnset('name');
        app(DayController::class)->createMany($request);
        app(LocationController::class)->create($request);
        app(TagController::class)->create($request);
        app(MediaController::class)->create($request);

        return Success(payload: ['event' => $event->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = EventValidators::create($request->all(), true);
        $event = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('event_id')
        );

        $this->services->update(
            Auth::user(),
            $event,
            $validator->safe()->except('event_id')
        );

        return Success(payload: ['event' => $event->fresh()->toResource()]);
    }

    public function delete(Request $request)
    {
        $validator = EventValidators::delete($request->all());
        $event = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('event_id')
        );
        $this->services->delete($event);

        return Success(msg: 'event deleted');
    }

    public function toggleActivation(Request $request)
    {
        $validator = EventValidators::find($request->all());
        $event = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('event_id')
        );
        $this->services->toggleActivation($event);

        return Success();
    }

    public function attend(Request $request)
    {
        $validator = EventValidators::attend($request->all());
        $event = $this->services->findByUser(Auth::user(), $validator->safe()->integer('event_id'));
        $customer = app(CustomerServices::class)->getCustomer($validator->safe()->except('event_id'));

        $this->services->attend($customer, $event);

        return Success();
    }

    public function cancel(Request $request)
    {
        $validator = EventValidators::cancel($request->all());
        $event = $this->services->findByUser(Auth::user(), $validator->safe()->integer('event_id'));
        $customer = app(CustomerServices::class)->getCustomer($validator->safe()->except('event_id'));

        $this->services->cancel($customer, $event);

        return Success();
    }
}
