<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ForceHttpsMiddleWare
{
    public function handle(Request $request, Closure $next)
    {
        // If the client connected to the frontend (load balancer) via https
        // redirection is not necessary

        if ($request->headers->has('X-Forwarded-Proto')) {
            if (strcmp($request->header('X-Forwarded-Proto'), 'https') === 0) {
                return $next($request);
            }
        }

        if (!$request->secure() && App::environment(['staging', 'production'])) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
