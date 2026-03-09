<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEnumeratorIsActive
{
    /**
     * Blokir enumerator yang berstatus 'Tidak Aktif' dari seluruh API enumerator.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->enumerator) {
            return response()->json([
                'status'  => false,
                'message' => 'Akses ditolak. Data enumerator tidak ditemukan.',
            ], 403);
        }

        if ($user->enumerator->status === 'Tidak Aktif') {
            return response()->json([
                'status'  => false,
                'message' => 'Akun Anda telah dinonaktifkan karena tidak memenuhi target pengajuan data lapangan (minimal 20 data dalam 30 hari). Silakan hubungi koordinator Anda.',
                'data'    => [
                    'status_enumerator' => 'Tidak Aktif',
                ],
            ], 403);
        }

        return $next($request);
    }
}
