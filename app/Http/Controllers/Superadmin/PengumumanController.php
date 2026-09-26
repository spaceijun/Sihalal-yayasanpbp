<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengumumanRequest;
use App\Models\Pengumuman;
use App\Services\Superadmin\PengumumanService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PengumumanController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private PengumumanService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->data($request);
        }

        $routePrefix = $this->routePrefix();
        $total = Pengumuman::count();

        return view('superadmin.pengumuman.index', compact('routePrefix', 'total'));
    }

    /**
     * Return DataTables JSON for pengumuman listing.
     */
    public function data(Request $request)
    {
        $routePrefix = $this->routePrefix();
        $query = Pengumuman::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nomor_badge', function ($p) {
                return '<span class="adm-mono" style="font-size:11.5px;font-weight:600;color:var(--adm-blue);background:var(--adm-blue-lt);padding:3px 8px;border-radius:6px;">'
                    .e($p->nomor).'</span>';
            })
            ->addColumn('judul_link', function ($p) use ($routePrefix) {
                $url = route($routePrefix.'.pengumumen.show', $p->hashed_id);

                return '<a href="'.$url.'" style="font-weight:600;font-size:13px;color:var(--adm-text-dark);text-decoration:none;">'.e($p->judul).'</a>';
            })
            ->addColumn('jenis_badge', function ($p) {
                return match ($p->jenis) {
                    'OSS' => '<span class="adm-badge adm-badge-oss">OSS</span>',
                    'SIHALAL' => '<span class="adm-badge adm-badge-sihalal">SIHALAL</span>',
                    default => '<span class="adm-badge adm-badge-info">'.e($p->jenis).'</span>',
                };
            })
            ->addColumn('aksi', function ($p) use ($routePrefix) {
                $showUrl = route($routePrefix.'.pengumumen.show', $p->hashed_id);
                $editUrl = route($routePrefix.'.pengumumen.edit', $p->hashed_id);

                return '<div class="adm-actions">
                    <a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a class="adm-btn success icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeletePengumuman(\''.$p->hashed_id.'\', \''.e(addslashes($p->judul)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['nomor_badge', 'judul_link', 'jenis_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $nextNomor = $this->service->generateNomor();
        $pengumuman = new Pengumuman;

        $routePrefix = $this->routePrefix();

        return view('superadmin.pengumuman.create', compact('pengumuman', 'nextNomor', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengumumanRequest $request): RedirectResponse
    {
        try {
            $this->service->store($request->validated(), $request->file('foto'));

            return Redirect::route($this->routePrefix().'.pengumumen.index')
                ->with('success', 'Pengumuman berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan pengumuman: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $pengumuman = Pengumuman::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.pengumuman.show', compact('pengumuman', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $pengumuman = Pengumuman::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.pengumuman.edit', compact('pengumuman', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PengumumanRequest $request, Pengumuman $pengumuman): RedirectResponse
    {
        try {
            $this->service->update($pengumuman, $request->validated(), $request->file('foto'));

            return Redirect::route($this->routePrefix().'.pengumumen.index')
                ->with('success', 'Pengumuman berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui pengumuman: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($hashedId): JsonResponse
    {
        $pengumuman = Pengumuman::findByHashedIdOrFail($hashedId);

        try {
            $this->service->delete($pengumuman);

            return response()->json(['message' => 'Pengumuman berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
