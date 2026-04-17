<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengumumanDataEntryController extends Controller
{
    public function index(Request $request): View
    {
        $pengumumen = Pengumuman::paginate();

        return view('data-entry.pengumuman.index', compact('pengumumen'))
            ->with('i', ($request->input('page', 1) - 1) * $pengumumen->perPage());
    }

    public function show($hashedId): View
    {
        $pengumuman = Pengumuman::findByHashedIdOrFail($hashedId);

        return view('data-entry.pengumuman.show', compact('pengumuman'));
    }
}
