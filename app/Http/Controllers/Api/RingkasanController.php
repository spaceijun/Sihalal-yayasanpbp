<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;

class RingkasanController extends Controller
{
    /**
     * Display a summary of data.
     */
    public function index()
    {
        $totalDataKoordinator = Koordinator::count();
        $totalDataEnumerator = Enumerator::count();
        $totalDataLapangan = DataLapangan::count();
        $totalDataPending = DataLapangan::where('status', 'Pending')->count();
        $totalDataProgressOSS = DataLapangan::where('status', 'Progress OSS')->count();
        $totalDataProgressSihalal = DataLapangan::where('status', 'Progress SiHalal')->count();
        $totalDataTerbitSH = DataLapangan::where('status', 'Terbit SH')->count();
        $totalDataRevisi = DataLapangan::where('status', 'REVISI')->count();
        $totalDataTerverifikasi = DataLapangan::where('status', 'TERVERIFIKASI')->count();

        // Data masuk per bulan (semua data)
        $dataMasukPerBulan = DataLapangan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        // Data Terbit SH per bulan (semua data status Terbit SH)
        $dataTerbitSHPerBulan = DataLapangan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('status', 'Terbit SH')
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data ringkasan berhasil diambil',
            'data' => [
                'totalDataKoordinator' => $totalDataKoordinator,
                'totalDataEnumerator' => $totalDataEnumerator,
                'totalDataLapangan' => $totalDataLapangan,
                'totalDataPending' => $totalDataPending,
                'totalDataProgressOSS' => $totalDataProgressOSS,
                'totalDataProgressSihalal' => $totalDataProgressSihalal,
                'totalDataTerbitSH' => $totalDataTerbitSH,
                'totalDataRevisi' => $totalDataRevisi,
                'totalDataTerverifikasi' => $totalDataTerverifikasi,
                'dataMasukPerBulan' => $dataMasukPerBulan,
                'dataTerbitSHPerBulan' => $dataTerbitSHPerBulan,
            ],
        ], 200);
    }
}
