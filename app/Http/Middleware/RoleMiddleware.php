<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }

        // Check if the authenticated user's role is allowed
        if (!in_array(Auth::user()->user_type, $roles)) {
            abort(403, 'Unauthorized access'); // Abort with 403 if not authorized
        }

        return $next($request);
    }
}
    