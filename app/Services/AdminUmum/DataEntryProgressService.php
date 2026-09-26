<?php

namespace App\Services\AdminUmum;

use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\Verifikator;
use App\Services\DataEntryPenagihanService;
use App\Services\Superadmin\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataEntryProgressService
{
    public function __construct(
        private DataEntryPenagihanService $penagihanService,
        private NotificationService $notificationService,
    ) {}

    /**
     * Hitung jumlah progress (action=created) per status, dipakai untuk badge tab.
     */
    public function getStatusCounts(): array
    {
        return [
            'countPending' => DataEntryProgress::where('action', 'created')->where('status', 'PENDING')->count(),
            'countValidasiAdmin' => DataEntryProgress::where('action', 'created')->where('status', 'VALIDASI_ADMIN')->count(),
            'countRevisi' => DataEntryProgress::where('action', 'created')->where('status', 'REVISI')->count(),
            'countDiterima' => DataEntryProgress::where('action', 'created')->where('status', 'DITERIMA')->count(),
            'countDitolak' => DataEntryProgress::where('action', 'created')->where('status', 'DITOLAK')->count(),
        ];
    }

    /**
     * Cek apakah data entry masih punya progress PENDING yang menunggu review.
     */
    private function cekAdaPending(int $dataEntryId): bool
    {
        return DataEntryProgress::where('data_entry_id', $dataEntryId)
            ->where('action', 'created')
            ->where('status', 'PENDING')
            ->exists();
    }

    /**
     * Query dasar untuk DataTables JSON endpoint (Admin Umum hanya melihat PENDING & REVISI).
     */
    public function getListingQuery(Request $request): Builder
    {
        $query = DataEntryProgress::with([
            'dataLapangan',
            'dataEntry.user',
            'verifikator',
        ])->where('action', 'created');

        // Filter status: jika ada filter spesifik gunakan itu, jika tidak default ke PENDING
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            // Tab "Butuh Review" default: tampilkan PENDING saja
            $query->where('status', 'PENDING');
        }

        if ($request->filled('entry_type')) {
            $query->whereHas('dataEntry', fn ($q) => $q->where('entry_type', $request->entry_type));
        }

        return $query;
    }

    /**
     * Data untuk halaman index: daftar progress (paginated), filter, verifikator & status counts.
     */
    public function getIndexData(Request $request): array
    {
        $query = DataEntryProgress::with([
            'dataLapangan',
            'dataEntry.user',
            'verifikator',
        ])->where('action', 'created');

        // Admin Umum hanya melihat data PENDING dan REVISI
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['PENDING', 'REVISI']);
        }

        if ($request->filled('entry_type')) {
            $query->whereHas(
                'dataEntry',
                fn ($q) => $q->where('entry_type', $request->entry_type)
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'dataLapangan',
                    fn ($q2) => $q2->where('nama_pu', 'like', "%{$search}%")
                )->orWhereHas(
                    'dataEntry.user',
                    fn ($q2) => $q2->where('name', 'like', "%{$search}%")
                );
            });
        }

        $progresses = $query->latest('actioned_at')->paginate(20)->withQueryString();
        $verifikators = Verifikator::orderBy('nama_lengkap')->get();

        return array_merge(
            compact('progresses', 'verifikators'),
            $this->getStatusCounts()
        );
    }

    /**
     * Data untuk halaman show: detail progress, histori progress data entry terkait,
     * verifikator, serta flag apakah Admin Umum boleh mengambil tindakan.
     */
    public function getShowData(DataEntryProgress $progress): array
    {
        $progress->load(['dataLapangan.enumerator', 'dataEntry.user', 'verifikator']);

        $progresses = DataEntryProgress::with(['dataLapangan', 'dataEntry.user', 'verifikator'])
            ->where('action', 'created')
            ->where('data_entry_id', $progress->data_entry_id)
            ->latest('actioned_at')
            ->paginate(20);

        $verifikators = Verifikator::orderBy('nama_lengkap')->get();

        // Tentukan apakah admin umum boleh mengambil tindakan (hanya PENDING & REVISI)
        $canAct = in_array($progress->status, ['PENDING', 'REVISI']);

        return array_merge(
            compact('progress', 'progresses', 'verifikators', 'canAct'),
            $this->getStatusCounts()
        );
    }

    /**
     * Validasi progress oleh Admin Umum.
     * Admin Umum hanya bisa memvalidasi progress dengan status PENDING dan REVISI.
     *
     * Logika:
     * - PENDING SIHALAL: Status progress = "VALIDASI_ADMIN", data_lapangans.status TIDAK berubah
     * - PENDING OSS: Status progress = "DITERIMA", data_lapangans.status = "PROGRESS OSS"
     * - REVISI: Langsung terima = "DITERIMA", data_lapangans.status TIDAK berubah
     *
     * @return array{success: bool, message: string}
     */
    public function validasi(Request $request, DataEntryProgress $progress): array
    {
        // Admin Umum hanya bisa memvalidasi progress dengan status PENDING dan REVISI
        if (! in_array($progress->status, ['PENDING', 'REVISI'])) {
            return ['success' => false, 'message' => 'Hanya progress berstatus PENDING atau REVISI yang dapat divalidasi.'];
        }

        return DB::transaction(function () use ($request, $progress) {
            $progress->loadMissing('dataEntry');
            $entryType = $progress->dataEntry?->entry_type;

            // Jika status REVISI, langsung terima tanpa mengubah alur
            if ($progress->status === 'REVISI') {
                $request->validate([
                    'verifikator_id' => 'required|exists:verifikators,id',
                    'tanggal_verifikasi' => 'required|date',
                ]);

                $progress->update([
                    'status' => 'DITERIMA',
                    'verifikator_id' => $request->verifikator_id,
                    'tanggal_verifikasi' => $request->tanggal_verifikasi,
                    'actioned_at' => now(),
                ]);

                // Jika entry type OSS, cek dan buat tagihan otomatis
                $dataEntry = $progress->dataEntry;
                if ($dataEntry && $entryType === 'OSS' && ! $this->cekAdaPending($dataEntry->id)) {
                    $penagihan = $this->penagihanService->cekDanBuatTagihan($dataEntry);
                    if ($penagihan) {
                        return [
                            'success' => true,
                            'message' => 'Progress berhasil diterima. '.
                                'Tagihan baru sebesar Rp '.number_format($penagihan->nominal, 0, ',', '.').
                                ' ('.$penagihan->jumlah_paket.' paket) otomatis dibuat.',
                        ];
                    }
                }

                return ['success' => true, 'message' => 'Progress berhasil diterima.'];
            }

            // PENDING
            if ($entryType === 'SIHALAL') {
                // SIHALAL: Ubah status progress menjadi VALIDASI_ADMIN, data_lapangans.status tetap
                $progress->update([
                    'status' => 'VALIDASI_ADMIN',
                    'actioned_at' => now(),
                ]);

                return ['success' => true, 'message' => 'Progress berhasil divalidasi. Menunggu konfirmasi Superadmin untuk SIHALAL.'];

            } elseif ($entryType === 'OSS') {
                // OSS: Langsung terima dan ubah data_lapangans.status menjadi PROGRESS OSS
                $request->validate([
                    'verifikator_id' => 'required|exists:verifikators,id',
                    'tanggal_verifikasi' => 'required|date',
                ]);

                $progress->update([
                    'status' => 'DITERIMA',
                    'verifikator_id' => $request->verifikator_id,
                    'tanggal_verifikasi' => $request->tanggal_verifikasi,
                    'actioned_at' => now(),
                ]);

                // Update data_lapangans.status ke PROGRESS OSS
                $dataLapangan = $progress->dataLapangan;
                if ($dataLapangan) {
                    $dataLapangan->update(['status' => 'PROGRESS OSS']);
                }

                // Cek dan buat tagihan otomatis jika sudah ≥ 15 data DITERIMA
                $dataEntry = $progress->dataEntry;
                if ($dataEntry && ! $this->cekAdaPending($dataEntry->id)) {
                    $penagihan = $this->penagihanService->cekDanBuatTagihan($dataEntry);
                    if ($penagihan) {
                        return [
                            'success' => true,
                            'message' => 'Progress OSS berhasil diterima dan status diubah ke PROGRESS OSS. '.
                                'Tagihan baru sebesar Rp '.number_format($penagihan->nominal, 0, ',', '.').
                                ' ('.$penagihan->jumlah_paket.' paket) otomatis dibuat.',
                        ];
                    }
                }

                return ['success' => true, 'message' => 'Progress OSS berhasil diterima dan status diubah ke PROGRESS OSS.'];

            } else {
                // Fallback: terima langsung
                $request->validate([
                    'verifikator_id' => 'required|exists:verifikators,id',
                    'tanggal_verifikasi' => 'required|date',
                ]);

                $progress->update([
                    'status' => 'DITERIMA',
                    'verifikator_id' => $request->verifikator_id,
                    'tanggal_verifikasi' => $request->tanggal_verifikasi,
                    'actioned_at' => now(),
                ]);

                return ['success' => true, 'message' => 'Progress berhasil diterima.'];
            }
        });
    }

    /**
     * Bulk validasi progress oleh Admin Umum.
     * Admin Umum hanya bisa bulk validasi progress dengan status PENDING dan REVISI.
     *
     * @return array{success: bool, message: string}
     */
    public function bulkValidasi(Request $request): array
    {
        $request->validate([
            'progress_ids' => 'required|array|min:1',
            'progress_ids.*' => 'string',
        ]);

        $realIds = collect($request->progress_ids)
            ->map(fn ($hashedId) => DataEntryProgress::findByHashedId($hashedId)?->id)
            ->filter()
            ->values()
            ->all();

        if (empty($realIds)) {
            return ['success' => false, 'message' => 'Tidak ada progress valid yang dipilih.'];
        }

        return DB::transaction(function () use ($request, $realIds) {
            // Admin Umum hanya bisa bulk validasi progress dengan status PENDING dan REVISI
            $progresses = DataEntryProgress::with('dataEntry')
                ->whereIn('id', $realIds)
                ->whereIn('status', ['PENDING', 'REVISI'])
                ->get();

            if ($progresses->isEmpty()) {
                return ['success' => false, 'message' => 'Tidak ada progress PENDING atau REVISI yang dipilih.'];
            }

            $sihalalCount = 0;
            $ossCount = 0;
            $revisiCount = 0;
            $ossDataEntryIds = [];

            foreach ($progresses as $progress) {
                $dataEntry = $progress->dataEntry;
                $entryType = $dataEntry?->entry_type;

                if ($progress->status === 'REVISI') {
                    // Langsung terima REVISI
                    $progress->update([
                        'status' => 'DITERIMA',
                        'verifikator_id' => $request->verifikator_id ?? null,
                        'tanggal_verifikasi' => $request->tanggal_verifikasi ?? now()->toDateString(),
                        'actioned_at' => now(),
                    ]);
                    $revisiCount++;

                    // Jika REVISI dengan entry_type OSS, tandai untuk cek tagihan
                    if ($entryType === 'OSS' && $progress->data_entry_id) {
                        $ossDataEntryIds[] = $progress->data_entry_id;
                    }

                } elseif ($entryType === 'SIHALAL') {
                    // SIHALAL: Ubah status progress menjadi VALIDASI_ADMIN
                    $progress->update([
                        'status' => 'VALIDASI_ADMIN',
                        'actioned_at' => now(),
                    ]);
                    $sihalalCount++;

                } elseif ($entryType === 'OSS') {
                    // OSS: Langsung terima
                    $progress->update([
                        'status' => 'DITERIMA',
                        'verifikator_id' => $request->verifikator_id ?? null,
                        'tanggal_verifikasi' => $request->tanggal_verifikasi ?? now()->toDateString(),
                        'actioned_at' => now(),
                    ]);

                    // Update data_lapangans.status ke PROGRESS OSS
                    $dataLapangan = $progress->dataLapangan;
                    if ($dataLapangan) {
                        $dataLapangan->update(['status' => 'PROGRESS OSS']);
                    }

                    if ($progress->data_entry_id) {
                        $ossDataEntryIds[] = $progress->data_entry_id;
                    }
                    $ossCount++;
                }
            }

            // Cek dan buat tagihan untuk setiap data entry OSS yang punya data DITERIMA ≥ 15
            $penagihanDibuat = 0;
            foreach (array_unique($ossDataEntryIds) as $dataEntryId) {
                $dataEntry = DataEntry::find($dataEntryId);
                if (! $dataEntry) {
                    continue;
                }

                // Tahan pembuatan tagihan jika masih ada PENDING lain
                if ($this->cekAdaPending($dataEntryId)) {
                    continue;
                }

                $penagihan = $this->penagihanService->cekDanBuatTagihan($dataEntry);
                if ($penagihan) {
                    $penagihanDibuat++;
                }
            }

            $msg = $progresses->count().' progress berhasil divalidasi.';
            if ($revisiCount > 0) {
                $msg .= " {$revisiCount} REVISI langsung diterima.";
            }
            if ($sihalalCount > 0) {
                $msg .= " {$sihalalCount} SIHALAL menunggu konfirmasi Superadmin.";
            }
            if ($ossCount > 0) {
                $msg .= " {$ossCount} OSS langsung diterima (PROGRESS OSS).";
            }
            if ($penagihanDibuat > 0) {
                $msg .= " {$penagihanDibuat} tagihan baru otomatis dibuat.";
            }

            return ['success' => true, 'message' => $msg];
        });
    }

    /**
     * Minta revisi progress.
     * Admin Umum hanya bisa minta revisi untuk progress dengan status PENDING dan REVISI.
     *
     * @return array{success: bool, message: string}
     */
    public function revisi(Request $request, DataEntryProgress $progress): array
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        // Admin Umum hanya bisa minta revisi untuk PENDING dan REVISI
        if (! in_array($progress->status, ['PENDING', 'REVISI'])) {
            return ['success' => false, 'message' => 'Hanya progress berstatus PENDING atau REVISI yang dapat direvisi.'];
        }

        DB::transaction(function () use ($request, $progress) {
            $progress->update([
                'status' => 'REVISI',
                'keterangan_revisi' => $request->keterangan_revisi,
                'actioned_at' => now(),
            ]);

            // Kirim notifikasi WhatsApp ke data entry
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
        });

        return ['success' => true, 'message' => 'Progress ditandai perlu revisi.'];
    }

    /**
     * Tolak progress.
     * Admin Umum hanya bisa menolak progress dengan status PENDING dan REVISI.
     *
     * Logika:
     * - PENDING/REVISI: Status progress = DITOLAK, data_lapangans.status TIDAK berubah
     *
     * @return array{success: bool, message: string}
     */
    public function tolak(Request $request, DataEntryProgress $progress): array
    {
        $request->validate([
            'keterangan_revisi' => 'required|string|max:1000',
        ]);

        // Admin Umum hanya bisa menolak PENDING dan REVISI
        if (! in_array($progress->status, ['PENDING', 'REVISI'])) {
            return ['success' => false, 'message' => 'Hanya progress berstatus PENDING atau REVISI yang dapat ditolak.'];
        }

        DB::transaction(function () use ($request, $progress) {
            $progress->update([
                'status' => 'DITOLAK',
                'keterangan_revisi' => $request->keterangan_revisi,
                'actioned_at' => now(),
            ]);

            // data_lapangans.status TIDAK berubah saat ditolak
            // Lock editing tetap dilepas agar data bisa diedit ulang
            $dataLapangan = $progress->dataLapangan;
            if ($dataLapangan) {
                $dataLapangan->update([
                    'is_being_edited' => false,
                    'edited_by' => null,
                    'edit_expires_at' => null,
                    'is_unlocked_for_data_entry' => true,
                ]);
            }
        });

        return ['success' => true, 'message' => 'Progress berhasil ditolak.'];
    }
}
