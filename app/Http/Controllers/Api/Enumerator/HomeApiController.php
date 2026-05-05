<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\CashflowsKoordinator; // ← model yang benar
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomeApiController extends Controller
{
    public function index(): JsonResponse
    {
        $enumerator = Enumerator::where('user_id', Auth::id())->firstOrFail();
        $enumeratorId = $enumerator->id;

        $pending = DataLapangan::where('enumerator_id', $enumeratorId)
            ->where('status', 'PENDING')
            ->count();

        $terverifikasi = DataLapangan::where('enumerator_id', $enumeratorId)
            ->where('status', 'TERVERIFIKASI')
            ->count();

        $progress = DataLapangan::where('enumerator_id', $enumeratorId)
            ->whereIn('status', ['PROGRESS OSS', 'PROGRESS SIHALAL'])
            ->count();

        $terbitSH = DataLapangan::where('enumerator_id', $enumeratorId)
            ->where('status', 'TERBIT SH')
            ->count();

        $revisi = DataLapangan::where('enumerator_id', $enumeratorId)
            ->where('status', 'REVISI')
            ->count();

        $dibayar = DataLapangan::where('enumerator_id', $enumeratorId)
            ->where('status_pembayaran', 'DIBAYAR')
            ->count();

        $dataMasuk = DataLapangan::where('enumerator_id', $enumeratorId)->count();

        $totalPemasukan = CashflowsKoordinator::where('tipe', 'PEMASUKAN')
            ->whereHas('dataLapangan', function ($q) use ($enumeratorId) {
                $q->where('enumerator_id', $enumeratorId);
            })
            ->sum('nominal');

        $dataLapangan = DataLapangan::with('enumerator')
            ->where('enumerator_id', $enumeratorId)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $pengajuanTerakhir = DataLapangan::with('enumerator')
            ->where('enumerator_id', $enumeratorId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Dashboard data retrieved successfully',
            'data'    => [
                'statistik' => [
                    'pending'         => $pending,
                    'terverifikasi'   => $terverifikasi,
                    'progress'        => $progress,
                    'terbit_sh'       => $terbitSH,
                    'revisi'          => $revisi,
                    'data_masuk'      => $dataMasuk,
                    'dibayar'         => $dibayar,
                    'total_pemasukan' => (float) $totalPemasukan,
                ],
                'data_lapangan'      => $dataLapangan,
                'pengajuan_terakhir' => $pengajuanTerakhir,
            ],
        ], 200);
    }
}
