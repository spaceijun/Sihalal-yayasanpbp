<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DataEntryProgressController extends Controller
{
    /**
     * Lihat progress milik data entry yang sedang login
     */
    public function index()
    {
        return view('data-entry.progress.index');
    }

    /**
     * Return Yajra DataTables JSON for the progress listing.
     * Filters: search (nama_pu, nik), action type, date range.
     */
    public function data(Request $request)
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        if (!$dataEntry) {
            return DataTables::of(DataEntryProgress::query()->whereRaw('1 = 0'))->make(true);
        }

        $query = DataEntryProgress::query()
            ->select('data_entry_progress.*', 'data_lapangans.nama_pu', 'data_lapangans.nik')
            ->join('data_lapangans', 'data_lapangans.id', '=', 'data_entry_progress.data_lapangan_id')
            ->where('data_entry_progress.data_entry_id', $dataEntry->id)
            ->where('data_entry_progress.action', 'created');

        // Action filter
        if ($request->filled('action_filter')) {
            $query->where('data_entry_progress.action', $request->action_filter);
        }

        // Date range filter
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('data_entry_progress.actioned_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('data_entry_progress.actioned_at', '<=', $request->tanggal_sampai);
        }

        $statusColors = [
            'PENDING' => 'bg-warning text-dark',
            'DITERIMA' => 'bg-success text-white',
            'REVISI'   => 'bg-info text-white',
            'DITOLAK'  => 'bg-danger text-white',
        ];

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('nama_pu', fn($q, $k) => $q->where('data_lapangans.nama_pu', 'like', "%{$k}%"))
            ->filterColumn('nik',     fn($q, $k) => $q->where('data_lapangans.nik',    'like', "%{$k}%"))
            ->addColumn(
                'waktu_aksi',
                fn($p) => $p->actioned_at
                    ? \Carbon\Carbon::parse($p->actioned_at)->isoFormat('D MMM YYYY, HH:mm')
                    : '-'
            )
            ->addColumn('action_badge', fn($p) => '<span class="badge bg-secondary">' . e($p->action) . '</span>')
            ->addColumn('status_badge', function ($p) use ($statusColors) {
                $cls = $statusColors[$p->status] ?? 'bg-secondary text-white';
                return '<span class="badge ' . $cls . '">' . e($p->status) . '</span>';
            })
            ->addColumn('aksi', function ($p) {
                $showUrl = route('data-entry.progress.show', $p->dataLapangan->hashed_id);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-primary">
                    <i class="las la-eye"></i> Detail
                </a>';
            })
            ->rawColumns(['action_badge', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Detail data lapangan dari halaman progress
     */
    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        $entryType = $dataEntry?->entry_type;

        // Ambil progress terbaru milik data lapangan ini untuk user yang login
        $latestProgress = $dataEntry
            ? DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('data_lapangan_id', $dataLapangan->id)
            ->where('action', 'created')
            ->latest('actioned_at')
            ->first()
            : null;

        return view('data-entry.progress.show', compact('dataLapangan', 'entryType', 'latestProgress'));
    }

    /**
     * Ringkasan progress per data entry (untuk admin)
     */
    public function summary()
    {
        $summary = DataEntry::withCount([
            'progress as total_created' => fn($q) => $q->where('action', 'created'),
            'progress as total_updated' => fn($q) => $q->where('action', 'updated'),
            'progress as total_deleted' => fn($q) => $q->where('action', 'deleted'),
        ])->get();

        return view('progress.summary', compact('summary'));
    }
}
