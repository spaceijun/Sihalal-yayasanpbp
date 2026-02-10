<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Spotcheck;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\SpotcheckRequest;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SpotcheckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $spotchecks = Spotcheck::paginate();

        return view('superadmin.spotcheck.index', compact('spotchecks'))
            ->with('i', ($request->input('page', 1) - 1) * $spotchecks->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $spotcheck = new Spotcheck();
        $dataLapangans = DataLapangan::all();
        $enumerators = Enumerator::all();

        return view('publik.form-spotcheck', compact('spotcheck', 'dataLapangans', 'enumerators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpotcheckRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('foto_pu')) {
            $file = $request->file('foto_pu');
            $fileName = time() . '_spotcheck_' . $file->getClientOriginalName();
            $file->storeAs('spotcheck', $fileName, 'public');
            $validatedData['foto_pu'] = 'spotcheck/' . $fileName;
        }
        Spotcheck::create($validatedData);

        return Redirect::route('spotcheck.formulir')
            ->with('success', 'Data Spotcheck anda berhasil dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $spotcheck = Spotcheck::find($id);

        return view('superadmin.spotcheck.show', compact('spotcheck'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $spotcheck = Spotcheck::find($id);

        return view('superadmin.spotcheck.edit', compact('spotcheck'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpotcheckRequest $request, Spotcheck $spotcheck): RedirectResponse
    {
        $spotcheck->update($request->validated());

        return Redirect::route('superadmin.spotchecks.index')
            ->with('success', 'Spotcheck updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Spotcheck::find($id)->delete();

        return Redirect::route('superadmin.spotchecks.index')
            ->with('success', 'Spotcheck deleted successfully');
    }
}
