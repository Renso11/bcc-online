<?php

namespace App\Http\Middleware;

use Closure;

class ContentSecurityPolicy
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $nonce = base64_encode(random_bytes(16));

        // Set Content Security Policy header
        //$response->headers->set('Content-Security-Policy', "style-src-elem 'self' 'nonce-$nonce' fonts.googleapis.com code.ionicframework.com");
        return $response;
    }
}
