<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\Verifikator;
use App\Services\DataEntryPenagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DataEntryProgressController extends Controller
{
    protected $penagihanService;

    public function __construct(DataEntryPenagihanService $penagihanService)
    {
        $this->penagihanService = $penagihanService;
    }

    private function getStatusCounts(): array
    {
        return [
            'countPending'  => DataEntryProgress::where('action', 'created')->where('status', 'PENDING')->count(),
            'countRevisi'   => DataEntryProgress::where('action', 'created')->where('status', 'REVISI')->count(),
            'countDiterima' => DataEntryProgress::where('action', 'created')->where('status', 'DITERIMA')->count(),
            'countDitolak'  => DataEntryProgress::where('action', 'created')->where('status', 'DITOLAK')->count(),
        ];
    }

    public function index(Request $request): View
    {
        $query = DataEntryProgress::with([
            'dataLapangan',
            'dataEntry.user',
            'verifikator',
        ])->where('action', 'created');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['PENDING', 'REVISI']);
        }

        if ($request->filled('entry_type')) {
            $query->whereHas(
                'dataEntry',
                fn($q) =>
                $q->where('entry_type', $request->entry_type)
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'dataLapangan',
                    fn($q2) =>
                    $q2->where('nama_pu', 'like', "%{$search}%")
                )->orWhereHas(
                    'dataEntry.user',
                    fn($q2) =>
                    $q2->where('name', 'like', "%{$search}%")
                );
            });
        }

        $progresses   = $query->latest('actioned_at')->paginate(20)->withQueryString();
        $verifikators = Verifikator::orderBy('nama_lengkap')->get();

        return view('superadmin.data-entry.progress.index', array_merge(
            compact('progresses', 'verifikators'),
            $this->getStatusCounts()
        ));
    }

    public function show(DataEntryProgress $progress): View
    {
        $progress->load(['dataLapangan.enumerator', 'dataEntry.user', 'verifikator']);

        $progresses = DataEntryProgress::with(['dataLapangan', 'dataEntry.user', 'verifikator'])
            ->where('action', 'created')
            ->where('data_entry_id', $progress->data_entry_id)
            ->latest('actioned_at')
            ->paginate(20);

        $verifikators = Verifikator::orderBy('nama_lengkap')->get();

        return view('superadmin.data-entry.progress.show', array_merge(
            compact('progress', 'progresses', 'verifikators'),
            $this->getStatusCounts()
        ));
    }

    public function terima(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'verifikator_id'     => 'required|exists:verifikators,id',
            'tanggal_verifikasi' => 'required|date',
        ]);

        if ($progress->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya progress berstatus PENDING yang dapat diterima.');
        }

        $progress->update([
            'status'             => 'DITERIMA',
            'verifikator_id'     => $request->verifikator_id,
            'tanggal_verifikasi' => $request->tanggal_verifikasi,
            'actioned_at'        => now(),
        ]);

        $dataEntry = $progress->dataEntry;
        if ($dataEntry) {
            $this->penagihanService->cekDanBuatTagihan($dataEntry);
        }

        return redirect()->back()->with('success', 'Progress berhasil diterima.');
    }

    public function revisi(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        if ($progress->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya progress berstatus PENDING yang dapat direvisi.');
        }

        $progress->update([
            'status'            => 'REVISI',
            'keterangan_revisi' => $request->keterangan_revisi,
            'actioned_at'       => now(),
        ]);

        return redirect()->back()->with('success', 'Progress ditandai perlu revisi.');
    }

    public function tolak(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        if ($progress->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Hanya progress berstatus PENDING yang dapat ditolak.');
        }

        $progress->update([
            'status'            => 'DITOLAK',
            'keterangan_revisi' => $request->keterangan_revisi,
            'actioned_at'       => now(),
        ]);

        $dataLapangan = $progress->dataLapangan;
        $oldData      = $progress->old_data;

        // DEBUG — hapus setelah fix
        Log::info('TOLAK DEBUG', [
            'progress_id'        => $progress->id,
            'old_data'           => $oldData,
            'dataLapangan_id'    => $dataLapangan?->id,
            'dataLapangan_status' => $dataLapangan?->status,
        ]);

        if ($dataLapangan && !empty($oldData['status'])) {
            $dataLapangan->update(['status' => $oldData['status']]);
        }

        return redirect()->back()->with('success', 'Progress berhasil ditolak.');
    }
    public function bulkTerima(Request $request): RedirectResponse
    {
        $request->validate([
            'progress_ids'       => 'required|array|min:1',
            'progress_ids.*'     => 'exists:data_entry_progress,id',
            'verifikator_id'     => 'required|exists:verifikators,id',
            'tanggal_verifikasi' => 'required|date',
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
                'status'             => 'DITERIMA',
                'verifikator_id'     => $request->verifikator_id,
                'tanggal_verifikasi' => $request->tanggal_verifikasi,
                'actioned_at'        => now(),
            ]);

            if ($progress->data_entry_id) {
                $dataEntryIds[] = $progress->data_entry_id;
            }
        }

        foreach (array_unique($dataEntryIds) as $dataEntryId) {
            $dataEntry = DataEntry::find($dataEntryId);
            if ($dataEntry) {
                $this->penagihanService->cekDanBuatTagihan($dataEntry);
            }
        }

        return redirect()->back()->with('success', $progresses->count() . ' progress berhasil diterima.');
    }
}
