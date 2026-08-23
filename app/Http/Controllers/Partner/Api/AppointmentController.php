<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityServices;
use App\Services\AppointmentServices;
use App\Services\CodeServices;
use App\Services\CustomerServices;
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
        $filters = $request->array('filters');
        $orderBy = $request->array('orderBy');
        $ownerType = request('owner_type', 'activity');
        $paginate = $request->boolean('paginate', true);

        $query = $this->services->getUserQuery(Auth::user(), $ownerType);
        $perPage = $paginate === false ? $query->count() : $perPage;
        $appointments = $this->services->allByQuery($query, $page, $perPage, $filters, $orderBy, $paginate);

        return Success(payload: [
            'page' => $page,
            'perPage' => $perPage,
            'appointments' => $appointments->toResourceCollection(),
        ]);
    }

    public function accepted(Request $request)
    {
        $orderBy = $request->array('orderBy');
        $ownerType = request('owner_type', 'activity');

        $query = $this->services->getUserQuery(Auth::user(), $ownerType);
        $appointments = $this->services->acceptedByQuery($query, $orderBy);

        return Success(payload: [
            'appointments' => $appointments->toResourceCollection(),
        ]);
    }

    public function find(Request $request)
    {
        $validator = AppointmentValidators::find($request->all());
        $appointment = $this->services->find($validator->safe()->integer('appointment_id'));

        return Success(payload: [
            'appointment' => $appointment->toResource(),
        ]);
    }

    public function check(Request $request, ActivityServices $activityServices)
    {
        $validator = AppointmentValidators::check($request->all());
        $activity = $activityServices->findByUser(Auth::user(), $validator->safe()->integer('activity_id'));
        $slots = $this->services->checkAvailableSlots($activity, $validator->safe()->all());

        return Success(payload: ['slots' => $slots]);
    }

    public function create(
        Request $request,
        ActivityServices $activityServices,
        CodeServices $codeServices,
        CustomerServices $customerServices
    ) {
        $validator = AppointmentValidators::create($request->all());

        $code = $codeServices->codeById($validator->safe()->integer('code'));
        Truthy(! $code->isValid(), 'Invalid code');
        $codeServices->deleteCode($code);

        $activity = $activityServices
            ->findByUser(Auth::user(), $validator->safe()->integer('activity_id'));
        if ($this->services->checkAppointmentExists($activity, $validator->safe()->all())) {
            return Error('appointment just got booked');
        }
        $customer = $customerServices->getCustomer($validator->safe()->all());

        $appointment = $this->services->create($activity, $validator->safe()->all(), $customer);

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
