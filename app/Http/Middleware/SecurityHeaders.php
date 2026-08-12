<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking by restricting iframe embedding
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent browsers from sniffing MIME types away from declared content-types
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Control how much referrer information is shared with external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features and APIs (like geolocation, camera, microphone)
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Enforce HTTPS communication via HSTS (only add in production)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self'; frame-ancestors 'none';");
        }

        // $scriptSrc = "'self'";
        // $styleSrc = "'self'";

        // // Dynamically allow Vite during local development
        // if (app()->environment('local')) {
        //     // Allows both IPv4 and IPv6 localhost variants for Vite
        //     $scriptSrc .= " http://localhost:5173 http://[::1]:5173 ws://localhost:5173 ws://[::1]:5173";
        //     $styleSrc .= " http://localhost:5173 http://[::1]:5173 'unsafe-inline'";
        // }

        // // Set the final headers
        // $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src $scriptSrc; style-src $styleSrc;");

        return $response;
    }
}
