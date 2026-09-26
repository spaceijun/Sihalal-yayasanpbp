<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class RankingPendampingController extends Controller
{
    use HasRoutePrefix;

    public function index(Request $request)
    {
        [$periode, $periodeStart, $periodeEnd] = $this->resolvePeriode($request);

        $periodRange = [
            'start' => $periodeStart->isoFormat('D MMM YYYY'),
            'end' => $periodeEnd->isoFormat('D MMM YYYY'),
        ];

        $limit = 10;

        $enumerators = $this->baseRankingQuery($periode, $periodeStart, $periodeEnd)
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

    /**
     * Return DataTables JSON untuk tabel peringkat lengkap (semua pendamping,
     * tidak dibatasi 10 seperti podium/stat cards di index()).
     */
    public function data(Request $request)
    {
        [$periode, $periodeStart, $periodeEnd] = $this->resolvePeriode($request);

        $query = $this->baseRankingQuery($periode, $periodeStart, $periodeEnd)
            ->orderByDesc('total_pengajuan');

        // Nilai pengajuan tertinggi dipakai sebagai basis persentase progress bar,
        // sama seperti $maxPengajuan pada index() (selalu sama dengan rank #1).
        $maxPengajuan = (clone $query)->limit(1)->get()->first()?->total_pengajuan ?: 1;

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function (Enumerator $e) {
                $words = explode(' ', trim($e->nama_lengkap));
                $inisial = strtoupper(substr($words[0], 0, 1).(isset($words[1]) ? substr($words[1], 0, 1) : ''));
                $wilayah = optional($e->koordinator)->wilayah ?? '-';

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.e($inisial).'</div>
                    <div>
                        <strong>'.e($e->nama_lengkap).'</strong>
                        <small style="color:var(--adm-text-muted);display:block;margin-top:2px;">'.e($wilayah).'</small>
                    </div>
                </div>';
            })
            ->addColumn('total_fmt', fn (Enumerator $e) => number_format($e->total_pengajuan))
            ->addColumn('terbit_fmt', fn (Enumerator $e) => number_format($e->terbit_sh))
            ->addColumn('progress_cell', function (Enumerator $e) use ($maxPengajuan) {
                $ratio = round($e->total_pengajuan / $maxPengajuan * 100);

                return '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:5px;min-width:72px;">
                    <span style="font-size:11.5px;font-weight:700;color:var(--adm-blue);">'.$ratio.'%</span>
                    <div style="width:72px;height:6px;border-radius:99px;background:var(--adm-border-mid);overflow:hidden;">
                        <div style="height:100%;border-radius:99px;background:var(--adm-blue);width:'.$ratio.'%;"></div>
                    </div>
                </div>';
            })
            ->rawColumns(['nama_cell', 'progress_cell'])
            ->make(true);
    }

    /**
     * Hitung rentang periode bergilir tanggal-25.
     * Periode berjalan: tgl 25 bulan lalu 00:00 s.d. tgl 24 bulan ini 23:59.
     * Reset terjadi setiap kali tanggal menyentuh angka 25.
     *
     * @return array{0: string, 1: \Illuminate\Support\Carbon, 2: \Illuminate\Support\Carbon}
     */
    private function resolvePeriode(Request $request): array
    {
        $periode = in_array($request->get('periode'), ['all', 'bulan_ini'])
            ? $request->get('periode')
            : 'all';

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

        return [$periode, $periodeStart, $periodeEnd];
    }

    /**
     * Query dasar ranking enumerator berdasarkan total pengajuan.
     */
    private function baseRankingQuery(string $periode, Carbon $periodeStart, Carbon $periodeEnd)
    {
        return Enumerator::query()
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
    }
}
