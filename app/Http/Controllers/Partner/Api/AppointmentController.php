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

    public function check(Request $request)
    {
        $validator = AppointmentValidators::check($request->all());

        $slots = $this->services->checkAvailableSlots($validator->safe()->all());

        return Success(payload: ['slots' => $slots]);
    }

    public function create(Request $request)
    {
        $validator = AppointmentValidators::create($request->all());
        $activity = app(ActivityServices::class)->find($validator->safe()->integer('activity_id'));
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
