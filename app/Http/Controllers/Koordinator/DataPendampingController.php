<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataPendampingController extends Controller
{
    public function index()
    {
        $enumerators = Enumerator::where('koordinator_id', Auth::user()->koordinator->id)->latest()->paginate(10);
        return view('koordinator.data-pendamping.index', compact('enumerators'));
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
}
