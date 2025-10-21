<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

final class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $guard = null;

            if ($request->is('*api/partner/*')) {
                $guard = 'partner:api';
            } elseif ($request->is('*api/customer/*')) {
                $guard = 'customer:api';
            } else {
                throw new Exception('Invalid route guard', 422);
            }
            auth()->shouldUse($guard);
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return Error(msg: "Invaid Token: {$e->getMessage()}", code: 401);
        }

        return $next($request);
    }
}
