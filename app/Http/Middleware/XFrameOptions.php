<?php

namespace App\Http\Middleware;

use Closure;

class XFrameOptions
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set X-Frame-Options header
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
