<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\ResepMakanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ResepMakananRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ResepMakananController extends Controller
{
    use HasRoutePrefix;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $resepMakanans = ResepMakanan::paginate();

        return view('superadmin.resep-makanan.index', compact('resepMakanans'))
            ->with('i', ($request->input('page', 1) - 1) * $resepMakanans->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $resepMakanan = new ResepMakanan();

        $routePrefix = $this->routePrefix();

        return view('superadmin.resep-makanan.create', compact('resepMakanan', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ResepMakananRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if (request()->hasFile('foto')) {
            $foto = request()->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('resep-makanan', $fotoName, 'public');

            $validatedData['foto'] = 'resep-makanan/' . $fotoName;
        }

        ResepMakanan::create($validatedData);

        return Redirect::route($this->routePrefix() . '.resep-makanans.index')
            ->with('success', 'ResepMakanan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $resepMakanan = ResepMakanan::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.resep-makanan.show', compact('resepMakanan', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $resepMakanan = ResepMakanan::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.resep-makanan.edit', compact('resepMakanan', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ResepMakananRequest $request, $hashedId): RedirectResponse
    {
        $resepMakanan = ResepMakanan::findByHashedIdOrFail($hashedId);
        $validatedData = $request->validated();

        if (request()->hasFile('foto')) {
            $foto = request()->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('resep-makanan', $fotoName, 'public');

            $validatedData['foto'] = 'resep-makanan/' . $fotoName;
        }

        $resepMakanan->update($validatedData);

        return Redirect::route($this->routePrefix() . '.resep-makanans.index')
            ->with('success', 'ResepMakanan updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        ResepMakanan::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route($this->routePrefix() . '.resep-makanans.index')
            ->with('success', 'ResepMakanan deleted successfully');
    }
}
