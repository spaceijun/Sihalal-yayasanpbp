<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataEntryRequest;
use App\Models\DataEntry;
use App\Models\Superadmin\Koordinator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DataEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('superadmin.data-entry.index');
    }

    /**
     * Return DataTables JSON for data entry listing.
     */
    public function data(Request $request)
    {
        $query = DataEntry::with('koordinators', 'bank');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function ($de) {
                $inisial = strtoupper(substr($de->nama_lengkap, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.$inisial.'</div>
                    <div><strong>'.e($de->nama_lengkap).'</strong></div>
                </div>';
            })
            ->addColumn('status_badge', function ($de) {
                return $de->status === 'Aktif'
                    ? '<span class="adm-badge adm-badge-success">Aktif</span>'
                    : '<span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>';
            })
            ->addColumn('entry_type_badge', function ($de) {
                if ($de->entry_type === 'OSS') {
                    return '<span class="adm-badge adm-badge-oss">OSS</span>';
                }
                if ($de->entry_type === 'SIHALAL') {
                    return '<span class="adm-badge adm-badge-sihalal">SIHALAL</span>';
                }

                return '<span class="adm-badge adm-badge-info">'.e($de->entry_type).'</span>';
            })
            ->addColumn('rekening', function ($de) {
                if ($de->bank && $de->no_rekening && $de->nama_rekening) {
                    return '<span style="font-size:12px;color:var(--adm-text-muted);">'.e($de->bank->name).', '.e($de->no_rekening).' an. '.e($de->nama_rekening).'</span>';
                }

                return '<span style="color:var(--adm-text-faint);">—</span>';
            })
            ->addColumn('aksi', function ($de) {
                $editUrl = route('superadmin.data-entries.edit', $de->hashed_id);
                $deleteUrl = route('superadmin.data-entries.destroy', $de->hashed_id);

                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin hapus data entry ini?\')">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['nama_cell', 'status_badge', 'entry_type_badge', 'rekening', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dataEntry = new DataEntry;
        $koordinators = Koordinator::all();

        return view('superadmin.data-entry.create', compact('dataEntry', 'koordinators'));
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

        $dataEntry = DataEntry::create([
            'user_id' => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'alamat' => $request->alamat,
            'status' => $request->status,
            'entry_type' => $request->entry_type,
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
    public function show($hashedId): View
    {
        $dataEntry = DataEntry::findByHashedIdOrFail($hashedId);
        $dataEntry->load('koordinators');

        return view('superadmin.data-entry.show', compact('dataEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $dataEntry = DataEntry::findByHashedIdOrFail($hashedId);
        $dataEntry->load('koordinators');
        $koordinators = Koordinator::all();
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
            'email' => $request->email,
            'telephone' => $request->telephone,
            'alamat' => $request->alamat,
            'status' => $request->status,
            'entry_type' => $request->entry_type,
        ]);

        // Sync koordinator (otomatis handle tambah/hapus)
        $dataEntry->koordinators()->sync($request->koordinator_ids ?? []);

        // Update password jika diisi
        if ($request->filled('password')) {
            $dataEntry->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Di Update.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId): RedirectResponse
    {
        $dataEntry = DataEntry::findByHashedIdOrFail($hashedId);

        // Detach koordinator dulu sebelum delete
        $dataEntry->koordinators()->detach();
        $dataEntry->delete();

        return Redirect::route('superadmin.data-entries.index')
            ->with('success', 'Data Entry Berhasil Dihapus.');
    }
}
