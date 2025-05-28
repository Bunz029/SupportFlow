<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized action.');
        }

        foreach ($roles as $role) {
            if (strtolower($request->user()->role) === strtolower($role)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action. You do not have the necessary role to access this resource.');
    }
} 