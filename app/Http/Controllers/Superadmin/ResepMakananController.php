<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResepMakananRequest;
use App\Models\ResepMakanan;
use App\Services\Superadmin\ResepMakananService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ResepMakananController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private ResepMakananService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $routePrefix = $this->routePrefix();
        $total = ResepMakanan::count();

        return view('superadmin.resep-makanan.index', compact('routePrefix', 'total'));
    }

    /**
     * Return DataTables JSON for resep makanan listing.
     */
    public function data(Request $request)
    {
        $routePrefix = $this->routePrefix();
        $query = ResepMakanan::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_link', function ($r) use ($routePrefix) {
                $url = route($routePrefix.'.resep-makanans.show', $r->hashed_id);

                return '<a href="'.$url.'" style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">'.e($r->nama_produk).'</a>';
            })
            ->addColumn('kategori_badge', function ($r) {
                return '<span class="adm-badge adm-badge-info" style="text-transform:capitalize;">'.e($r->kategori).'</span>';
            })
            ->addColumn('aksi', function ($r) use ($routePrefix) {
                $showUrl = route($routePrefix.'.resep-makanans.show', $r->hashed_id);
                $editUrl = route($routePrefix.'.resep-makanans.edit', $r->hashed_id);

                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a class="adm-btn success icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteResepMakanan(\''.$r->hashed_id.'\', \''.e(addslashes($r->nama_produk)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['nama_link', 'kategori_badge', 'aksi'])
            ->make(true);
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
        try {
            $this->service->store($request->validated(), $request->file('foto'));

            return Redirect::route($this->routePrefix().'.resep-makanans.index')
                ->with('success', 'ResepMakanan berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan resep makanan: '.$e->getMessage())->withInput();
        }
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

        try {
            $this->service->update($resepMakanan, $request->validated(), $request->file('foto'));

            return Redirect::route($this->routePrefix().'.resep-makanans.index')
                ->with('success', 'ResepMakanan berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui resep makanan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId): JsonResponse
    {
        $resepMakanan = ResepMakanan::findByHashedIdOrFail($hashedId);

        try {
            $this->service->delete($resepMakanan);

            return response()->json(['message' => 'ResepMakanan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
