<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use Closure;
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

            if ($request->is('api/partner/*') || str_contains($request->getPathInfo(), 'api/partner')) {
                $guard = 'partner:api';
            } elseif ($request->is('api/customer/*') || str_contains($request->getPathInfo(), 'api/customer')) {
                $guard = 'customer:api';
            } else {
                return Error(msg: 'Invalid route guard', code: 422);
            }

            auth()->shouldUse($guard);
            $user = JWTAuth::parseToken()->authenticate();
            if (! $user) {
                return Error(msg: 'User Not Found', code: 416);
            }
        } catch (JWTException $e) {
            return Error(msg: "Invalid Token: {$e->getMessage()}", code: 401);
        }

        return $next($request);
    }
}
