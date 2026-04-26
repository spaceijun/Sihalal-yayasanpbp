<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PengumumanRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pengumumen = Pengumuman::paginate();

        return view('superadmin.pengumuman.index', compact('pengumumen'))
            ->with('i', ($request->input('page', 1) - 1) * $pengumumen->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $nextNomor = $this->generateNomor();
        $pengumuman = new Pengumuman();

        return view('superadmin.pengumuman.create', compact('pengumuman', 'nextNomor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengumumanRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Generate nomor otomatis
        $validatedData['nomor'] = $this->generateNomor();

        if (request()->hasFile('foto')) {
            $foto = request()->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('pengumuman', $fotoName, 'public');
            $validatedData['foto'] = 'pengumuman/' . $fotoName;
        }

        Pengumuman::create($validatedData);

        return Redirect::route('superadmin.pengumumen.index')
            ->with('success', 'Pengumuman created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $pengumuman = Pengumuman::findByHashedIdOrFail($hashedId);

        return view('superadmin.pengumuman.show', compact('pengumuman'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $pengumuman = Pengumuman::findByHashedIdOrFail($hashedId);

        return view('superadmin.pengumuman.edit', compact('pengumuman'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengumumanRequest $request, Pengumuman $pengumuman): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($pengumuman->foto) {
                Storage::disk('public')->delete($pengumuman->foto);
            }

            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('pengumuman', $fotoName, 'public');
            $validatedData['foto'] = 'pengumuman/' . $fotoName;
        } else {
            // Pertahankan foto lama jika tidak ada upload baru
            unset($validatedData['foto']);
        }

        $pengumuman->update($validatedData);

        return Redirect::route('superadmin.pengumumen.index')
            ->with('success', 'Pengumuman updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        Pengumuman::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.pengumumen.index')
            ->with('success', 'Pengumuman deleted successfully');
    }

    /**
     * PRIVATE FUNCTION
     */

    private function generateNomor(): string
    {
        $bulan = now()->format('m');
        $tahun = now()->format('Y');
        $prefix = "YPBP-KH/{$bulan}/{$tahun}/";

        // Selalu ambil nomor urut dari total seluruh data
        $count = Pengumuman::count();
        $nextNumber = $count + 1;

        // Cek jika nomor sudah dipakai (hindari duplikat)
        $nomor = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        while (Pengumuman::where('nomor', $nomor)->exists()) {
            $nextNumber++;
            $nomor = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $nomor;
    }
}

