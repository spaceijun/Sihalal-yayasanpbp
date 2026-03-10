<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Services\DataEntryPenagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataEntryProgressController extends Controller
{
    protected $penagihanService;

    public function __construct(DataEntryPenagihanService $penagihanService)
    {
        $this->penagihanService = $penagihanService;
    }

    /**
     * Hitung jumlah progress per status — dipakai di index dan show.
     */
    private function getStatusCounts(): array
    {
        return [
            'countPending'  => DataEntryProgress::where('action', 'created')->where('status', 'PENDING')->count(),
            'countRevisi'   => DataEntryProgress::where('action', 'created')->where('status', 'REVISI')->count(),
            'countDiterima' => DataEntryProgress::where('action', 'created')->where('status', 'DITERIMA')->count(),
            'countDitolak'  => DataEntryProgress::where('action', 'created')->where('status', 'DITOLAK')->count(),
        ];
    }

    /**
     * Tampilkan daftar semua progress data entry untuk direview superadmin.
     */
    public function index(Request $request): View
    {
        $query = DataEntryProgress::with([
            'dataLapangan',
            'dataEntry.user',
        ])
            ->where('action', 'created');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: tampilkan PENDING dan REVISI (yang butuh perhatian)
            $query->whereIn('status', ['PENDING', 'REVISI']);
        }

        // Filter entry_type (OSS / SIHALAL)
        if ($request->filled('entry_type')) {
            $query->whereHas('dataEntry', function ($q) use ($request) {
                $q->where('entry_type', $request->entry_type);
            });
        }

        // Filter search nama PU / nama data entry
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dataLapangan', function ($q2) use ($search) {
                    $q2->where('nama_pu', 'like', "%{$search}%");
                })->orWhereHas('dataEntry.user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        $progresses = $query->latest('actioned_at')->paginate(20)->withQueryString();

        return view('superadmin.data-entry.progress.index', array_merge(
            compact('progresses'),
            $this->getStatusCounts()
        ));
    }

    /**
     * Detail satu progress — superadmin bisa melihat data lapangan & file terkait.
     */
    public function show(DataEntryProgress $progress): View
    {
        $progress->load(['dataLapangan.enumerator', 'dataEntry.user']);

        // Ambil semua progress milik data_entry yang sama, untuk tabel di show view
        $progresses = DataEntryProgress::with(['dataLapangan', 'dataEntry.user'])
            ->where('action', 'created')
            ->where('data_entry_id', $progress->data_entry_id)
            ->latest('actioned_at')
            ->paginate(20);

        $countPending  = DataEntryProgress::where('action', 'created')->where('status', 'PENDING')->count();
        $countRevisi   = DataEntryProgress::where('action', 'created')->where('status', 'REVISI')->count();
        $countDiterima = DataEntryProgress::where('action', 'created')->where('status', 'DITERIMA')->count();
        $countDitolak  = DataEntryProgress::where('action', 'created')->where('status', 'DITOLAK')->count();

        return view('superadmin.data-entry.progress.show', compact(
            'progress',
            'progresses',       
            'countPending',
            'countRevisi',
            'countDiterima',
            'countDitolak'
        ));
    }
    /**
     * Terima progress — status jadi DITERIMA, lalu cek penagihan.
     */
    public function terima(DataEntryProgress $progress): RedirectResponse
    {
        if ($progress->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya progress berstatus PENDING yang dapat diterima.');
        }

        $progress->update([
            'status'      => 'DITERIMA',
            'actioned_at' => now(),
        ]);

        // Cek dan buat tagihan jika sudah memenuhi 15 data DITERIMA
        $dataEntry = $progress->dataEntry;
        if ($dataEntry) {
            $this->penagihanService->cekDanBuatTagihan($dataEntry);
        }

        return redirect()->back()->with('success', 'Progress berhasil diterima.');
    }

    /**
     * Revisi progress — superadmin memberi catatan, status jadi REVISI.
     */
    public function revisi(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        if ($progress->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya progress berstatus PENDING yang dapat direvisi.');
        }

        $progress->update([
            'status'             => 'REVISI',
            'keterangan_revisi'  => $request->keterangan_revisi,
            'actioned_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Progress ditandai perlu revisi. Data entry akan diberitahu.');
    }

    /**
     * Tolak progress — status jadi DITOLAK dengan keterangan.
     */
    public function tolak(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        if ($progress->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya progress berstatus PENDING yang dapat ditolak.');
        }

        $progress->update([
            'status'             => 'DITOLAK',
            'keterangan_revisi'  => $request->keterangan_revisi,
            'actioned_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Progress berhasil ditolak.');
    }

    /**
     * Bulk terima — terima banyak progress sekaligus.
     */
    public function bulkTerima(Request $request): RedirectResponse
    {
        $request->validate([
            'progress_ids'   => 'required|array|min:1',
            'progress_ids.*' => 'exists:data_entry_progress,id',
        ]);

        $progresses = DataEntryProgress::whereIn('id', $request->progress_ids)
            ->where('status', 'PENDING')
            ->get();

        if ($progresses->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada progress PENDING yang dipilih.');
        }

        $dataEntryIds = [];

        foreach ($progresses as $progress) {
            $progress->update([
                'status'      => 'DITERIMA',
                'actioned_at' => now(),
            ]);
            if ($progress->data_entry_id) {
                $dataEntryIds[] = $progress->data_entry_id;
            }
        }

        // Cek penagihan untuk setiap data entry yang terlibat
        foreach (array_unique($dataEntryIds) as $dataEntryId) {
            $dataEntry = DataEntry::find($dataEntryId);
            if ($dataEntry) {
                $this->penagihanService->cekDanBuatTagihan($dataEntry);
            }
        }

        return redirect()->back()->with('success', $progresses->count() . ' progress berhasil diterima.');
    }
}
