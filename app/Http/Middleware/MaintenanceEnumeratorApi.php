<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blokir endpoint POST/PUT/PATCH pada API enumerator saat maintenance aktif.
 * Endpoint GET (baca data) tetap bisa diakses.
 */
class MaintenanceEnumeratorApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            config('app.maintenance_enumerator_api') &&
            in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'])
        ) {
            return response()->json([
                'status'  => false,
                'message' => 'Sistem sedang dalam pemeliharaan. Pengajuan dan pembaruan data tidak dapat dilakukan saat ini. Silakan coba beberapa saat lagi.',
                'maintenance' => true,
            ], 503);
        }

        return $next($request);
    }
}
