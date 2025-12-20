<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataLapanganController extends Controller
{

    public function index()
    {
        $dataLapangans = DataLapangan::with('enumerator')
            ->whereHas('enumerator', function ($q) {
                $q->where('koordinator_id', Auth::user()->koordinator->id);
            })->latest()->paginate(10); // ← jumlah data per halaman

        return view('koordinator.data-lapangan.index', compact('dataLapangans'));
    }

    public function show($id): View
    {
        $dataLapangan = DataLapangan::with('enumerator')->find($id);


        return view('koordinator.data-lapangan.show', compact('dataLapangan'));
    }
}
