<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource (all pengumuman, read-only).
     */
    public function index(): View
    {
        $pengumumen = Pengumuman::latest()->paginate(9);

        return view('koordinator.pengumuman.index', compact('pengumumen'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengumuman $pengumuman): View
    {
        return view('koordinator.pengumuman.show', compact('pengumuman'));
    }
}
