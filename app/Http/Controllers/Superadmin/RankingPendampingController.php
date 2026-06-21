<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;

class RankingPendampingController extends Controller
{
    use HasRoutePrefix;

    public function index(Request $request)
    {
        $periode = in_array($request->get('periode'), ['all', 'bulan_ini'])
            ? $request->get('periode')
            : 'all';

        // ── Hitung rentang periode bergilir tanggal-25 ──────────────────────
        // Periode berjalan: tgl 25 bulan lalu 00:00 s.d. tgl 24 bulan ini 23:59
        // Reset terjadi setiap kali tanggal menyentuh angka 25.
        $today = now();
        if ($today->day >= 25) {
            // Sudah melewati tanggal 25 bulan ini → periode mulai tgl 25 bulan ini
            $periodeStart = $today->copy()->startOfMonth()->addDays(24); // tgl 25
            $periodeEnd = $periodeStart->copy()->addMonth()->subDay()->endOfDay(); // tgl 24 bulan depan 23:59
        } else {
            // Belum sampai tgl 25 → periode mulai tgl 25 bulan lalu
            $periodeStart = $today->copy()->subMonth()->startOfMonth()->addDays(24); // tgl 25 bulan lalu
            $periodeEnd = $today->copy()->startOfMonth()->addDays(23)->endOfDay(); // tgl 24 bulan ini 23:59
        }

        $periodRange = [
            'start' => $periodeStart->isoFormat('D MMM YYYY'),
            'end' => $periodeEnd->isoFormat('D MMM YYYY'),
        ];
        // ────────────────────────────────────────────────────────────────────

        $limit = 10;

        $query = Enumerator::query()
            ->with('koordinator:id,nama_lengkap')
            ->withCount([
                'dataLapangans as total_pengajuan' => function ($q) use ($periode, $periodeStart, $periodeEnd) {
                    if ($periode === 'bulan_ini') {
                        $q->whereBetween('created_at', [$periodeStart, $periodeEnd]);
                    }
                },
                'dataLapangans as terbit_sh' => function ($q) use ($periode, $periodeStart, $periodeEnd) {
                    $q->where('status', 'TERBIT SH');
                    if ($periode === 'bulan_ini') {
                        $q->whereBetween('created_at', [$periodeStart, $periodeEnd]);
                    }
                },
                'dataLapangans as progress' => function ($q) use ($periode, $periodeStart, $periodeEnd) {
                    $q->whereNotIn('status', ['TERBIT SH', 'DITOLAK']);
                    if ($periode === 'bulan_ini') {
                        $q->whereBetween('created_at', [$periodeStart, $periodeEnd]);
                    }
                },
            ]);

        $enumerators = $query
            ->orderByDesc('total_pengajuan')
            ->limit($limit)
            ->get();

        $maxPengajuan = $enumerators->max('total_pengajuan') ?: 1;

        $enumerators = $enumerators->map(function (Enumerator $e, int $i) use ($maxPengajuan) {
            $e->rank = $i + 1;
            $e->progress_ratio = round($e->total_pengajuan / $maxPengajuan * 100);

            $words = explode(' ', trim($e->nama_lengkap));
            $e->inisial = strtoupper(
                substr($words[0], 0, 1).(isset($words[1]) ? substr($words[1], 0, 1) : '')
            );

            return $e;
        });

        $stats = [
            'total_enumerator' => Enumerator::count(),
            'total_pengajuan' => $enumerators->sum('total_pengajuan'),
            'total_terbit_sh' => $enumerators->sum('terbit_sh'),
            'total_progress' => $enumerators->sum('progress'),
        ];

        $routePrefix = $this->routePrefix();

        return view('superadmin.ranking-pendamping.index', compact(
            'enumerators',
            'maxPengajuan',
            'stats',
            'periode',
            'periodRange',
            'routePrefix'));
    }
}
