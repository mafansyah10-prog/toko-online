<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPosUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Check if user is hitting the admin dashboard index
        if ($user && trim($request->path(), '/') === 'admin') {
            
            // If they are super_admin or they have explicit dashboard access, let them through
            if ($user->hasRole('super_admin') || $user->can('View:Dashboard')) {
                return $next($request);
            }

            // Otherwise, if they only have POS access, redirect them
            if ($user->can('View:PosPage')) {
                return redirect('/admin/pos-page');
            }
        }

        return $next($request);
    }
}
