<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DataEntryRequest;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DataEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $dataEntries = DataEntry::paginate();

        return view('superadmin.data-entry.index', compact('dataEntries'))
            ->with('i', ($request->input('page', 1) - 1) * $dataEntries->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dataEntry = new DataEntry();

        return view('superadmin.data-entry.create', compact('dataEntry'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DataEntryRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
            'role' => 'data_entry',
        ]);

        $user->assignRole('data_entry');

        DataEntry::create([
            'user_id' => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'alamat' => $request->alamat,
            'status' => $request->status,
        ]);


        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $dataEntry = DataEntry::find($id);

        return view('superadmin.data-entry.show', compact('dataEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $dataEntry = DataEntry::find($id);

        return view('superadmin.data-entry.edit', compact('dataEntry'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DataEntryRequest $request, DataEntry $dataEntry): RedirectResponse
    {
        $dataEntry->update($request->validated());

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Di Update.');
    }

    public function destroy($id): RedirectResponse
    {
        DataEntry::find($id)->delete();

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Dihapus.');
    }
}
