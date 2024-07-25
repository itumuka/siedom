<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user terautentikasi
        if (!$request->session()->has('admin') && !$request->session()->has('mahasiswa')) {
            return redirect()->route('login')->withErrors(['error' => 'Please login first']);
        }

        return $next($request);
    }
}
