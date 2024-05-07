<?php

namespace App\Http\Middleware;

use Closure;

class PermissionsPolicy
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set Permissions-Policy header
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=()');

        return $response;
    }
}
