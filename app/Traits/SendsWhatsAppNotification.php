<?php

namespace App\Traits;

use App\Services\KawuloHalalService;
use Illuminate\Support\Facades\Log;

trait SendsWhatsAppNotification
{
    /**
     * Kirim notifikasi WhatsApp untuk file OSS
     *
     * @return bool
     */
    public function sendOSSNotification(): bool
    {
        try {
            $this->load('enumerator.koordinator');
            $phoneNumber = $this->enumerator->koordinator->telephone ?? null;

            if (!$phoneNumber) {
                return false;
            }

            $phoneNumber    = $this->formatPhoneNumber($phoneNumber);
            $emailUsername  = str_replace(' ', '', strtolower($this->nama_pu));
            $caption        = $this->formatOSSMessage($this->nama_pu, $emailUsername);

            /** @var KawuloHalalService $service */
            $service  = app(KawuloHalalService::class);
            $mediaUrl = env('KAWULOHALAL_DEFAULT_MEDIA_URL', 'https://kawulohalal.id/assets/logo.png');

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'image',
                url: $mediaUrl,
                caption: $caption,
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending OSS notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk data revisi
     *
     * @return bool
     */
    public function sendRevisiNotification(): bool
    {
        try {
            $this->load('enumerator');
            $phoneNumber = $this->enumerator->telephone ?? null;

            if (!$phoneNumber) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            $caption     = $this->formatRevisiMessage(
                $this->enumerator->nama_lengkap,
                $this->nama_pu,
                $this->keterangan
            );

            /** @var KawuloHalalService $service */
            $service  = app(KawuloHalalService::class);
            $mediaUrl = env('KAWULOHALAL_DEFAULT_MEDIA_URL', 'https://kawulohalal.id/assets/logo.png');

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'image',
                url: $mediaUrl,
                caption: $caption,
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending revisi notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk upload file OSS dengan link download
     *
     * @return bool
     */
    public function sendOSSUploadNotification(): bool
    {
        try {
            $phoneNumber = $this->telephone ?? null;

            if (!$phoneNumber || !$this->file_oss) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            $fileUrl     = asset('storage/' . $this->file_oss);
            $caption     = $this->formatOSSUploadMessage($this->nama_pu, $fileUrl);

            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'document',
                url: $fileUrl,
                caption: $caption,
                footer: 'TIM KAWULO HALAL | +62 897-6774-482',
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending OSS upload notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk upload file Sertifikat Halal dengan link download
     *
     * @return bool
     */
    public function sendSihalalUploadNotification(): bool
    {
        try {
            $phoneNumber = $this->telephone ?? null;

            if (!$phoneNumber || !$this->file_sihalal) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            $fileUrl     = asset('storage/' . $this->file_sihalal);
            $caption     = $this->formatSihalalUploadMessage($this->nama_pu, $fileUrl);

            /** @var KawuloHalalService $service */
            $service = app(KawuloHalalService::class);

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'document',
                url: $fileUrl,
                caption: $caption,
                footer: 'TIM KAWULO HALAL | +62 897-6774-482',
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending Sihalal upload notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk pembayaran enumerator
     *
     * @return bool
     */
    public function sendPembayaranNotificationToEnumerator(): bool
    {
        try {
            $this->load('enumerator.koordinator');
            $phoneNumber = $this->enumerator->telephone ?? null;

            if (!$phoneNumber) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            $caption     = $this->formatPembayaranMessage(
                $this->enumerator->nama_lengkap,
                $this->nama_pu,
                $this->nik,
                $this->enumerator->koordinator->fee_enum
            );

            /** @var KawuloHalalService $service */
            $service  = app(KawuloHalalService::class);
            $mediaUrl = env('KAWULOHALAL_DEFAULT_MEDIA_URL', 'https://kawulohalal.id/assets/logo.png');

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'image',
                url: $mediaUrl,
                caption: $caption,
                footer: 'TIM KAWULO HALAL | +62 897-6774-482',
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending pembayaran notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk pembayaran data entry
     *
     * @param string $namaDataEntry
     * @param string $telephone
     * @param int    $jumlahData
     * @param int    $jumlahPaket
     * @param int    $nominal
     * @return bool
     */
    public function sendPembayaranDataEntryNotification(
        string $namaDataEntry,
        string $telephone,
        int    $jumlahData,
        int    $jumlahPaket,
        int    $nominal
    ): bool {
        try {
            if (!$telephone) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($telephone);
            $caption     = $this->formatPembayaranDataEntryMessage(
                $namaDataEntry,
                $jumlahData,
                $jumlahPaket,
                $nominal
            );

            /** @var KawuloHalalService $service */
            $service  = app(KawuloHalalService::class);
            $mediaUrl = env('KAWULOHALAL_DEFAULT_MEDIA_URL', 'https://kawulohalal.id/assets/logo.png');

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'image',
                url: $mediaUrl,
                caption: $caption,
                footer: 'TIM KAWULO HALAL | +62 897-6774-482',
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending pembayaran data entry notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi WhatsApp untuk pembayaran verifikator
     *
     * @param string $namaVerifikator
     * @param string $telephone
     * @param int    $jumlahData
     * @param float  $ratePerData
     * @param float  $totalNominal
     * @return bool
     */
    public function sendPembayaranVerifikatorNotification(
        string $namaVerifikator,
        string $telephone,
        int    $jumlahData,
        float  $ratePerData,
        float  $totalNominal
    ): bool {
        try {
            if (!$telephone) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($telephone);
            $caption     = $this->formatPembayaranVerifikatorMessage(
                $namaVerifikator,
                $jumlahData,
                $ratePerData,
                $totalNominal
            );

            /** @var KawuloHalalService $service */
            $service  = app(KawuloHalalService::class);
            $mediaUrl = env('KAWULOHALAL_DEFAULT_MEDIA_URL', 'https://kawulohalal.id/assets/logo.png');

            $response = $service->sendMedia(
                number: $phoneNumber,
                mediaType: 'image',
                url: $mediaUrl,
                caption: $caption,
                footer: 'TIM KAWULO HALAL | +62 897-6774-482',
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending pembayaran verifikator notification: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Format nomor telepon ke format internasional (62xxx)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Message Templates
    // ─────────────────────────────────────────────────────────────────────────

    protected function formatOSSMessage(string $namaPU, string $emailUsername): string
    {
        return "Halo {$namaPU}!\n\n" .
            "NIB kamu sudah terbit nih! Selanjutnya silahkan pengajuan ke sertifikasi halal ya!\n\n" .
            "Data Email PU :\n" .
            "📧 email : *Bisa Dilihat Di File NIB.*\n" .
            "🔐 password : Halal@123\n\n" .
            "Link Login Email:\n" .
            "🔗 https://webmail.kawulohalal.id/\n\n" .
            "Link Pengajuan Sertifikasi:\n" .
            "🔗 https://ptsp.halal.go.id/register\n\n" .
            "Note: *Untuk membuat akun pengajuan di PTSP Halal dimohon untuk disamakan passwordnya.*\n\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    protected function formatRevisiMessage(string $namaPendamping, string $namaPU, string $revisi): string
    {
        return "🔔 *NOTIFIKASI REVISI DATA*\n\n" .
            "Nama Pendamping : *{$namaPendamping}*\n" .
            "Nama PU : *{$namaPU}*\n" .
            "Revisi : \n{$revisi}\n" .
            "*Mohon segera diperbaiki ya!*\n\n" .
            "Jika terkendala, silahkan hubungi :\n" .
            "+62 897-6774-482\n" .
            "Customer Service\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    protected function formatOSSUploadMessage(string $namaPU, string $fileUrl): string
    {
        return "📄 *SELAMAT! NIB TELAH TERBIT*\n\n" .
            "Halo *{$namaPU}*!\n\n" .
            "File NIB Anda telah Terbit.\n\n" .
            "📥 Download File OSS:\n" .
            "🔗 {$fileUrl}\n\n" .
            "Silahkan download dan simpan file NIB Anda. Jika ada pertanyaan lebih lanjut hubungi :\n" .
            "+62 897-6774-482\n" .
            "Customer Service\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    protected function formatSihalalUploadMessage(string $namaPU, string $fileUrl): string
    {
        return "🎉 *SELAMAT! SERTIFIKAT HALAL TERBIT*\n\n" .
            "Halo *{$namaPU}*!\n\n" .
            "Sertifikat Halal Anda telah terbit.\n\n" .
            "📥 Download Sertifikat Halal:\n" .
            "🔗 {$fileUrl}\n\n" .
            "*Selamat! Produk Anda kini telah tersertifikasi halal.*🎉\n\n" .
            "Silahkan download dan simpan sertifikat Anda. Jika ada pertanyaan lebih lanjut hubungi :\n" .
            "+62 897-6774-482\n" .
            "Customer Service\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    protected function formatPembayaranMessage(string $namaEnumerator, string $namaPU, string $nik, int $nominal): string
    {
        $nominalFormatted = 'Rp ' . number_format($nominal, 0, ',', '.');

        return "*NOTIFIKASI PEMBAYARAN*\n\n" .
            "Halo *{$namaEnumerator}*!\n\n" .
            "Pembayaran atas nama *{$namaPU}* berhasil dikirim dengan nominal *{$nominalFormatted}*.\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*\n" .
            "+62 897-6774-482 (CS)";
    }

    protected function formatPembayaranDataEntryMessage(
        string $namaDataEntry,
        int    $jumlahData,
        int    $jumlahPaket,
        int    $nominal
    ): string {
        $nominalFormatted = 'Rp ' . number_format($nominal, 0, ',', '.');
        $perPaket         = 'Rp ' . number_format(150000, 0, ',', '.');

        return "💰 *NOTIFIKASI PEMBAYARAN DATA ENTRY*\n\n" .
            "Halo *{$namaDataEntry}*!\n\n" .
            "Pembayaran untuk Data Entry Anda telah disetujui dan sedang diproses.\n\n" .
            "📊 *Detail Pembayaran:*\n" .
            "• Jumlah Data  : *{$jumlahData} data*\n" .
            "• Jumlah Paket : *{$jumlahPaket} paket*\n" .
            "• Tarif/Paket  : *{$perPaket}*\n" .
            "• Total        : *{$nominalFormatted}*\n\n" .
            "✅ *Pembayaran telah dikonfirmasi.*\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*\n" .
            "+62 897-6774-482";
    }

    protected function formatPembayaranVerifikatorMessage(
        string $namaVerifikator,
        int    $jumlahData,
        float  $ratePerData,
        float  $totalNominal
    ): string {
        $rateFormatted  = 'Rp ' . number_format($ratePerData, 0, ',', '.');
        $totalFormatted = 'Rp ' . number_format($totalNominal, 0, ',', '.');

        return "💰 *NOTIFIKASI PEMBAYARAN VERIFIKATOR*\n\n" .
            "Halo *{$namaVerifikator}*!\n\n" .
            "Pembayaran atas hasil verifikasi Anda telah diproses.\n\n" .
            "📊 *Detail Pembayaran:*\n" .
            "• Jumlah Data  : *{$jumlahData} data*\n" .
            "• Rate / Data  : *{$rateFormatted}*\n" .
            "• Total        : *{$totalFormatted}*\n\n" .
            "✅ *Pembayaran telah dikonfirmasi.*\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*\n" .
            "+62 897-6774-482 (CS)";
    }
}
