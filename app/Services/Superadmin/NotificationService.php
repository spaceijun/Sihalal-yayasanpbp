<?php

namespace App\Services\Superadmin;

use App\Models\DataLapangan;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send revisi notification for single data
     */
    public function sendRevisiNotification(DataLapangan $dataLapangan): array
    {
        if (!$dataLapangan->keterangan) {
            return [
                'success' => false,
                'message' => 'Data tidak memiliki keterangan revisi'
            ];
        }

        $result = $dataLapangan->sendRevisiNotification();

        if ($result) {
            return [
                'success' => true,
                'message' => 'Notifikasi berhasil dikirim ke ' . $dataLapangan->enumerator->nama_lengkap
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengirim notifikasi'
        ];
    }

    /**
     * Send revisi notifications for all data
     */
    public function sendAllRevisiNotifications(): array
    {
        $dataLapangans = DataLapangan::with('enumerator')
            ->whereNotNull('keterangan')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada data revisi yang ditemukan',
                'data' => [
                    'success' => 0,
                    'failed' => 0,
                    'failed_data' => []
                ]
            ];
        }

        $successCount = 0;
        $failedCount = 0;
        $failedData = [];

        foreach ($dataLapangans as $dataLapangan) {
            $result = $dataLapangan->sendRevisiNotification();

            if ($result) {
                $successCount++;
            } else {
                $failedCount++;
                $failedData[] = $dataLapangan->nama_pu;
            }

            // Delay to avoid rate limit
            usleep(500000); // 0.5 second
        }

        $message = "Berhasil mengirim {$successCount} notifikasi";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} gagal";
        }

        return [
            'success' => true,
            'message' => $message,
            'data' => [
                'success' => $successCount,
                'failed' => $failedCount,
                'failed_data' => $failedData
            ]
        ];
    }

    /**
     * Send OSS upload notification
     */
    public function sendOSSNotification(DataLapangan $dataLapangan): bool
    {
        try {
            return $dataLapangan->sendOSSNotification();
        } catch (\Exception $e) {
            Log::error('Failed to send OSS notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SIHALAL upload notification
     */
    public function sendSihalalUploadNotification(DataLapangan $dataLapangan): bool
    {
        try {
            return $dataLapangan->sendSihalalUploadNotification();
        } catch (\Exception $e) {
            Log::error('Failed to send Sihalal notification: ' . $e->getMessage());
            return false;
        }
    }
}
