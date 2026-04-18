<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\Superadmin\Koordinator;
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
        $dataEntries = DataEntry::with('koordinators', 'bank')->paginate();
        return view('superadmin.data-entry.index', compact('dataEntries'))
            ->with('i', ($request->input('page', 1) - 1) * $dataEntries->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dataEntry    = new DataEntry();
        $koordinators = Koordinator::all();
        return view('superadmin.data-entry.create', compact('dataEntry', 'koordinators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DataEntryRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'      => $request->nama_lengkap,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'password'  => bcrypt($request->password),
            'role'      => 'data_entry',
        ]);
        $user->assignRole('data_entry');

        $dataEntry = DataEntry::create([
            'user_id'      => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'telephone'    => $request->telephone,
            'alamat'       => $request->alamat,
            'status'       => $request->status,
            'entry_type'   => $request->entry_type,
        ]);

        // Attach koordinator
        if ($request->filled('koordinator_ids')) {
            $dataEntry->koordinators()->sync($request->koordinator_ids);
        }

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $dataEntry = DataEntry::with('koordinators')->findOrFail($id);
        return view('superadmin.data-entry.show', compact('dataEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $dataEntry              = DataEntry::with('koordinators')->findOrFail($id);
        $koordinators           = Koordinator::all();
        $selectedKoordinatorIds = $dataEntry->koordinators->pluck('id')->toArray();

        return view('superadmin.data-entry.edit', compact('dataEntry', 'koordinators', 'selectedKoordinatorIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DataEntryRequest $request, DataEntry $dataEntry): RedirectResponse
    {
        $dataEntry->update([
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'telephone'    => $request->telephone,
            'alamat'       => $request->alamat,
            'status'       => $request->status,
            'entry_type'   => $request->entry_type,
        ]);

        // Sync koordinator (otomatis handle tambah/hapus)
        $dataEntry->koordinators()->sync($request->koordinator_ids ?? []);

        // Update password jika diisi
        if ($request->filled('password')) {
            $dataEntry->user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Di Update.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $dataEntry = DataEntry::findOrFail($id);

        // Detach koordinator dulu sebelum delete
        $dataEntry->koordinators()->detach();
        $dataEntry->delete();

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Dihapus.');
    }
}
