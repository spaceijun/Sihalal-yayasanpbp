<?php

namespace App\Services\Superadmin;

use App\Models\DataEntry;
use App\Models\DataLapangan;
use App\Services\KawuloHalalService;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private KawuloHalalService $kawuloHalal
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Kirim notifikasi revisi untuk satu data lapangan.
     */
    public function sendRevisiNotification(DataLapangan $dataLapangan): array
    {
        if (!$dataLapangan->keterangan) {
            return $this->failure('Data tidak memiliki keterangan revisi');
        }

        $dataLapangan->loadMissing('enumerator');
        $phone = $this->resolvePhone($dataLapangan->enumerator?->telephone);

        if (!$phone) {
            return $this->failure('Nomor telepon enumerator tidak tersedia');
        }

        $caption = $this->buildRevisiCaption(
            $dataLapangan->enumerator->nama_lengkap,
            $dataLapangan->nama_pu,
            $dataLapangan->keterangan
        );

        $response = $this->kawuloHalal->sendText($phone, $caption);
        $sent     = $response['status'] ?? false;

        return $sent
            ? $this->success('Notifikasi berhasil dikirim ke ' . $dataLapangan->enumerator->nama_lengkap)
            : $this->failure('Gagal mengirim notifikasi');
    }

    /**
     * Kirim notifikasi revisi untuk semua data lapangan yang memiliki keterangan.
     */
    public function sendAllRevisiNotifications(): array
    {
        $dataLapangans = DataLapangan::with('enumerator')
            ->whereNotNull('keterangan')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return $this->failure('Tidak ada data revisi yang ditemukan', [
                'success' => 0,
                'failed' => 0,
                'failed_data' => [],
            ]);
        }

        $successCount = 0;
        $failedCount  = 0;
        $failedData   = [];

        foreach ($dataLapangans as $dataLapangan) {
            $result = $this->sendRevisiNotification($dataLapangan);

            if ($result['success']) {
                $successCount++;
            } else {
                $failedCount++;
                $failedData[] = $dataLapangan->nama_pu;
                Log::warning('NotificationService: revisi gagal', [
                    'nama_pu' => $dataLapangan->nama_pu,
                    'reason'  => $result['message'],
                ]);
            }

            usleep(500_000); // 0.5 detik — hindari rate limit
        }

        $message = "Berhasil mengirim {$successCount} notifikasi";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} gagal";
        }

        return [
            'success' => true,
            'message' => $message,
            'data'    => [
                'success'     => $successCount,
                'failed'      => $failedCount,
                'failed_data' => $failedData,
            ],
        ];
    }

    /**
     * Kirim notifikasi OSS terbit ke koordinator enumerator.
     */
    public function sendOSSNotification(DataLapangan $dataLapangan): bool
    {
        try {
            $dataLapangan->loadMissing('enumerator.koordinator');
            $phone = $this->resolvePhone($dataLapangan->enumerator?->koordinator?->telephone);

            if (!$phone) {
                Log::warning('NotificationService: nomor koordinator tidak tersedia', [
                    'data_lapangan_id' => $dataLapangan->id,
                ]);
                return false;
            }

            $emailUsername = str_replace(' ', '', strtolower($dataLapangan->nama_pu));
            $message       = $this->buildOSSCaption($dataLapangan->nama_pu, $emailUsername);

            $response = $this->kawuloHalal->sendText(
                number: $phone,
                message: $message,
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('NotificationService: OSS notification error', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kirim notifikasi sertifikat halal terbit beserta file ke PU.
     */
    public function sendSihalalUploadNotification(DataLapangan $dataLapangan): bool
    {
        try {
            $phone = $this->resolvePhone($dataLapangan->telephone);

            if (!$phone || !$dataLapangan->file_sihalal) {
                Log::warning('NotificationService: data sihalal tidak lengkap', [
                    'data_lapangan_id' => $dataLapangan->id,
                    'has_phone'        => (bool) $dataLapangan->telephone,
                    'has_file'         => (bool) $dataLapangan->file_sihalal,
                ]);
                return false;
            }

            $fileUrl = asset('storage/' . $dataLapangan->file_sihalal);
            $caption = $this->buildSihalalCaption($dataLapangan->nama_pu);

            $response = $this->kawuloHalal->sendMedia(
                number: $phone,
                mediaType: 'document',
                url: $fileUrl,
                caption: $caption,
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('NotificationService: Sihalal notification error', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kirim notifikasi file OSS (NIB) beserta link download ke PU.
     */
    public function sendOSSUploadNotification(DataLapangan $dataLapangan): bool
    {
        try {
            $phone = $this->resolvePhone($dataLapangan->telephone);

            if (!$phone || !$dataLapangan->file_oss) {
                Log::warning('NotificationService: data OSS upload tidak lengkap', [
                    'data_lapangan_id' => $dataLapangan->id,
                ]);
                return false;
            }

            $fileUrl = asset('storage/' . $dataLapangan->file_oss);
            $caption = $this->buildOSSUploadCaption($dataLapangan->nama_pu);

            $response = $this->kawuloHalal->sendMedia(
                number: $phone,
                mediaType: 'document',
                url: $fileUrl,
                caption: $caption,
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('NotificationService: OSS upload notification error', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kirim notifikasi revisi data entry ke nomor telephone di tabel data_entrys.
     */
    public function sendDataEntryRevisiNotification(
        DataEntry $dataEntry,
        string $namaPU,
        string $keteranganRevisi,
    ): bool {
        try {
            $phone = $this->resolvePhone($dataEntry->telephone);

            if (!$phone) {
                Log::warning('NotificationService: telephone data entry tidak tersedia', [
                    'data_entry_id' => $dataEntry->id,
                ]);
                return false;
            }

            $caption = $this->buildDataEntryRevisiCaption(
                namaPU: $namaPU,
                entryType: $dataEntry->entry_type ?? '-',
                keterangan: $keteranganRevisi,
            );

            $response = $this->kawuloHalal->sendText($phone, $caption);

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('NotificationService: data entry revisi notification error', [
                'data_entry_id' => $dataEntry->id,
                'message'       => $e->getMessage(),
            ]);
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Format nomor telepon ke format internasional (62xxx).
     * Kembalikan null jika nomor kosong atau tidak valid.
     */
    private function resolvePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        // Nomor WhatsApp Indonesia minimal 10 digit (62 + 8 digit)
        // maksimal 15 digit sesuai standar E.164
        $digitCount = strlen($phone);
        if ($digitCount < 10 || $digitCount > 15) {
            Log::warning('NotificationService: nomor telepon tidak valid', [
                'raw'    => $phone,
                'length' => $digitCount,
            ]);
            return null;
        }

        return $phone;
    }

    private function success(string $message, array $data = []): array
    {
        $result = ['success' => true, 'message' => $message];
        if (!empty($data)) {
            $result['data'] = $data;
        }
        return $result;
    }

    private function failure(string $message, array $data = []): array
    {
        $result = ['success' => false, 'message' => $message];
        if (!empty($data)) {
            $result['data'] = $data;
        }
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Message Templates
    // ─────────────────────────────────────────────────────────────────────────

    private function buildRevisiCaption(string $namaPendamping, string $namaPU, string $revisi): string
    {
        return "🔔 *NOTIFIKASI REVISI DATA*\n\n" .
            "Nama Pendamping : *{$namaPendamping}*\n" .
            "Nama PU : *{$namaPU}*\n" .
            "Revisi :\n{$revisi}\n\n" .
            "*Mohon segera diperbaiki ya!*\n\n" .
            "Jika terkendala, silahkan hubungi :\n" .
            "+62 897-6774-482 (Customer Service)\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n*TIM KAWULO HALAL*";
    }

    private function buildOSSCaption(string $namaPU, string $emailUsername): string
    {
        return "Halo {$namaPU}!\n\n" .
            "NIB kamu sudah terbit nih! Selanjutnya silahkan pengajuan ke sertifikasi halal ya!\n\n" .
            "Data Email PU :\n" .
            "📧 email : *Bisa Dilihat Di File NIB.*\n" .
            "🔐 password : Halal@123\n\n" .
            "Link Login Email:\n🔗 https://webmail.kawulohalal.id/\n\n" .
            "Link Pengajuan Sertifikasi:\n🔗 https://ptsp.halal.go.id/register\n\n" .
            "Note: *Untuk membuat akun pengajuan di PTSP Halal dimohon untuk disamakan passwordnya.*\n\n" .
            "Best Regards,\n*TIM KAWULO HALAL*";
    }

    private function buildOSSUploadCaption(string $namaPU): string
    {
        return "📄 *SELAMAT! NIB TELAH TERBIT*\n\n" .
            "Halo *{$namaPU}*!\n\n" .
            "File NIB Anda telah Terbit. Silahkan download dan simpan file NIB Anda.\n\n" .
            "Jika ada pertanyaan lebih lanjut hubungi :\n" .
            "+62 897-6774-482 (Customer Service)\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n*TIM KAWULO HALAL*";
    }

    private function buildSihalalCaption(string $namaPU): string
    {
        return "🎉 *SELAMAT! SERTIFIKAT HALAL TERBIT*\n\n" .
            "Halo *{$namaPU}*!\n\n" .
            "Sertifikat Halal Anda telah terbit. Silahkan download dan simpan sertifikat Anda.\n\n" .
            "*Selamat! Produk Anda kini telah tersertifikasi halal.*🎉\n\n" .
            "Jika ada pertanyaan lebih lanjut hubungi :\n" .
            "+62 897-6774-482 (Customer Service)\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n*TIM KAWULO HALAL*";
    }


    private function buildDataEntryRevisiCaption(
        string $namaPU,
        string $entryType,
        string $keterangan,
    ): string {
        return "🔔 *REVISI DATA ENTRY*\n\n" .
            "Nama PU      : *{$namaPU}*\n" .
            "Entry Type   : *{$entryType}*\n" .
            "Keterangan   :\n{$keterangan}\n\n" .
            "Best Regards,\n*TIM KAWULO HALAL*";
    }
}
