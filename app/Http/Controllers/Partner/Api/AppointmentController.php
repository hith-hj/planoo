<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Services\ActivityServices;
use App\Services\AppointmentServices;
use App\Validators\AppointmentValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AppointmentController extends Controller
{
    public function __construct(public AppointmentServices $services) {}

    public function all(Request $request)
    {
        $page = $request->filled('page') ? $request->integer('page') : 1;
        $perPage = $request->filled('perPage') ? $request->integer('perPage') : 10;
        $filters = $request->filled('filters') ? $request->array('filters') : [];
        $orderBy = $request->filled('orderBy') ? $request->array('orderBy') : [];
        $appointments = $this->services->allByObject(
            owner:getModel(),
            page:$page,
            perPage:$perPage,
            filters:$filters,
            orderBy:$orderBy
        );

        return Success(payload: [
            'appointments' => AppointmentResource::collection($appointments),
        ]);
    }

    public function check(Request $request)
    {
        $validator = AppointmentValidators::check($request->all());
        $activity = app(ActivityServices::class)
            ->findByUser(Auth::user(), $validator->safe()->integer('activity_id'));
        $slots = $this->services->checkAvailableSlots($activity, $validator->safe()->all());

        return Success(payload: ['slots' => $slots]);
    }

    public function create(Request $request)
    {
        $validator = AppointmentValidators::create($request->all());
        $activity = app(ActivityServices::class)
            ->findByUser(Auth::user(), $validator->safe()->integer('activity_id'));
        if ($this->services->checkAppointmentExists($validator->safe()->all())) {
            return Error('Appointment just got booked');
        }
        $appointment = $this->services->create($activity, $validator->safe()->all());

        return Success(payload: ['appointment' => AppointmentResource::make($appointment)]);
    }

    public function cancel(Request $request)
    {
        $validator = AppointmentValidators::find($request->all());
        $appointment = $this->services->find($validator->safe()->integer('appointment_id'));
        $this->services->cancel(Auth::user(), $appointment);

        return Success(payload: [
            'appointment' => AppointmentResource::make($appointment->fresh()),
        ]);
    }
}
