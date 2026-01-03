<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataLapanganController extends Controller
{

    public function index(Request $request)
    {
        $query = DataLapangan::with('enumerator')
            ->whereHas('enumerator', function ($q) {
                $q->where('koordinator_id', Auth::user()->koordinator->id);
            });

        // Filter berdasarkan nama PU
        if ($request->filled('nama_pu')) {
            $query->where('nama_pu', 'like', '%' . $request->nama_pu . '%');
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $dataLapangans = $query->latest()->paginate(10)->appends($request->all());

        return view('koordinator.data-lapangan.index', compact('dataLapangans'));
    }
    public function show($id): View
    {
        $dataLapangan = DataLapangan::with('enumerator')->find($id);


        return view('koordinator.data-lapangan.show', compact('dataLapangan'));
    }

    /**
     * Check if a nik exists in the database
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNik(Request $request)
    {
        $nik = $request->nik;

        $exists = DataLapangan::where('nik', $nik)->first();

        return response()->json([
            'exists' => $exists ? true : false,
            'nama_pu' => $exists ? $exists->nama_pu : null
        ]);
    }
}
