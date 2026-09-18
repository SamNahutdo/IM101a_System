<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please authenticate to access FalconSystem.');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your FalconSystem account is deactivated. Contact an Administrator.');
        }

        // Check if user possesses any of the requested roles
        if (!empty($roles) && !$user->hasRole($roles)) {
            // If user has a different valid role, redirect them to their respective dashboard
            $primary = $user->getPrimaryRole();
            if ($primary) {
                return redirect()->route("{$primary->name}.dashboard")
                                 ->with('error', 'Unauthorized access attempt. You do not possess clearance for that module.');
            }
            abort(403, 'Unauthorized access to FalconSystem resource.');
        }

        return $next($request);
    }
}
