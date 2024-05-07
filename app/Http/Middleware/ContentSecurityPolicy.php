<?php

namespace App\Http\Middleware;

use Closure;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set Content Security Policy header
        $response->headers->set('Content-Security-Policy', "style-src-elem 'self' fonts.googleapis.com code.ionicframework.com");
        return $response;
    }
}
