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

        $dataMasuk = DataLapangan::whereHas('enumerator', function ($q) use ($koordinatorId) {
            $q->where('koordinator_id', $koordinatorId);
        })->count();

        return view('koordinator.home.index', compact('pending', 'progress', 'terbitSH', 'dataMasuk'));
    }
}
