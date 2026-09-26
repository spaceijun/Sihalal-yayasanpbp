<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Services\Koordinator\KoordinatorDataLapanganService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DataLapanganController extends Controller
{
    public function __construct(private KoordinatorDataLapanganService $service) {}

    /**
     * Display a listing of the resource (scoped to this koordinator's enumerators).
     */
    public function index(): View
    {
        return view('koordinator.data-lapangan.index');
    }

    /**
     * Return DataTables JSON for data lapangan listing, scoped to this koordinator.
     */
    public function data(Request $request)
    {
        $koordinator = auth()->user()->koordinator;
        $enumeratorIds = Enumerator::where('koordinator_id', $koordinator->id)->pluck('id');

        $query = DataLapangan::whereIn('enumerator_id', $enumeratorIds)->with('enumerator');

        if ($request->filled('verifikasi')) {
            $query->where('verifikasi_koordinator', $request->verifikasi);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_reg_cell', fn ($d) => '<span class="adm-mono" style="font-size:12px;">'.e($d->no_registrasi).'</span>')
            ->addColumn('nama_pu', fn ($d) => e($d->nama_pu))
            ->addColumn('enumerator_nama', fn ($d) => $d->enumerator?->nama_lengkap ?? '—')
            ->addColumn('status_badge', fn ($d) => '<span class="adm-badge adm-badge-info">'.e($d->status).'</span>')
            ->addColumn('foto_badge', function ($d) {
                $lengkap = $d->foto_ktp && $d->foto_rumah && $d->foto_pendamping && $d->foto_produk;

                return $lengkap
                    ? '<span title="Foto lengkap" style="color:var(--adm-green);display:inline-flex;"><svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></span>'
                    : '<span title="Foto belum lengkap" style="color:var(--adm-amber);display:inline-flex;"><svg viewBox="0 0 24 24" style="width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>';
            })
            ->addColumn('verifikasi_badge', function ($d) {
                return match ($d->verifikasi_koordinator) {
                    'Terverifikasi' => '<span class="adm-badge adm-badge-success">Terverifikasi</span>',
                    'Perlu Koreksi' => '<span class="adm-badge adm-badge-danger">Perlu Koreksi</span>',
                    default => '<span class="adm-badge adm-badge-pending">Belum</span>',
                };
            })
            ->addColumn('tanggal_fmt', fn ($d) => $d->created_at?->format('d M Y') ?? '-')
            ->addColumn('aksi', function ($d) {
                $showUrl = route('koordinator.data-lapangan.show', $d->hashed_id);

                return '<div class="adm-actions">
                    <a href="'.$showUrl.'" class="adm-btn primary icon-only" title="Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <button type="button" class="adm-btn success icon-only" title="Verifikasi"
                        onclick="bukaModalVerifikasi(\''.$d->hashed_id.'\', \''.e(addslashes($d->no_registrasi)).'\', \''.e(addslashes($d->nama_pu)).'\')">
                        <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['no_reg_cell', 'status_badge', 'foto_badge', 'verifikasi_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Display the specified resource (read-only detail).
     */
    public function show(DataLapangan $dataLapangan): View
    {
        $koordinator = auth()->user()->koordinator;
        $dataLapangan->loadMissing('enumerator', 'verifiedByKoordinator');

        if ($dataLapangan->enumerator?->koordinator_id !== $koordinator->id) {
            abort(403, 'Data lapangan ini bukan di bawah koordinasi Anda.');
        }

        return view('koordinator.data-lapangan.show', compact('dataLapangan'));
    }

    /**
     * Verifikasi data lapangan (AJAX). Hanya mengubah kolom verifikasi_koordinator,
     * catatan_koordinator, verified_at_koordinator, verified_by_koordinator.
     */
    public function verifikasi(Request $request, DataLapangan $dataLapangan): JsonResponse
    {
        $request->validate([
            'verifikasi_koordinator' => 'required|in:Terverifikasi,Perlu Koreksi',
            'catatan_koordinator' => 'nullable|string|max:1000',
        ]);

        try {
            $koordinator = auth()->user()->koordinator;
            $this->service->verifikasi($dataLapangan, $koordinator, $request->only(['verifikasi_koordinator', 'catatan_koordinator']));

            return response()->json(['message' => 'Verifikasi berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
}
