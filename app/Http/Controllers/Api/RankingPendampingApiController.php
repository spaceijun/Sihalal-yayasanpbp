<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RankingPendampingApiController extends Controller
{
    /**
     * GET /api/ranking-pendamping
     *
     * Mengembalikan top-10 enumerator berdasarkan total DataLapangan
     * yang sudah terbit SH (status = 'TERBIT_SH') dan data pengajuan lainnya.
     *
     * Query params (opsional):
     *   - limit  : jumlah data (default 10, max 50)
     *   - periode: 'all' | 'bulan_ini' | 'tahun_ini'  (default 'all')
     */
    public function index(Request $request): JsonResponse
    {
        $limit   = min((int) $request->get('limit', 10), 50);
        $periode = $request->get('periode', 'all');

        // ── Base query ────────────────────────────────────────────────────
        $query = Enumerator::query()
            ->with(['koordinator:id,nama_lengkap', 'dataLapangans'])
            ->withCount([
                // total semua pengajuan
                'dataLapangans as total_pengajuan',

                // sudah terbit SH
                'dataLapangans as terbit_sh' => function ($q) {
                    $q->where('status', 'TERBIT SH');
                },

                // masih dalam proses (bukan TERBIT_SH dan bukan DITOLAK)
                'dataLapangans as progress' => function ($q) {
                    $q->whereNotIn('status', ['TERBIT SH', 'DITOLAK']);
                },
            ]);

        // ── Filter periode ────────────────────────────────────────────────
        if ($periode === 'bulan_ini') {
            $query->withCount([
                'dataLapangans as total_pengajuan' => fn($q) =>
                $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'dataLapangans as terbit_sh' => fn($q) =>
                $q->where('status', 'TERBIT SH')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'dataLapangans as progress' => fn($q) =>
                $q->whereNotIn('status', ['TERBIT SH', 'DITOLAK'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
            ]);
        } elseif ($periode === 'tahun_ini') {
            $query->withCount([
                'dataLapangans as total_pengajuan' => fn($q) =>
                $q->whereYear('created_at', now()->year),
                'dataLapangans as terbit_sh' => fn($q) =>
                $q->where('status', 'TERBIT SH')
                    ->whereYear('created_at', now()->year),
                'dataLapangans as progress' => fn($q) =>
                $q->whereNotIn('status', ['TERBIT SH', 'DITOLAK'])
                    ->whereYear('created_at', now()->year),
            ]);
        }

        // ── Urutkan dan ambil ─────────────────────────────────────────────
        $enumerators = $query
            ->orderByDesc('total_pengajuan')
            ->limit($limit)
            ->get();

        // ── Nilai max untuk kalkulasi persentase di Flutter ───────────────
        $maxPengajuan = $enumerators->max('total_pengajuan') ?: 1;

        // ── Format response ───────────────────────────────────────────────
        $data = $enumerators->map(function (Enumerator $e, int $index) use ($maxPengajuan) {
            // Ambil 2 huruf pertama nama sebagai inisial
            $words   = explode(' ', trim($e->nama_lengkap));
            $inisial = strtoupper(
                substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : '')
            );

            // Warna avatar berdasarkan posisi rank (konsisten dengan Flutter dummy)
            $avatarColors = [
                '#0043CE',
                '#8A3FFC',
                '#007D79',
                '#DA1E28',
                '#0CA678',
                '#FF832B',
                '#0F62FE',
                '#9C27B0',
                '#00838F',
                '#558B2F',
            ];

            return [
                'rank'             => $index + 1,
                'id'               => $e->hashed_id,        // gunakan hashed id
                'nama'             => $e->nama_lengkap,
                'wilayah'          => optional($e->koordinator)->wilayah ?? '-',
                'inisial'          => $inisial,
                'avatar_color'     => $avatarColors[$index] ?? '#0043CE',
                'total_pengajuan'  => $e->total_pengajuan,
                'terbit_sh'        => $e->terbit_sh,
                'progress'         => $e->progress,
                // 0.0 – 1.0, dipakai Flutter untuk LinearProgressIndicator
                'progress_ratio'   => round($e->total_pengajuan / $maxPengajuan, 4),
            ];
        });

        return response()->json([
            'success' => true,
            'periode' => $periode,
            'data'    => $data,
            'meta'    => [
                'total_enumerator' => $enumerators->count(),
                'max_pengajuan'    => $maxPengajuan,
                'generated_at'     => now()->toIso8601String(),
            ],
        ]);
    }
}
