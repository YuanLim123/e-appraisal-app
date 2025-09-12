<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$departments): Response
    {
        if (! auth()->check()) {
            abort(401);
        }

        $authorizedUser = false;

        foreach ($departments as $department) {
            if (auth()->user()->departments()->where('name', $department)->exists()) {
                $authorizedUser = true;
                break;
            }
        }

        if (!$authorizedUser) {
            abort(403, 'You do not have the required department access.');
        }

        return $next($request);
    }
}
