<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class EnumeratorController extends Controller
{
    /**
     * Display a listing of the resource (scoped to this koordinator's own enumerators).
     */
    public function index(): View
    {
        return view('koordinator.enumerator.index');
    }

    /**
     * Return DataTables JSON for enumerator listing, scoped to this koordinator.
     */
    public function data(Request $request)
    {
        $koordinator = auth()->user()->koordinator;

        $query = Enumerator::where('koordinator_id', $koordinator->id)
            ->withCount([
                'dataLapangans',
                'dataLapangans as data_bulan_ini' => fn ($q) => $q
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_cell', function ($e) {
                $inisial = strtoupper(substr($e->nama_lengkap, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.$inisial.'</div>
                    <strong>'.e($e->nama_lengkap).'</strong>
                </div>';
            })
            ->addColumn('status_badge', fn ($e) => $e->status === 'Aktif'
                ? '<span class="adm-badge adm-badge-success">Aktif</span>'
                : '<span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>')
            ->addColumn('aksi', function ($e) {
                $showUrl = route('koordinator.enumerator.show', $e->hashed_id);

                return '<a href="'.$showUrl.'" class="adm-btn primary icon-only" title="Detail">
                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>';
            })
            ->rawColumns(['nama_cell', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Display the specified resource (read-only detail + KPI).
     */
    public function show(Enumerator $enumerator): View
    {
        $koordinator = auth()->user()->koordinator;

        if ($enumerator->koordinator_id !== $koordinator->id) {
            abort(403, 'Enumerator ini bukan di bawah koordinasi Anda.');
        }

        $dataBulanIni = $enumerator->dataLapangans()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalData = $enumerator->dataLapangans()->count();
        $totalTerbitSh = $enumerator->dataLapangans()->where('status', 'TERBIT SH')->count();
        $targetBulanan = 20;
        $progressPercent = $targetBulanan > 0 ? min(100, (int) round(($dataBulanIni / $targetBulanan) * 100)) : 0;

        return view('koordinator.enumerator.show', compact(
            'enumerator', 'dataBulanIni', 'totalData', 'totalTerbitSh', 'targetBulanan', 'progressPercent'
        ));
    }
}
