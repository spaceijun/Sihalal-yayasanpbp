<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\Request;

class RankingPendampingController extends Controller
{
    /**
     * Tampilkan halaman ranking pendamping (enumerator) untuk superadmin.
     * GET /superadmin/ranking-pendamping
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'all');
        $limit   = 10;

        // ── Base query ────────────────────────────────────────────────────
        $query = Enumerator::query()
            ->with('koordinator:id,nama_lengkap')
            ->withCount([
                'dataLapangans as total_pengajuan',
                'dataLapangans as terbit_sh' => fn($q) =>
                $q->where('status', 'TERBIT_SH'),
                'dataLapangans as progress' => fn($q) =>
                $q->whereNotIn('status', ['TERBIT_SH', 'DITOLAK']),
            ]);

        // ── Filter periode ────────────────────────────────────────────────
        if ($periode === 'bulan_ini') {
            $query->withCount([
                'dataLapangans as total_pengajuan' => fn($q) =>
                $q->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'dataLapangans as terbit_sh' => fn($q) =>
                $q->where('status', 'TERBIT_SH')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'dataLapangans as progress' => fn($q) =>
                $q->whereNotIn('status', ['TERBIT_SH', 'DITOLAK'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
            ]);
        } elseif ($periode === 'tahun_ini') {
            $query->withCount([
                'dataLapangans as total_pengajuan' => fn($q) =>
                $q->whereYear('created_at', now()->year),
                'dataLapangans as terbit_sh' => fn($q) =>
                $q->where('status', 'TERBIT_SH')
                    ->whereYear('created_at', now()->year),
                'dataLapangans as progress' => fn($q) =>
                $q->whereNotIn('status', ['TERBIT_SH', 'DITOLAK'])
                    ->whereYear('created_at', now()->year),
            ]);
        }

        $enumerators = $query
            ->orderByDesc('total_pengajuan')
            ->limit($limit)
            ->get();

        $maxPengajuan = $enumerators->max('total_pengajuan') ?: 1;

        // Tambahkan rank & progress_ratio ke setiap item
        $enumerators = $enumerators->map(function (Enumerator $e, int $i) use ($maxPengajuan) {
            $e->rank           = $i + 1;
            $e->progress_ratio = $maxPengajuan > 0
                ? round($e->total_pengajuan / $maxPengajuan * 100)
                : 0;

            $words         = explode(' ', trim($e->nama_lengkap));
            $e->inisial    = strtoupper(
                substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : '')
            );

            return $e;
        });

        // Statistik ringkasan
        $stats = [
            'total_enumerator' => Enumerator::count(),
            'total_pengajuan'  => $enumerators->sum('total_pengajuan'),
            'total_terbit_sh'  => $enumerators->sum('terbit_sh'),
            'total_progress'   => $enumerators->sum('progress'),
        ];

        return view('superadmin.ranking-pendamping.index', compact(
            'enumerators',
            'maxPengajuan',
            'stats',
            'periode',
        ));
    }
}
