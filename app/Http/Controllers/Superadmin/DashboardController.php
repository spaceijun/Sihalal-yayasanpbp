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

        $latestData = DataLapangan::with('enumerator')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('superadmin.home.index', compact('totalDataKoordinator', 'totalDataEnumerator', 'totalDataLapangan', 'latestData', 'totalDataPending', 'totalDataProgressOSS', 'totalDataProgressSihalal', 'totalDataTerbitSH'));
    }
}
