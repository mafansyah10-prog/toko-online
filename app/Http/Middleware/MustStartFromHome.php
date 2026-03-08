<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustStartFromHome
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('visited_home')) {
            return redirect()->route('home')->with('error', 'Silakan mulai dari Beranda terlebih dahulu.');
        }

        return $next($request);
    }
}
