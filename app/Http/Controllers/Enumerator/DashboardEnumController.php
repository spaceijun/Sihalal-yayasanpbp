<?php

namespace App\Http\Controllers\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardEnumController extends Controller
{
    public function index()
    {
        $enumerator = Enumerator::where('user_id', Auth::id())->firstOrFail();
        $enumeratorId = $enumerator->id;

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

        $pengajuanTerakhir = DataLapangan::with('enumerator')
            ->where('enumerator_id', $enumeratorId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();



        return view('enumerator.home.index', compact('pending', 'progress', 'terbitSH', 'revisi', 'dataMasuk', 'dataLapangan', 'dataLapanganRevisi'));
    }
}
