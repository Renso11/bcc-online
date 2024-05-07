<?php

namespace App\Http\Middleware;

use Closure;

class ReferrerPolicy
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set Referrer-Policy header
        $response->headers->set('Referrer-Policy', 'strict-origin');

        return $response;
    }
}
