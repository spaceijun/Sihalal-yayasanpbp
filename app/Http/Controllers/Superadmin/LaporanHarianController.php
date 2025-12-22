<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Superadmin\Koordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $koordinatorId = $request->input('koordinator_id');

        // Ambil semua koordinator untuk dropdown filter
        $koordinators = Koordinator::orderBy('nama_lengkap')->get();

        $query = DataLapangan::select(
            'koordinators.id as koordinator_id',
            'koordinators.nama_lengkap as nama_koordinator',
            'enumerators.id as enumerator_id',
            'enumerators.nama_lengkap as nama_enumerator',
            DB::raw('COUNT(CASE WHEN data_lapangans.status = "PENDING" THEN 1 END) as pending'),
            DB::raw('COUNT(CASE WHEN data_lapangans.status = "PROGRESS OSS" THEN 1 END) as progress_oss'),
            DB::raw('COUNT(CASE WHEN data_lapangans.status = "PROGRESS SIHALAL" THEN 1 END) as progress_sihalal'),
            DB::raw('COUNT(CASE WHEN data_lapangans.status = "TERBIT SH" THEN 1 END) as terbit_sh'),
            DB::raw('COUNT(CASE WHEN data_lapangans.status_pembayaran = "PENDING" THEN 1 END) as pembayaran_pending'),
            DB::raw('COUNT(CASE WHEN data_lapangans.status_pembayaran = "PENGAJUAN" THEN 1 END) as pembayaran_pengajuan'),
            DB::raw('COUNT(CASE WHEN data_lapangans.status_pembayaran = "DIBAYAR" THEN 1 END) as pembayaran_dibayar'),
            DB::raw('COUNT(*) as total_data')
        )
            ->join('enumerators', 'data_lapangans.enumerator_id', '=', 'enumerators.id')
            ->join('koordinators', 'enumerators.koordinator_id', '=', 'koordinators.id')
            ->whereDate('data_lapangans.created_at', $tanggal);

        // Filter berdasarkan koordinator jika dipilih
        if ($koordinatorId) {
            $query->where('koordinators.id', $koordinatorId);
        }

        $laporan = $query->groupBy('koordinators.id', 'koordinators.nama_lengkap', 'enumerators.id', 'enumerators.nama_lengkap')
            ->orderBy('koordinators.nama_lengkap')
            ->orderBy('enumerators.nama_lengkap')
            ->get();

        // Mengelompokkan berdasarkan koordinator
        $laporanPerKoordinator = $laporan->groupBy('koordinator_id')->map(function ($items, $koordinatorId) {
            $koordinator = $items->first();
            return [
                'koordinator_id' => $koordinatorId,
                'nama_koordinator' => $koordinator->nama_koordinator,
                'total_data' => $items->sum('total_data'),
                'total_pending' => $items->sum('pending'),
                'total_progress_oss' => $items->sum('progress_oss'),
                'total_progress_sihalal' => $items->sum('progress_sihalal'),
                'total_terbit_sh' => $items->sum('terbit_sh'),
                'total_pembayaran_pending' => $items->sum('pembayaran_pending'),
                'total_pembayaran_pengajuan' => $items->sum('pembayaran_pengajuan'),
                'total_pembayaran_dibayar' => $items->sum('pembayaran_dibayar'),
                'enumerators' => $items->map(function ($item) {
                    return [
                        'enumerator_id' => $item->enumerator_id,
                        'nama_enumerator' => $item->nama_enumerator,
                        'total_data' => $item->total_data,
                        'status' => [
                            'pending' => $item->pending,
                            'progress_oss' => $item->progress_oss,
                            'progress_sihalal' => $item->progress_sihalal,
                            'terbit_sh' => $item->terbit_sh,
                        ],
                        'status_pembayaran' => [
                            'pending' => $item->pembayaran_pending,
                            'pengajuan' => $item->pembayaran_pengajuan,
                            'dibayar' => $item->pembayaran_dibayar,
                        ]
                    ];
                })->values()
            ];
        })->values();

        return view('superadmin.data-lapangan.laporan-harian.index', compact('laporanPerKoordinator', 'tanggal', 'koordinators', 'koordinatorId'));
    }
}
