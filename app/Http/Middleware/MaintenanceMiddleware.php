<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceMiddleware
{
    /**
     * Blokir akses berdasarkan flag maintenance per-role.
     *
     * - data_entry  : MAINTENANCE_DATA_ENTRY=true  → tampilkan halaman maintenance
     * - admin_umum  : MAINTENANCE_ADMIN_UMUM=true  → tampilkan halaman maintenance
     * - Superadmin  : tidak pernah diblokir
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Maintenance untuk role data_entry
            if (
                $user->role === 'data_entry' &&
                config('app.maintenance_data_entry')
            ) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('maintenance_message', 'Sistem sedang dalam pemeliharaan untuk Data Entry. Silakan coba beberapa saat lagi.');
            }

            // Maintenance untuk role admin_umum
            if (
                $user->role === 'admin_umum' &&
                config('app.maintenance_admin_umum')
            ) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('maintenance_message', 'Sistem sedang dalam pemeliharaan untuk Admin Umum. Silakan coba beberapa saat lagi.');
            }
        }

        return $next($request);
    }
}
