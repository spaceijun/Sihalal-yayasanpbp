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
     * Query params (opsional):
     *   - limit  : jumlah data (default 10, max 50)
     *   - periode: 'all' | 'bulan_ini'  (default 'all')
     */
    public function index(Request $request): JsonResponse
    {
        $limite = min((int) $request->get('limit', 10), 50);
        $periode = in_array($request->get('periode'), ['all', 'bulan_ini'])
            ? $request->get('periode')
            : 'all';

        // ── Rentang periode bergilir tanggal-25 ────────────────────────────
        $today = now();
        if ($today->day >= 25) {
            $periodeStart = $today->copy()->startOfMonth()->addDays(24);
            $periodeEnd = $periodeStart->copy()->addMonth()->subDay()->endOfDay();
        } else {
            $periodeStart = $today->copy()->subMonth()->startOfMonth()->addDays(24);
            $periodeEnd = $today->copy()->startOfMonth()->addDays(23)->endOfDay();
        }
        // ───────────────────────────────────────────────────────────────────

        $query = Enumerator::query()
            ->with(['koordinator:id,nama_lengkap'])
            ->withCount([
                'dataLapangans as total_pengajuan' => function ($q) use ($periode, $periodeStart, $periodeEnd) {
                    if ($periode === 'bulan_ini') {
                        $q->whereBetween('created_at', [$periodeStart, $periodeEnd]);
                    }
                },
            ]);

        $enumerators = $query
            ->orderByDesc('total_pengajuan')
            ->limit($limite)
            ->get();

        $maxPengajuan = $enumerators->max('total_pengajuan') ?: 1;

        $data = $enumerators->map(function (Enumerator $e, int $index) use ($maxPengajuan) {
            $words = explode(' ', trim($e->nama_lengkap));
            $inisial = strtoupper(
                substr($words[0], 0, 1).(isset($words[1]) ? substr($words[1], 0, 1) : '')
            );

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
                'rank' => $index + 1,
                'id' => $e->hashed_id,
                'nama' => $e->nama_lengkap,
                'wilayah' => optional($e->koordinator)->wilayah ?? '-',
                'inisial' => $inisial,
                'avatar_color' => $avatarColors[$index] ?? '#0043CE',
                'total_pengajuan' => $e->total_pengajuan,
                'progress_ratio' => round($e->total_pengajuan / $maxPengajuan, 4),
            ];
        });

        return response()->json([
            'success' => true,
            'periode' => $periode,
            'data' => $data,
            'meta' => [
                'total_enumerator' => $enumerators->count(),
                'max_pengajuan' => $maxPengajuan,
                'generated_at' => now()->toIso8601String(),
                'period_range' => $periode === 'bulan_ini'
                    ? [
                        'start' => $periodeStart->toDateString(),
                        'end' => $periodeEnd->toDateString(),
                    ]
                    : null,
            ],
        ]);
    }
}
