<?php

namespace App\Observers;

use App\Models\DataLapangan;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Services\FcmService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DataLapanganObserver
{
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
        $this->logProgress($dataLapangan, 'created', null, $dataLapangan->toArray());
    }

    public function updated(DataLapangan $dataLapangan): void
    {
        // 1) Progress log — hanya untuk role data_entry, skip lock-only changes
        $this->handleProgressLog($dataLapangan);

        // 2) FCM notification — kirim jika status berubah menjadi DIBAYAR
        $this->handleStatusNotification($dataLapangan);
    }

    public function deleted(DataLapangan $dataLapangan): void
    {
        $this->logProgress($dataLapangan, 'deleted', $dataLapangan->toArray(), null);
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
        $user = $dataLapangan->enumerator ?? $dataLapangan->user ?? null;

        if (!$user?->fcm_token) {
            Log::warning('[FCM] FCM token tidak ditemukan', [
                'data_lapangan_id' => $dataLapangan->id,
                'user_id'          => $user?->id,
            ]);
            return;
        }

        FcmService::send(
            fcmToken: $user->fcm_token,
            title: '💰 Pembayaran Dikonfirmasi!',
            body: "Pembayaran untuk {$dataLapangan->nama_pu} telah berhasil dikonfirmasi.",
            data: [
                'type'             => 'status_dibayar',
                'data_lapangan_id' => (string) $dataLapangan->id,
                'click_action'     => 'FLUTTER_NOTIFICATION_CLICK',
            ]
        );
    }
}
