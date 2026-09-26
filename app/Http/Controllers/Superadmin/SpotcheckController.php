<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpotcheckRequest;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Spotcheck;
use App\Services\Superadmin\SpotcheckService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SpotcheckController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private SpotcheckService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $routePrefix = $this->routePrefix();

        return view('superadmin.spotcheck.index', compact('routePrefix'));
    }

    /**
     * Return DataTables JSON for spotcheck listing.
     */
    public function data(Request $request)
    {
        $query = Spotcheck::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_cell', fn ($s) => '<span class="adm-mono" style="font-size:12.5px;">'.e($s->tanggal_spotcheck).'</span>')
            ->addColumn('foto_pu_cell', function ($s) {
                if (! $s->foto_pu) {
                    return '<span style="color:var(--adm-text-faint);">—</span>';
                }

                return '<span style="font-size:12px;color:var(--adm-text-muted);max-width:120px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:middle;">'.e($s->foto_pu).'</span>';
            })
            ->addColumn('hasil_cell', function ($s) {
                if (! $s->hasil_spotcheck) {
                    return '<span style="color:var(--adm-text-faint);">—</span>';
                }

                return '<span style="font-size:12.5px;max-width:180px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:middle;" title="'.e($s->hasil_spotcheck).'">'.e($s->hasil_spotcheck).'</span>';
            })
            ->addColumn('aksi', function ($s) {
                $prefix = $this->routePrefix();
                $showUrl = route($prefix.'.spotchecks.show', $s->hashed_id);
                $editUrl = route($prefix.'.spotchecks.edit', $s->hashed_id);

                return '<div class="adm-actions" style="justify-content:center;gap:4px;">
                    <a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Lihat">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a class="adm-btn warning icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteSpotcheck(\''.$s->hashed_id.'\', \''.e(addslashes($s->nama_spotcheck)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['tanggal_cell', 'foto_pu_cell', 'hasil_cell', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * Catatan: rute ini juga dipakai oleh formulir publik spotcheck
     * (lihat routes/web.php: `spotcheck.formulir`), sehingga sengaja
     * tidak diubah agar tidak mengubah alur publik yang sudah ada.
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
        try {
            $this->service->store($request->validated());

            return Redirect::route('spotcheck.formulir')
                ->with('success', 'Data Spotcheck anda berhasil dikirim.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan spotcheck: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $spotcheck = Spotcheck::findByHashedIdOrFail($hashedId);
        $routePrefix = $this->routePrefix();

        return view('superadmin.spotcheck.show', compact('spotcheck', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $spotcheck = Spotcheck::findByHashedIdOrFail($hashedId);
        $routePrefix = $this->routePrefix();

        return view('superadmin.spotcheck.edit', compact('spotcheck', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpotcheckRequest $request, Spotcheck $spotcheck): RedirectResponse
    {
        try {
            $this->service->update($spotcheck, $request->validated());

            return Redirect::route($this->routePrefix().'.spotchecks.index')
                ->with('success', 'Spotcheck updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui spotcheck: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId): JsonResponse
    {
        $spotcheck = Spotcheck::findByHashedIdOrFail($hashedId);

        try {
            $this->service->delete($spotcheck);

            return response()->json(['message' => 'Spotcheck berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
