<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized action.');
        }

        $user = $request->user();

        // Support pipe-separated roles, e.g. role:superadmin|admin_umum
        $allowedRoles = explode('|', $role);

        // Check via Spatie hasRole OR via kolom `role` di tabel users
        $hasAccess = false;
        foreach ($allowedRoles as $r) {
            if ($user->hasRole($r) || $user->role === $r) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
