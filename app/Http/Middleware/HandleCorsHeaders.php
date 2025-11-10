<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleCorsHeaders
{
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader('Authorization')) {
            $request->headers->set('Authorization', $request->header('Authorization'));
        }
        return $next($request);
    }
}