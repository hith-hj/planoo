<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseServices;
use App\Validators\CourseValidators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class CourseController extends Controller
{
    public function __construct(public CourseServices $services) {}

    public function all()
    {
        $courses = $this->services->all();

        return Success(payload: ['courses' => $courses->toResourceCollection()]);
    }

    public function find(Request $request)
    {
        $validator = CourseValidators::find($request->all());
        $course = $this->services->find(
            $validator->safe()->integer('course_id')
        );

        return Success(payload: ['course' => $course->toResource()]);
    }

    public function attend(Request $request)
    {
        $validator = CourseValidators::find($request->all());

        $course = $this->services->find($validator->safe()->integer('course_id'));

        $this->services->attend(Auth::user(), $course);

        return Success();
    }

    public function cancel(Request $request)
    {
        $validator = CourseValidators::find($request->all());

        $course = $this->services->find($validator->safe()->integer('course_id'));

        $this->services->cancel(Auth::user(), $course);

        return Success();
    }
}
