<?php

namespace App\Observers;

use App\Models\DataLapangan;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Services\FcmService;
use App\Services\Integrasi\Wrgroup\WrgroupEventService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DataLapanganObserver
{
    public function __construct(private WrgroupEventService $wrgroup) {}

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function isDataEntry(): bool
    {
        return Auth::check() && Auth::user()->role === 'data_entry';
    }

    private function getDataEntryId(): ?int
    {
        if (!Auth::check()) return null;
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();
        return $dataEntry?->id;
    }

    // -------------------------------------------------------------------------
    // Observer Hooks
    // -------------------------------------------------------------------------

    public function created(DataLapangan $dataLapangan): void
    {
        // Sengaja tidak mencatat progress saat created.
        // Data baru selalu masuk via formulir-halal publik (tanpa auth yang relevan),
        // sehingga mencatat progress di sini akan menimbulkan entri palsu
        // ketika seorang data_entry kebetulan masih login di browser yang sama.
        // Progress hanya dicatat pada aksi 'updated' (edit dari panel data-entry).
    }

    public function updated(DataLapangan $dataLapangan): void
    {
        // 1) Progress log — hanya untuk role data_entry, skip lock-only changes
        $this->handleProgressLog($dataLapangan);

        // 2) FCM notification — kirim jika status berubah menjadi DIBAYAR
        $this->handleStatusNotification($dataLapangan);

        // 3) Lapor ke WRGROUP Super Apps — kirim jika status berubah menjadi DIBAYAR
        $this->handleWrgroupSync($dataLapangan);
    }

    public function deleted(DataLapangan $dataLapangan): void
    {
        // Data hanya bisa dihapus oleh Superadmin/Admin Umum — bukan data_entry.
        // isDataEntry() guard sudah mencegah eksekusi, tapi dikosongkan
        // untuk konsistensi dengan created() dan menghindari kebingungan di masa mendatang.
    }

    // -------------------------------------------------------------------------
    // Progress Log
    // -------------------------------------------------------------------------

    private function handleProgressLog(DataLapangan $dataLapangan): void
    {
        if (!$this->isDataEntry()) return;

        $lockFields  = ['is_being_edited', 'edited_by', 'edit_expires_at', 'updated_at'];
        $changedKeys = array_keys($dataLapangan->getChanges());

        // Abaikan jika hanya field lock/unlock yang berubah
        if (empty(array_diff($changedKeys, $lockFields))) return;

        $this->logProgress(
            $dataLapangan,
            'updated',
            $dataLapangan->getOriginal(),
            $dataLapangan->getChanges()
        );
    }

    private function logProgress(
        DataLapangan $dataLapangan,
        string       $action,
        ?array       $oldData,
        ?array       $newData
    ): void {
        if (!$this->isDataEntry()) return;

        DataEntryProgress::create([
            'user_id'          => Auth::id(),
            'data_entry_id'    => $this->getDataEntryId(),
            'data_lapangan_id' => $dataLapangan->id,
            'action'           => $action,
            'old_data'         => $oldData,
            'new_data'         => $newData,
            'actioned_at'      => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // FCM Notification
    // -------------------------------------------------------------------------

    private function handleStatusNotification(DataLapangan $dataLapangan): void
    {
        if (!$dataLapangan->wasChanged('status_pembayaran')) return;

        if (strtoupper(trim($dataLapangan->status_pembayaran)) === 'DIBAYAR') {
            $this->notifyDibayar($dataLapangan);
        }
    }

    private function notifyDibayar(DataLapangan $dataLapangan): void
    {
        // Pastikan relasi ter-load agar tidak terjadi N+1 query
        $dataLapangan->loadMissing('enumerator.user');

        $user = $dataLapangan->enumerator?->user ?? null;

        if (!$user || !$user->fcm_token) {
            Log::warning('[FCM] FCM token tidak ditemukan', [
                'data_lapangan_id' => $dataLapangan->id,
                'enumerator_id'    => $dataLapangan->enumerator_id,
                'user_id'          => $user?->id,
            ]);
            return;
        }

        $namaPu = $dataLapangan->nama_pu ?? 'Pelaku Usaha';

        FcmService::send(
            fcmToken: $user->fcm_token,
            title: '💰 Pembayaran Dikonfirmasi!',
            body: "Pembayaran untuk {$namaPu} telah berhasil dikonfirmasi.",
            data: [
                'type'             => 'status_dibayar',
                'data_lapangan_id' => (string) $dataLapangan->id,
                'click_action'     => 'FLUTTER_NOTIFICATION_CLICK',
            ]
        );
    }

    // -------------------------------------------------------------------------
    // WRGROUP Super Apps
    // -------------------------------------------------------------------------

    /**
     * Lapor kasus sertifikasi lunas ke WRGROUP (invoice + pembayaran, satu event gabungan —
     * lihat WrgroupEventService::recordDataLapanganPaid()). Guard sama persis dengan logic
     * cashflow lokal di DataLapangan::booted(), sehingga fee yang dilaporkan konsisten dengan
     * yang dibukukan ke CashflowsKoordinator/Cashflow. Best-effort: kegagalan integrasi WRGROUP
     * tidak pernah mengganggu alur pembayaran (lihat .agent/workflows/wrgroup-integrasi.md).
     */
    private function handleWrgroupSync(DataLapangan $dataLapangan): void
    {
        if (! $dataLapangan->wasChanged('status_pembayaran')) {
            return;
        }

        if (strtoupper(trim((string) $dataLapangan->status_pembayaran)) !== 'DIBAYAR') {
            return;
        }

        $this->wrgroup->recordDataLapanganPaid($dataLapangan);
    }
}
