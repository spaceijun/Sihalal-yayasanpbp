<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\Enumerator;
use Illuminate\Http\Request;

class RankingPendampingController extends Controller
{
    use HasRoutePrefix;

    public function index(Request $request)
    {
        $periode = in_array($request->get('periode'), ['all', 'bulan_ini'])
            ? $request->get('periode')
            : 'all';

        $limit = 10;

        $query = Enumerator::query()
            ->with('koordinator:id,nama_lengkap')
            ->withCount([
                'dataLapangans as total_pengajuan' => function ($q) use ($periode) {
                    if ($periode === 'bulan_ini') {
                        $q->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    }
                },
                'dataLapangans as terbit_sh' => function ($q) use ($periode) {
                    $q->where('status', 'TERBIT SH');
                    if ($periode === 'bulan_ini') {
                        $q->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    }
                },
                'dataLapangans as progress' => function ($q) use ($periode) {
                    $q->whereNotIn('status', ['TERBIT SH', 'DITOLAK']);
                    if ($periode === 'bulan_ini') {
                        $q->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    }
                },
            ]);

        $enumerators = $query
            ->orderByDesc('total_pengajuan')
            ->limit($limit)
            ->get();

        $maxPengajuan = $enumerators->max('total_pengajuan') ?: 1;

        $enumerators = $enumerators->map(function (Enumerator $e, int $i) use ($maxPengajuan) {
            $e->rank           = $i + 1;
            $e->progress_ratio = round($e->total_pengajuan / $maxPengajuan * 100);

            $words      = explode(' ', trim($e->nama_lengkap));
            $e->inisial = strtoupper(
                substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : '')
            );

            return $e;
        });

        $stats = [
            'total_enumerator' => Enumerator::count(),
            'total_pengajuan'  => $enumerators->sum('total_pengajuan'),
            'total_terbit_sh'  => $enumerators->sum('terbit_sh'),
            'total_progress'   => $enumerators->sum('progress'),
        ];

        $routePrefix = $this->routePrefix();

        return view('superadmin.ranking-pendamping.index', compact(
            'enumerators',
            'maxPengajuan',
            'stats',
            'periode',
            'routePrefix'));
    }
}
