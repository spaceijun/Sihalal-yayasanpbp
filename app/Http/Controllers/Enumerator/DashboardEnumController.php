<?php

namespace App\Http\Controllers\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardEnumController extends Controller
{
    public function index()
    {
        $koordinatorId = Auth::user()->enumerator->id;

        $pending = DataLapangan::with('enumerator')
            ->where('status', 'PENDING')
            ->count();

        $progress = DataLapangan::with('enumerator')
            ->whereIn('status', ['PROGRESS OSS', 'PROGRESS SIHALAL'])
            ->count();

        $terbitSH = DataLapangan::with('enumerator')
            ->where('status', 'TERBIT SH')
            ->count();

        $revisi = DataLapangan::with('enumerator')
            ->where('status', 'REVISI')
            ->count();

        $dataMasuk = DataLapangan::with('enumerator')->count();

        $dataLapangan = DataLapangan::with('enumerator')->orderBy('created_at', 'desc')->take(20)->get();

        $dataLapanganRevisi = DataLapangan::with('enumerator')
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();



        return view('enumerator.home.index', compact('pending', 'progress', 'terbitSH', 'revisi', 'dataMasuk', 'dataLapangan', 'dataLapanganRevisi'));
    }
}
