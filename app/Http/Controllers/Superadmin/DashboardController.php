<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
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
        $totalPembayaranPending = DataLapangan::where('status', 'TERBIT SH')->where('status_pembayaran', 'PENDING')->count();
        $totalPembayaranPengajuan = DataLapangan::where('status_pembayaran', 'PENGAJUAN')->count();
        $totalDibayar = DataLapangan::where('status_pembayaran', 'DIBAYAR')->count();
        $totalDataRevisi = DataLapangan::where('status', 'REVISI')->count();

        $latestDataToday = DataLapangan::with('enumerator')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $latestDataUpdate = DataLapangan::with('enumerator')
            ->where('status', 'Terbit SH')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        return view('superadmin.home.index', compact('totalDataKoordinator', 'totalDataEnumerator', 'totalDataLapangan', 'latestDataToday', 'latestDataUpdate', 'totalDataPending', 'totalDataProgressOSS', 'totalDataProgressSihalal', 'totalDataTerbitSH', 'totalPembayaranPending', 'totalPembayaranPengajuan', 'totalDibayar', 'totalDataRevisi'));
    }
}
