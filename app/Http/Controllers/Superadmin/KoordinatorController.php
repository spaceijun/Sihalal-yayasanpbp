<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Http\Requests\KoordinatorRequest;
use App\Models\Superadmin\Koordinator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KoordinatorController extends Controller
{
    use HasRoutePrefix;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $routePrefix = $this->routePrefix();
        return view('superadmin.koordinator.index', compact('routePrefix'));
    }

    /**
     * Return DataTables JSON for koordinator listing.
     */
    public function data(Request $request)
    {
        $query = Koordinator::withCount([
            'dataLapangans',
            'dataLapangans as terbit_sh_count' => fn ($q) => $q->where('data_lapangans.status', 'TERBIT SH'),
            'dataLapangans as dibayar_count' => fn ($q) => $q->where('data_lapangans.status_pembayaran', 'DIBAYAR'),
        ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function ($k) {
                $inisial = strtoupper(substr($k->nama_lengkap, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.$inisial.'</div>
                    <div>
                        <strong>'.e($k->nama_lengkap).'</strong>
                        <small style="color:var(--adm-text-muted);display:block;margin-top:2px;">ID: KO-'.str_pad($k->id, 4, '0', STR_PAD_LEFT).'</small>
                    </div>
                </div>';
            })
            ->addColumn('fee_fmt', fn ($k) => 'Rp '.number_format($k->fee_enum, 0, ',', '.'))
            ->addColumn('status_badge', function ($k) {
                return $k->status === 'Aktif'
                    ? '<span class="adm-badge adm-badge-success">Aktif</span>'
                    : '<span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>';
            })
            ->addColumn('aksi', function ($k) {
                $editUrl = route($this->routePrefix() . '.koordinators.edit', $k->hashed_id);
                $deleteUrl = route($this->routePrefix() . '.koordinators.destroy', $k->hashed_id);

                return '<div class="adm-actions">
                    <a class="adm-btn warning icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <form action="'.$deleteUrl.'" method="POST" class="d-inline form-delete">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['nama_cell', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $koordinator = new Koordinator;

        $routePrefix = $this->routePrefix();

        return view('superadmin.koordinator.create', compact('koordinator', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KoordinatorRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
            'role' => 'koordinator',
        ]);

        $user->assignRole('koordinator');

        Koordinator::create([
            'user_id' => $user->id,
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'fee_enum' => $request->fee_enum,
            'alamat' => $request->alamat,
            'status' => $request->status,
        ]);

        return Redirect::route($this->routePrefix() . '.koordinators.index')
            ->with('success', 'Koordinator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $koordinator = Koordinator::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.koordinator.show', compact('koordinator', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $koordinator = Koordinator::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.koordinator.edit', compact('koordinator', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KoordinatorRequest $request, Koordinator $koordinator): RedirectResponse
    {
        $koordinator->update($request->validated());

        return Redirect::route($this->routePrefix() . '.koordinators.index')
            ->with('success', 'Koordinator updated successfully');
    }

    public function destroy($hashedId): RedirectResponse
    {
        Koordinator::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route($this->routePrefix() . '.koordinators.index')
            ->with('success', 'Koordinator deleted successfully');
    }
}
