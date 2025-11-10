<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationServices;
use App\Validators\NotificationValidators;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class NotificationController extends Controller
{
    public function __construct(public NotificationServices $services) {}

    public function all(Request $request): JsonResponse
    {
        $notis = $this->services->all(Auth::user());

        return Success(payload: ['notifications' => $notis->toResourceCollection()]);
    }

    public function find(Request $request): JsonResponse
    {
        $validator = NotificationValidators::find($request->all());

        $noti = $this->services->findByNotifiable(
            Auth::user(),
            $validator->safe()->integer('notification_id')
        );

        return Success(payload: ['notification' => $noti->toResource()]);
    }

    public function view(Request $request): JsonResponse
    {
        $validator = NotificationValidators::view($request->all());

        $this->services->view($validator->safe()->array('notifications'));

        return Success();
    }

    public function delete(Request $request)
    {
        $validator = NotificationValidators::delete($request->all());

        $notification = $this->services->findByNotifiable(
            Auth::user(),
            $validator->safe()->integer('notification_id')
        );
        if ($notification->isBelongTo(Auth::user())) {
            $this->services->delete($notification);

            return Success();
        }

        return Error(msg: __('main.un authorized'), code: 403);
    }

    public function clear()
    {
        $this->services->clear(Auth::user());

        return Success();
    }
}
