<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Secure File Download Middleware
 *
 * Memastikan file sensitif hanya bisa di-download oleh user yang authorized.
 * Middleware ini sebaiknya digunakan pada route download yang memerlukan
 * authorization check tambahan.
 */
class SecureFileDownload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifikasi user sudah login
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please login first.',
            ], 401);
        }

        // Verifikasi user memiliki role yang diizinkan
        $allowedRoles = $this->getAllowedRoles();
        $userRole = auth()->user()->role;

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * Get list of roles allowed to download files
     */
    private function getAllowedRoles(): array
    {
        return [
            'superadmin',
            'admin_umum',
            'data_entry',
            'enumerator',
            'koordinator',
        ];
    }
}
