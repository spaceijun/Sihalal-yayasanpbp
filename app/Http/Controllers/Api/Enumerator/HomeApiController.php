<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeApiController extends Controller
{
    public function index(): JsonResponse
    {
        $enumeratorId = Auth::user()->enumerator->id;

        $pending = DataLapangan::where('enumerator_id', $enumeratorId)
            ->where('status', 'PENDING')
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

        $dataLapangan = DataLapangan::with('enumerator')
            ->where('enumerator_id', $enumeratorId)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // 10 pengajuan terakhir (terbaru berdasarkan created_at)
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
                    'pending'    => $pending,
                    'progress'   => $progress,
                    'terbit_sh'  => $terbitSH,
                    'revisi'     => $revisi,
                    'data_masuk' => $dataMasuk,
                    'dibayar'    => $dibayar,
                ],
                'data_lapangan'      => $dataLapangan,
                'pengajuan_terakhir' => $pengajuanTerakhir,
            ],
        ], 200);
    }
}
