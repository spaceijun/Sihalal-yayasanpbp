<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $koordinatorId = Auth::user()->koordinator->id;

        $pending = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })
            ->where('status', 'PENDING')
            ->count();

        $progress = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })
            ->whereIn('status', ['PROGRESS OSS', 'PROGRESS SIHALAL'])
            ->count();

        $terbitSH = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })
            ->where('status', 'TERBIT SH')
            ->count();

        $revisi = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })
            ->where('status', 'REVISI')
            ->count();

        $dataMasuk = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })->count();

        $dataLapangan = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })->orderBy('created_at', 'desc')->take(20)->get();

        $dataLapanganRevisi = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();



        return view('koordinator.home.index', compact('pending', 'progress', 'terbitSH', 'revisi', 'dataMasuk', 'dataLapangan', 'dataLapanganRevisi'));
    }
}
