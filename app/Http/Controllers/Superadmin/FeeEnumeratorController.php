<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeeEnumeratorRequest;
use App\Models\Superadmin\FeeEnumerator;
use App\Services\Superadmin\FeeEnumeratorService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FeeEnumeratorController extends Controller
{
    use HasRoutePrefix;

    public function __construct(private FeeEnumeratorService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $routePrefix = $this->routePrefix();

        return view('superadmin.fee-enumerator.index', compact('routePrefix'));
    }

    /**
     * Return DataTables JSON for fee enumerator listing.
     */
    public function data(Request $request)
    {
        $query = FeeEnumerator::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('skala_badge', function ($f) {
                return $f->skala === 'Global'
                    ? '<span class="adm-badge adm-badge-info">Global</span>'
                    : '<span class="adm-badge adm-badge-indigo">Provinsi &middot; '.e($f->provinsi).'</span>';
            })
            ->addColumn('tipe_fee_badge', function ($f) {
                return $f->tipe_fee === 'Bulan'
                    ? '<span class="adm-badge adm-badge-cyan">Bulan</span>'
                    : '<span class="adm-badge adm-badge-pending">Target &middot; '.$f->target_data.' data</span>';
            })
            ->addColumn('nominal_fmt', fn ($f) => 'Rp '.number_format($f->nominal_fee, 0, ',', '.'))
            ->addColumn('keterangan_fmt', fn ($f) => $f->keterangan ? e($f->keterangan) : '<span style="color:var(--adm-text-faint);">&mdash;</span>')
            ->addColumn('status_badge', function ($f) {
                return $f->is_aktif
                    ? '<span class="adm-badge adm-badge-success">Aktif</span>'
                    : '<span class="adm-badge adm-badge-nonaktif">Nonaktif</span>';
            })
            ->addColumn('aksi', function ($f) {
                $editUrl = route($this->routePrefix().'.fee-enumerator.edit', $f->hashed_id);
                $toggleIcon = $f->is_aktif
                    ? '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
                    : '<svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>';
                $toggleClass = $f->is_aktif ? 'warning' : 'success';
                $toggleTitle = $f->is_aktif ? 'Nonaktifkan' : 'Aktifkan';

                return '<div class="adm-actions">
                    <button type="button" class="adm-btn '.$toggleClass.' icon-only" title="'.$toggleTitle.'"
                        onclick="toggleAktifFee(\''.$f->hashed_id.'\')">
                        '.$toggleIcon.'
                    </button>
                    <a class="adm-btn warning icon-only" href="'.$editUrl.'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only" title="Hapus"
                        onclick="confirmDeleteFee(\''.$f->hashed_id.'\', \''.e(addslashes($f->skala.' - '.$f->tipe_fee)).'\')">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['skala_badge', 'tipe_fee_badge', 'keterangan_fmt', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $feeEnumerator = new FeeEnumerator;
        $routePrefix = $this->routePrefix();

        return view('superadmin.fee-enumerator.create', compact('feeEnumerator', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FeeEnumeratorRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['is_aktif'] = $request->boolean('is_aktif');

            $this->service->store($data);

            return Redirect::route($this->routePrefix().'.fee-enumerator.index')
                ->with('success', 'Fee enumerator berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan fee enumerator: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeeEnumerator $feeEnumerator): View
    {
        $routePrefix = $this->routePrefix();

        return view('superadmin.fee-enumerator.edit', compact('feeEnumerator', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FeeEnumeratorRequest $request, FeeEnumerator $feeEnumerator): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['is_aktif'] = $request->boolean('is_aktif');

            $this->service->update($feeEnumerator, $data);

            return Redirect::route($this->routePrefix().'.fee-enumerator.index')
                ->with('success', 'Fee enumerator berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui fee enumerator: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeEnumerator $feeEnumerator): JsonResponse
    {
        try {
            $this->service->delete($feeEnumerator);

            return response()->json(['message' => 'Fee enumerator berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Toggle the active state of the specified resource.
     */
    public function toggleAktif(FeeEnumerator $feeEnumerator): JsonResponse
    {
        try {
            $updated = $this->service->toggleAktif($feeEnumerator);

            return response()->json([
                'message' => $updated->is_aktif ? 'Fee enumerator diaktifkan' : 'Fee enumerator dinonaktifkan',
                'is_aktif' => $updated->is_aktif,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
