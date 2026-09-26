<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Http\Requests\KoordinatorRequest;
use App\Models\Superadmin\Koordinator;
use App\Services\Superadmin\KoordinatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KoordinatorController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private KoordinatorService $service) {}

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
        $query = Koordinator::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function ($k) {
                if ($k->foto_formal) {
                    $avatar = '<img src="'.\Illuminate\Support\Facades\Storage::url($k->foto_formal).'" class="adm-avatar" style="object-fit:cover;" alt="">';
                } else {
                    $inisial = strtoupper(substr($k->nama_lengkap, 0, 2));
                    $avatar = '<div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.$inisial.'</div>';
                }

                return '<div class="adm-name-cell">
                    '.$avatar.'
                    <div>
                        <strong>'.e($k->nama_lengkap).'</strong>
                        <small style="color:var(--adm-text-muted);display:block;margin-top:2px;">ID: KO-'.str_pad($k->id, 4, '0', STR_PAD_LEFT).'</small>
                    </div>
                </div>';
            })
            ->addColumn('wilayah_badge', function ($k) {
                if (! $k->tipe_wilayah_kerja) {
                    return '<span style="color:var(--adm-text-faint);">&mdash;</span>';
                }

                $label = $k->tipe_wilayah_kerja === 'Kabupaten'
                    ? e($k->kabupaten_kerja).', '.e($k->provinsi_kerja)
                    : e($k->provinsi_kerja);

                return '<span class="adm-badge adm-badge-indigo">'.$k->tipe_wilayah_kerja.'</span> '.$label;
            })
            ->addColumn('status_badge', function ($k) {
                return match ($k->status) {
                    'Aktif' => '<span class="adm-badge adm-badge-success">Aktif</span>',
                    'Blacklist' => '<span class="adm-badge adm-badge-danger">Blacklist</span>',
                    default => '<span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>',
                };
            })
            ->addColumn('aksi', function ($k) {
                $editUrl = route($this->routePrefix() . '.koordinators.edit', $k->hashed_id);

                return '<div class="adm-actions">
                    <a class="adm-btn warning icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteKoordinator(\''.$k->hashed_id.'\', \''.e(addslashes($k->nama_lengkap)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['nama_cell', 'wilayah_badge', 'status_badge', 'aksi'])
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
        try {
            $this->service->store($request->validated(), $request->file('foto_ktp'), $request->file('foto_formal'));

            return Redirect::route($this->routePrefix() . '.koordinators.index')
                ->with('success', 'Koordinator berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan koordinator: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Koordinator $koordinator): View
    {
        $routePrefix = $this->routePrefix();

        $totalEnumerator = $koordinator->enumerators()->count();
        $totalDataLapangan = $koordinator->dataLapangans()->count();
        $totalTerbitSh = $koordinator->dataLapangans()->where('data_lapangans.status', 'TERBIT SH')->count();

        return view('superadmin.koordinator.show', compact(
            'koordinator', 'routePrefix', 'totalEnumerator', 'totalDataLapangan', 'totalTerbitSh'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Koordinator $koordinator): View
    {
        $routePrefix = $this->routePrefix();

        return view('superadmin.koordinator.edit', compact('koordinator', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KoordinatorRequest $request, Koordinator $koordinator): RedirectResponse
    {
        try {
            $this->service->update($koordinator, $request->validated(), $request->file('foto_ktp'), $request->file('foto_formal'));

            return Redirect::route($this->routePrefix() . '.koordinators.index')
                ->with('success', 'Koordinator berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui koordinator: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Koordinator $koordinator): JsonResponse
    {
        try {
            $this->service->delete($koordinator);

            return response()->json(['message' => 'Koordinator berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
