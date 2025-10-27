<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseServices;
use App\Services\CustomerServices;
use App\Validators\CourseValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CourseController extends Controller
{
    public function __construct(public CourseServices $services) {}

    public function all()
    {
        $courses = $this->services->allByUser(Auth::user());

        return Success(payload: ['courses' => $courses->toResourceCollection()]);
    }

    public function find(Request $request)
    {
        $validator = CourseValidators::find($request->all());
        $course = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('course_id')
        );

        return Success(payload: ['course' => $course->toResource()]);
    }

    public function create(Request $request)
    {
        $validator = CourseValidators::create($request->all());
        $course = $this->services->create(
            Auth::user(),
            $validator->safe()->except(['cords', 'days', 'times', 'tags'])
        );
        $request->merge(['owner_type' => 'course', 'owner_id' => $course->id]);
        $request->offsetUnset('name');
        app(DayController::class)->createMany($request);
        app(LocationsController::class)->create($request);
        app(TagController::class)->create($request);
        app(MediaController::class)->create($request);

        return Success(payload: ['course' => $course->toResource()]);
    }

    public function update(Request $request)
    {
        $validator = CourseValidators::create($request->all(), true);
        $course = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('course_id')
        );

        $this->services->update(
            Auth::user(),
            $course,
            $validator->safe()->except('course_id')
        );

        return Success(payload: ['course' => $course->fresh()->toResource()]);
    }

    public function delete(Request $request)
    {
        $validator = CourseValidators::delete($request->all());
        $course = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('course_id')
        );
        $this->services->delete($course);

        return Success(msg: 'course deleted');
    }

    public function toggleActivation(Request $request)
    {
        $validator = CourseValidators::find($request->all());
        $course = $this->services->findByUser(
            Auth::user(),
            $validator->safe()->integer('course_id')
        );
        $this->services->toggleActivation($course);

        return Success();
    }

    public function attend(Request $request)
    {
        $validator = CourseValidators::attend($request->all());
        $course = $this->services->findByUser(Auth::user(), $validator->safe()->integer('course_id'));
        $customer = app(CustomerServices::class)->getCustomer($validator->safe()->except('course_id'));

        $this->services->attend($customer, $course);

        return Success();
    }

    public function cancel(Request $request)
    {
        $validator = CourseValidators::cancel($request->all());
        $course = $this->services->findByUser(Auth::user(), $validator->safe()->integer('course_id'));
        $customer = app(CustomerServices::class)->getCustomer($validator->safe()->except('course_id'));

        $this->services->cancel($customer, $course);

        return Success();
    }
}
