<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $defaultLocale = config('app.locale', 'en');
        if ($request->wantsJson()) {
            if ($request->hasHeader('Accept-Language')) {
                $locale = $request->header('Accept-Language');
            } elseif ($request->hasHeader('X-Language')) {
                $locale = $request->header('X-Language');
            } else {
                $locale = $defaultLocale;
            }
        } else {
            if (session()->has('locale')) {
                $locale = session()->get('locale');
            } else {
                $locale = $defaultLocale;
            }
        }

        if (! in_array($locale, ['en', 'ar'])) {
            $locale = $defaultLocale;
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
