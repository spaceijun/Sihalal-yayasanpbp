<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataPendampingController extends Controller
{
    public function index()
    {
        $koordinatorId = Auth::user()->koordinator->id;

        $enumerators = Enumerator::where('koordinator_id', $koordinatorId)->latest()->paginate(10);

        $terbitSh = DataLapangan::select('enumerator_id', DB::raw('COUNT(*) as total'))
            ->whereHas('enumerator', function ($q) use ($koordinatorId) {
                $q->where('koordinator_id', $koordinatorId);
            })
            ->where('status', 'TERBIT SH')
            ->groupBy('enumerator_id')
            ->with('enumerator')
            ->get();

        $dataDibayar = DataLapangan::select('enumerator_id', DB::raw('COUNT(*) as total'))
            ->whereHas('enumerator', function ($q) use ($koordinatorId) {
                $q->where('koordinator_id', $koordinatorId);
            })
            ->where('status_pembayaran', 'DIBAYAR')
            ->groupBy('enumerator_id')
            ->with('enumerator')
            ->get();

        return view('koordinator.data-pendamping.index', compact('enumerators', 'terbitSh', 'dataDibayar'));
    }
    /**
     * Show the specified enumerator.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $enumerator = Enumerator::with('koordinator')->where('koordinator_id', Auth::user()->koordinator->id)->findOrFail($id);

        return view('koordinator.data-pendamping.show', compact('enumerator'));
    }

    /**
     * Generate and display Surat Tugas
     */
    public function suratTugas($id)
    {
        $enumerator = Enumerator::with('koordinator')->findOrFail($id);

        return view('superadmin.enumerator.partials.surat', compact('enumerator'));
    }

    /**
     * Generate ID Card as HTML (will be converted to image via html2canvas in frontend)
     */
    public function idCard($id)
    {
        $enumerator = Enumerator::find($id);

        return view('superadmin.enumerator.partials.idcard', compact('enumerator'));
    }

    /**
     * Show data lapangan from a specified enumerator
     *
     * @param int $id the id of the enumerator
     * @return \Illuminate\View\View
     */
    public function dataLapangan($id)
    {
        $enumerator = Enumerator::with('koordinator')
            ->where('koordinator_id', Auth::user()->koordinator->id)
            ->findOrFail($id);

        $dataLapangan = DataLapangan::where('enumerator_id', $enumerator->id)
            ->latest()
            ->paginate(20);

        $totalTerbitSh = DataLapangan::where('enumerator_id', $enumerator->id)
            ->where('status', 'TERBIT SH')
            ->count();

        $totalPembayaranPending = DataLapangan::where('enumerator_id', $enumerator->id)
            ->where('status_pembayaran', 'PENDING')
            ->count();

        $totalDibayar = DataLapangan::where('enumerator_id', $enumerator->id)
            ->where('status_pembayaran', 'DIBAYAR')
            ->count();

        return view('koordinator.data-pendamping.data-lapangan', compact('enumerator', 'dataLapangan', 'totalTerbitSh', 'totalPembayaranPending', 'totalDibayar'));
    }
}
