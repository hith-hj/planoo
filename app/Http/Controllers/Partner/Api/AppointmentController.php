<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
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
        $page = $request->integer('page', 1);
        $perPage = $request->integer('perPage', 10);
        $filters = $request->input('filters', []);
        $orderBy = $request->input('orderBy', []);
        $ownerType = $request->input('owner_type', 'activity');

        $query = $this->services->getQuery(Auth::user(), $ownerType);
        $appointments = $this->services->allByQuery($query, $page, $perPage, $filters, $orderBy);

        return Success(payload: [
            'appointments' => $appointments->toResourceCollection(),
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

        return Success(payload: ['appointment' => $appointment->toResource()]);
    }

    public function cancel(Request $request)
    {
        $validator = AppointmentValidators::find($request->all());
        $appointment = $this->services->find($validator->safe()->integer('appointment_id'));
        $this->services->cancel(Auth::user(), $appointment);

        return Success(payload: [
            'appointment' => $appointment->fresh()->toResource(),
        ]);
    }
}
