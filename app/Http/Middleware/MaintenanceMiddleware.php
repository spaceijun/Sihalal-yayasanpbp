<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.maintenance_mode') && Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'data_entry') {
                return response()->view('maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
