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

    public function all(Request $request)
    {
        $page = $request->integer('page', 1);
        $perPage = $request->integer('perPage', 10);
        $filters = $request->input('filters', []);
        $orderBy = $request->input('orderBy', []);
        $courses = $this->services->allByFilter(
            $page,
            $perPage,
            $filters,
            $orderBy
        );

        return Success(payload: [
            'page' => $page,
            'perPage' => $perPage,
            'courses' => $courses->toResourceCollection(),
        ]);
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
