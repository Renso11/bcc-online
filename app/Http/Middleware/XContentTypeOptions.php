<?php

namespace App\Http\Middleware;

use Closure;

class XContentTypeOptions
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set X-Content-Type-Options header
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
