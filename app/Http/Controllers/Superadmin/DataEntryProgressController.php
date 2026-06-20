<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\Verifikator;
use Yajra\DataTables\Facades\DataTables;
use App\Services\DataEntryPenagihanService;
use App\Services\Superadmin\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DataEntryProgressController extends Controller
{
    use HasRoutePrefix;

    protected $penagihanService;
    protected $notificationService;

    public function __construct(DataEntryPenagihanService $penagihanService, NotificationService $notificationService)
    {
        $this->penagihanService = $penagihanService;
        $this->notificationService = $notificationService;
    }

    private function getStatusCounts(): array
    {
        return [
            'countPending'  => DataEntryProgress::where('action', 'created')->where('status', 'PENDING')->count(),
            'countValidasiAdmin' => DataEntryProgress::where('action', 'created')->where('status', 'VALIDASI_ADMIN')->count(),
            'countRevisi'   => DataEntryProgress::where('action', 'created')->where('status', 'REVISI')->count(),
            'countDiterima' => DataEntryProgress::where('action', 'created')->where('status', 'DITERIMA')->count(),
            'countDitolak'  => DataEntryProgress::where('action', 'created')->where('status', 'DITOLAK')->count(),
        ];
    }

    /**
     * Cek apakah data entry masih punya progress PENDING selain progress yang baru saja diproses.
     */
    private function cekAdaPending(int $dataEntryId): bool
    {
        return DataEntryProgress::where('data_entry_id', $dataEntryId)
            ->where('action', 'created')
            ->where('status', 'PENDING')
            ->exists();
    }

    /**
     * Yajra DataTables JSON endpoint untuk tabel progress di halaman index.
     * Superadmin hanya melihat VALIDASI_ADMIN (sudah divalidasi Admin Umum)
     */
    public function data(Request $request)
    {
        $query = DataEntryProgress::with([
            'dataLapangan',
            'dataEntry.user',
            'verifikator',
        ])->where('action', 'created');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            // Default: Superadmin hanya melihat VALIDASI_ADMIN
            $query->where('status', 'VALIDASI_ADMIN');
        }

        if ($request->filled('entry_type')) {
            $query->whereHas('dataEntry', fn($q) => $q->where('entry_type', $request->entry_type));
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($p) {
                // Checkbox untuk VALIDASI_ADMIN
                if ($p->status !== 'VALIDASI_ADMIN') return '';
                return '<input type="checkbox" name="progress_ids[]" value="' . $p->hashed_id . '" class="form-check-input row-check">';
            })
            ->addColumn('tanggal', fn($p) => $p->actioned_at?->format('d/m/Y H:i') ?? '-')
            ->addColumn('data_entry_cell', function ($p) {
                $init = strtoupper(substr($p->dataEntry?->user?->name ?? 'U', 0, 2));
                $name = e($p->dataEntry?->user?->name ?? '-');
                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);font-size:11px;">' . $init . '</div>
                    <strong style="font-size:13px;">' . $name . '</strong>
                </div>';
            })
            ->addColumn('type_badge', function ($p) {
                return match ($p->dataEntry?->entry_type) {
                    'OSS'     => '<span class="adm-badge adm-badge-oss">OSS</span>',
                    'SIHALAL' => '<span class="adm-badge adm-badge-sihalal">SIHALAL</span>',
                    default   => '<span class="adm-badge adm-badge-nonaktif">—</span>',
                };
            })
            ->addColumn('nama_pu_cell', function ($p) {
                $url    = route($this->routePrefix() . '.data-entry-progress.show', $p->hashed_id);
                $nama   = e($p->dataLapangan?->nama_pu ?? '-');
                $nik    = e($p->dataLapangan?->nik ?? '');
                return '<a href="' . $url . '" style="font-weight:600;font-size:13px;color:var(--adm-blue);text-decoration:none;">' . $nama . '</a>'
                    . '<div style="font-size:11px;color:var(--adm-text-muted);">' . $nik . '</div>';
            })
            ->addColumn('status_badge', function ($p) {
                return match ($p->status) {
                    'PENDING'  => '<span class="adm-badge adm-badge-pending"><span class="dot"></span>PENDING</span>',
                    'VALIDASI_ADMIN' => '<span class="adm-badge adm-badge-info"><span class="dot"></span>VALIDASI ADMIN</span>',
                    'DITERIMA' => '<span class="adm-badge adm-badge-success"><span class="dot"></span>DITERIMA</span>',
                    'REVISI'   => '<span class="adm-badge adm-badge-revisi"><span class="dot"></span>REVISI</span>',
                    'DITOLAK'  => '<span class="adm-badge adm-badge-danger"><span class="dot"></span>DITOLAK</span>',
                    default    => '',
                };
            })
            ->addColumn('verifikator_cell', function ($p) {
                if ($p->verifikator) {
                    return '<div style="font-weight:600;font-size:13px;">' . e($p->verifikator->nama_lengkap) . '</div>'
                        . '<div style="font-size:11px;color:var(--adm-text-muted);">' . ($p->tanggal_verifikasi?->format('d/m/Y') ?? '') . '</div>';
                }
                return '<span style="color:var(--adm-text-faint);">—</span>';
            })
            ->addColumn('keterangan_cell', function ($p) {
                if (!$p->keterangan_revisi && !$p->keterangan_update) {
                    return '<span style="color:var(--adm-text-faint);">—</span>';
                }
                $cls   = $p->keterangan_update ? 'success' : 'danger';
                $label = $p->keterangan_update ? 'Sudah Direvisi' : 'Perlu Revisi';
                $kr    = $p->keterangan_revisi ? "'" . addslashes(e($p->keterangan_revisi)) . "'" : 'null';
                $ku    = $p->keterangan_update ? "'" . addslashes(e($p->keterangan_update)) . "'" : 'null';
                return '<button type="button" class="adm-btn ' . $cls . '" onclick="lihatKeterangan(' . $kr . ',' . $ku . ')">' . $label . '</button>';
            })
            ->addColumn('aksi', function ($p) {
                // Aksi untuk VALIDASI_ADMIN saja
                if ($p->status === 'VALIDASI_ADMIN') {
                    $type = $p->dataEntry?->entry_type;
                    return '<div class="adm-actions">
                        <button type="button" class="adm-btn success icon-only" title="Terima" onclick="submitTerima(\'' . $p->hashed_id . '\',\'' . $type . '\')">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                        <button type="button" class="adm-btn warning icon-only" title="Minta Revisi" onclick="bukaModalRevisi(\'' . $p->hashed_id . '\')">
                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button type="button" class="adm-btn danger icon-only" title="Tolak" onclick="bukaModalTolak(\'' . $p->hashed_id . '\')">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>';
                }
                $url = route($this->routePrefix() . '.data-entry-progress.show', $p->hashed_id);
                return '<div class="adm-actions">
                    <a href="' . $url . '" class="adm-btn primary icon-only" title="Lihat Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                </div>';
            })
            ->filterColumn(
                'nama_pu_cell',
                fn($q, $k) =>
                $q->whereHas('dataLapangan', fn($q2) => $q2->where('nama_pu', 'like', "%{$k}%"))
            )
            ->filterColumn(
                'data_entry_cell',
                fn($q, $k) =>
                $q->whereHas('dataEntry.user', fn($q2) => $q2->where('name', 'like', "%{$k}%"))
            )
            ->rawColumns(['checkbox', 'data_entry_cell', 'type_badge', 'nama_pu_cell', 'status_badge', 'verifikator_cell', 'keterangan_cell', 'aksi'])
            ->make(true);
    }

    public function index(Request $request): View
    {
        $query = DataEntryProgress::with([
            'dataLapangan',
            'dataEntry.user',
            'verifikator',
        ])->where('action', 'created');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            // Default: Superadmin hanya melihat VALIDASI_ADMIN
            $query->where('status', 'VALIDASI_ADMIN');
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
            ['routePrefix' => $this->routePrefix()],
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
            ['routePrefix' => $this->routePrefix()],
            $this->getStatusCounts()
        ));
    }

    /**
     * Terima satu progress.
     * Superadmin hanya bisa menerima VALIDASI_ADMIN.
     *
     * Logika:
     * - VALIDASI_ADMIN (SIHALAL): terima → DITERIMA, data_lapangans = PROGRESS SIHALAL
     */
    public function terima(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        // Superadmin hanya bisa menerima VALIDASI_ADMIN
        if ($progress->status !== 'VALIDASI_ADMIN') {
            return redirect()->back()->with('error', 'Hanya progress berstatus VALIDASI ADMIN yang dapat diterima.');
        }

        // Wajib pilih verifikator
        $request->validate([
            'verifikator_id'     => 'required|exists:verifikators,id',
            'tanggal_verifikasi' => 'required|date',
        ]);

        $progress->update([
            'status'             => 'DITERIMA',
            'verifikator_id'     => $request->verifikator_id,
            'tanggal_verifikasi' => $request->tanggal_verifikasi,
            'actioned_at'        => now(),
        ]);

        // Update data_lapangans ke PROGRESS SIHALAL
        $dataLapangan = $progress->dataLapangan;
        if ($dataLapangan) {
            $dataLapangan->update(['status' => 'PROGRESS SIHALAL']);
        }

        $dataEntry = $progress->dataEntry;
        if (!$dataEntry) {
            return redirect()->back()->with('success', 'Progress berhasil diterima.');
        }

        // Cek apakah masih ada PENDING lain
        $adaPending = $this->cekAdaPending($dataEntry->id);
        if ($adaPending) {
            return redirect()->back()->with(
                'success',
                'Progress berhasil diterima. ' .
                    'Tagihan belum dibuat karena masih ada data lain yang menunggu review.'
            );
        }

        // Buat tagihan
        $penagihan = $this->penagihanService->cekDanBuatTagihan($dataEntry);
        if ($penagihan) {
            return redirect()->back()->with(
                'success',
                'Progress berhasil diterima. ' .
                    'Tagihan baru sebesar Rp ' . number_format($penagihan->nominal, 0, ',', '.') .
                    ' (' . $penagihan->jumlah_paket . ' paket) otomatis dibuat.'
            );
        }

        return redirect()->back()->with('success', 'Progress berhasil diterima.');
    }

    public function revisi(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        // Superadmin hanya bisa minta revisi untuk VALIDASI_ADMIN
        if ($progress->status !== 'VALIDASI_ADMIN') {
            return redirect()->back()->with('error', 'Hanya progress berstatus VALIDASI ADMIN yang dapat direvisi.');
        }

        $progress->update([
            'status'            => 'REVISI',
            'keterangan_revisi' => $request->keterangan_revisi,
            'actioned_at'       => now(),
        ]);

        // Kirim notifikasi WhatsApp ke telephone data entry
        $dataEntry = $progress->dataEntry;
        if ($dataEntry) {
            $progress->loadMissing('dataLapangan');
            $namaPU = $progress->dataLapangan?->nama_pu ?? '-';

            $this->notificationService->sendDataEntryRevisiNotification(
                dataEntry: $dataEntry,
                namaPU: $namaPU,
                keteranganRevisi: $request->keterangan_revisi,
            );
        }

        return redirect()->back()->with('success', 'Progress ditandai perlu revisi.');
    }

    public function tolak(Request $request, DataEntryProgress $progress): RedirectResponse
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        // Superadmin hanya bisa menolak VALIDASI_ADMIN
        if ($progress->status !== 'VALIDASI_ADMIN') {
            return redirect()->back()->with('error', 'Hanya progress berstatus VALIDASI ADMIN yang dapat ditolak.');
        }

        $progress->update([
            'status'            => 'DITOLAK',
            'keterangan_revisi' => $request->keterangan_revisi,
            'actioned_at'       => now(),
        ]);

        // Lepas lock editing dan tandai data available kembali
        // data_lapangans.status TIDAK berubah saat ditolak
        $dataLapangan = $progress->dataLapangan;
        if ($dataLapangan) {
            $dataLapangan->update([
                'is_being_edited'            => false,
                'edited_by'                  => null,
                'edit_expires_at'            => null,
                'is_unlocked_for_data_entry' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Progress berhasil ditolak.');
    }

    /**
     * Terima banyak progress sekaligus.
     * Superadmin hanya bisa bulk terima VALIDASI_ADMIN.
     *
     * Logika:
     * - VALIDASI_ADMIN (SIHALAL): terima → DITERIMA, data_lapangans = PROGRESS SIHALAL
     */
    public function bulkTerima(Request $request): RedirectResponse
    {
        $request->validate([
            'progress_ids'       => 'required|array|min:1',
            'progress_ids.*'     => 'string',
            'verifikator_id'     => 'required|exists:verifikators,id',
            'tanggal_verifikasi' => 'required|date',
        ]);

        // Decode semua hashed_id ke id asli
        $realIds = collect($request->progress_ids)
            ->map(fn($hashedId) => DataEntryProgress::findByHashedId($hashedId)?->id)
            ->filter()
            ->values()
            ->all();

        if (empty($realIds)) {
            return redirect()->back()->with('error', 'Tidak ada progress valid yang dipilih.');
        }

        // Superadmin hanya bisa bulk terima VALIDASI_ADMIN
        $progresses = DataEntryProgress::whereIn('id', $realIds)
            ->where('status', 'VALIDASI_ADMIN')
            ->get();

        if ($progresses->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada progress VALIDASI ADMIN yang dipilih.');
        }

        $dataEntryIds = [];

        foreach ($progresses as $progress) {
            $progress->update([
                'status'             => 'DITERIMA',
                'verifikator_id'     => $request->verifikator_id,
                'tanggal_verifikasi' => $request->tanggal_verifikasi,
                'actioned_at'        => now(),
            ]);

            // Update status data_lapangans ke PROGRESS SIHALAL
            $dataLapangan = $progress->dataLapangan;
            if ($dataLapangan) {
                $dataLapangan->update(['status' => 'PROGRESS SIHALAL']);
            }

            if ($progress->data_entry_id) {
                $dataEntryIds[] = $progress->data_entry_id;
            }
        }

        $penagihanDibuat = 0;

        foreach (array_unique($dataEntryIds) as $dataEntryId) {
            $dataEntry = DataEntry::find($dataEntryId);
            if (!$dataEntry) {
                continue;
            }

            // Cek apakah masih ada PENDING setelah bulk diterima
            if ($this->cekAdaPending($dataEntryId)) {
                continue; // Lewati pembuatan tagihan untuk data entry ini
            }

            $penagihan = $this->penagihanService->cekDanBuatTagihan($dataEntry);
            if ($penagihan) {
                $penagihanDibuat++;
            }
        }

        // Susun pesan feedback
        $msg = $progresses->count() . ' progress berhasil diterima.';

        if ($penagihanDibuat > 0) {
            $msg .= " {$penagihanDibuat} tagihan baru otomatis dibuat.";
        }

        return redirect()->back()->with('success', $msg);
    }
}
