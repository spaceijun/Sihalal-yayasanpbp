<?php

namespace App\Services\Superadmin;

use App\Models\DataLapangan;

class StatusService
{
    /**
     * Update status data lapangan
     */
    public function updateStatus(DataLapangan $dataLapangan, string $newStatus): array
    {
        $oldStatus = $dataLapangan->status;
        $dataLapangan->status = $newStatus;
        $dataLapangan->save();

        return [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'message' => "Status berhasil diubah dari <strong>{$oldStatus}</strong> menjadi <strong>{$newStatus}</strong>"
        ];
    }

    /**
     * Update status pembayaran
     */
    public function updateStatusPayment(DataLapangan $dataLapangan, string $newStatus): array
    {
        $oldStatus = $dataLapangan->status_pembayaran;
        $dataLapangan->status_pembayaran = $newStatus;
        $dataLapangan->save();

        return [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'message' => "Status Pembayaran berhasil diubah dari <strong>{$oldStatus}</strong> menjadi <strong>{$newStatus}</strong>"
        ];
    }

    /**
     * Determine status based on file type
     */
    public function determineStatusByFileType(string $fileType): string
    {
        return match ($fileType) {
            'oss' => 'PROGRESS OSS',
            'sihalal' => 'TERBIT SH',
            default => 'PENDING'
        };
    }

    /**
     * Determine status after file deletion
     */
    public function determineStatusAfterDeletion(string $fileType, DataLapangan $dataLapangan): array
    {
        if ($fileType === 'oss') {
            return [
                'status' => 'PENDING',
                'message' => 'Status dikembalikan ke PENDING'
            ];
        }

        if ($fileType === 'sihalal') {
            if ($dataLapangan->file_oss) {
                return [
                    'status' => 'PROGRESS OSS',
                    'message' => 'Status dikembalikan ke PROGRESS OSS'
                ];
            }
            return [
                'status' => 'PENDING',
                'message' => 'Status dikembalikan ke PENDING'
            ];
        }

        return [
            'status' => 'PENDING',
            'message' => 'Status dikembalikan ke PENDING'
        ];
    }
}
