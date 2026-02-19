<?php

namespace App\Traits;

use App\Services\FonnteService;
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

            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            $emailUsername = str_replace(' ', '', strtolower($this->nama_pu));
            $message = $this->formatOSSMessage($this->nama_pu, $emailUsername);

            $fonnteService = app(FonnteService::class);
            $deviceToken = env('DEVICE_TOKEN');

            if (!$deviceToken) {
                return false;
            }

            $response = $fonnteService->sendWhatsAppMessage(
                $phoneNumber,
                $message,
                $deviceToken
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
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
            $message = $this->formatRevisiMessage(
                $this->enumerator->nama_lengkap,
                $this->nama_pu,
                $this->keterangan
            );

            $fonnteService = app(FonnteService::class);
            $deviceToken = env('DEVICE_TOKEN');

            if (!$deviceToken) {
                return false;
            }

            $response = $fonnteService->sendWhatsAppMessage(
                $phoneNumber,
                $message,
                $deviceToken
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
            $fileUrl = asset('storage/' . $this->file_oss);
            $message = $this->formatOSSUploadMessage($this->nama_pu, $fileUrl);

            $fonnteService = app(FonnteService::class);
            $deviceToken = env('DEVICE_TOKEN');

            if (!$deviceToken) {
                return false;
            }

            $response = $fonnteService->sendWhatsAppMessage(
                $phoneNumber,
                $message,
                $deviceToken
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
            $fileUrl = asset('storage/' . $this->file_sihalal);
            $message = $this->formatSihalalUploadMessage($this->nama_pu, $fileUrl);

            $fonnteService = app(FonnteService::class);
            $deviceToken = env('DEVICE_TOKEN');

            if (!$deviceToken) {
                return false;
            }

            $response = $fonnteService->sendWhatsAppMessage(
                $phoneNumber,
                $message,
                $deviceToken
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending Sihalal upload notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor telepon ke format internasional
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Format template pesan WhatsApp untuk OSS
     *
     * @param string $namaPU
     * @param string $emailUsername
     * @return string
     */
    protected function formatOSSMessage(string $namaPU, string $emailUsername): string
    {
        return "Halo {$namaPU}!\n\n" .
            "NIB kamu sudah terbit nih! Selanjutnya silahkan pengajuan ke sertifikasi halal ya!\n\n" .
            "Data Email PU :\n" .
            "📧 email : *Bisa Dilihat Di File NIB.*\n" .
            "🔐 password : Halal@123\n\n" .
            "Link Login Email: \n" .
            "🔗 https://webmail.kawulohalal.id/\n\n" .
            "Link Pengajuan Sertifikasi :\n" .
            "🔗 https://ptsp.halal.go.id/register\n\n\n" .
            "Note: *Untuk membuat akun pengajuan di PTSP Halal dimohon untuk disamakan passwordnya.*\n\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    /**
     * Format template pesan WhatsApp untuk revisi
     *
     * @param string $namaPendamping
     * @param string $namaPU
     * @param string $revisi
     * @return string
     */
    protected function formatRevisiMessage(string $namaPendamping, string $namaPU, string $revisi): string
    {
        return "🔔 *NOTIFIKASI REVISI DATA*\n\n" .
            "Nama Pendamping : *{$namaPendamping}*\n" .
            "Nama PU : *{$namaPU}*\n" .
            "Revisi : \n{$revisi}\n" .
            "*Mohon segera diperbaiki ya!*\n\n" .
            "Jika terkendala, silahkan hubungi :\n" . "+62 897-6774-482\n" . "Customer Service\n\n" . "_Dikirim otomatis oleh sistem.\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    /**
     * Format template pesan WhatsApp untuk upload file OSS
     *
     * @param string $namaPU
     * @param string $fileUrl
     * @return string
     */
    protected function formatOSSUploadMessage(string $namaPU, string $fileUrl): string
    {
        return "📄 *SELAMAT! NIB TELAH TERBIT*\n\n" .
            "Halo *{$namaPU}*!\n\n" .
            "File NIB Anda telah Terbit.\n\n" .
            "📥 Download File OSS:\n" .
            "🔗 {$fileUrl}\n\n" .
            "Silahkan download dan simpan file NIB Anda, jika ada pertanyaan lebih lanjut hubungi :\n" .
            "+62 897-6774-482\n" .
            "Customer Service\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    /**
     * Format template pesan WhatsApp untuk upload file Sertifikat Halal
     *
     * @param string $namaPU
     * @param string $fileUrl
     * @return string
     */
    protected function formatSihalalUploadMessage(string $namaPU, string $fileUrl): string
    {
        return "🎉 *SELAMAT! SERTIFIKAT HALAL TERBIT*\n\n" .
            "Halo *{$namaPU}*!\n\n" .
            "Sertifikat Halal Anda telah terbit.\n\n" .
            "📥 Download Sertifikat Halal:\n" .
            "🔗 {$fileUrl}\n\n" .
            "*Selamat! Produk Anda kini telah tersertifikasi halal.*🎉 \n\n" .
            "Silahkan download dan simpan sertifikat Anda. Jika ada pertanyaan lebih lanjut hubungi :\n" .
            "+62 897-6774-482\n" .
            "Customer Service\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }

    /**
     * Kirim notifikasi WhatsApp untuk pembayaran enumerator
     *
     * @return bool
     */
    public function sendPembayaranNotificationToEnumerator(): bool
    {
        try {
            // Load relasi enumerator
            $this->load('enumerator.koordinator');

            $phoneNumber = $this->enumerator->telephone ?? null;

            if (!$phoneNumber) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            $message = $this->formatPembayaranMessage(
                $this->enumerator->nama_lengkap,
                $this->nama_pu,
                $this->nik,
                $this->enumerator->koordinator->fee_enum
            );

            $fonnteService = app(FonnteService::class);
            $deviceToken = env('DEVICE_TOKEN');

            if (!$deviceToken) {
                return false;
            }

            $response = $fonnteService->sendWhatsAppMessage(
                $phoneNumber,
                $message,
                $deviceToken
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending pembayaran notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format template pesan WhatsApp untuk pembayaran enumerator
     *
     * @param string $namaEnumerator
     * @param string $namaPU
     * @param string $nik
     * @param int $nominal
     * @return string
     */
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

    /**
     * Kirim notifikasi WhatsApp untuk pembayaran data entry
     *
     * @param string $namaDataEntry
     * @param string $telephone
     * @param int $jumlahData
     * @param int $jumlahPaket
     * @param int $nominal
     * @return bool
     */
    public function sendPembayaranDataEntryNotification(
        string $namaDataEntry,
        string $telephone,
        int $jumlahData,
        int $jumlahPaket,
        int $nominal
    ): bool {
        try {
            if (!$telephone) {
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($telephone);
            $message     = $this->formatPembayaranDataEntryMessage(
                $namaDataEntry,
                $jumlahData,
                $jumlahPaket,
                $nominal
            );

            $fonnteService = app(FonnteService::class);
            $deviceToken   = env('DEVICE_TOKEN');

            if (!$deviceToken) {
                return false;
            }

            $response = $fonnteService->sendWhatsAppMessage(
                $phoneNumber,
                $message,
                $deviceToken
            );

            return $response['status'] ?? false;
        } catch (\Exception $e) {
            Log::error('Error sending pembayaran data entry notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format template pesan WhatsApp untuk pembayaran data entry
     *
     * @param string $namaDataEntry
     * @param int $jumlahData
     * @param int $jumlahPaket
     * @param int $nominal
     * @return string
     */
    protected function formatPembayaranDataEntryMessage(
        string $namaDataEntry,
        int $jumlahData,
        int $jumlahPaket,
        int $nominal
    ): string {
        $nominalFormatted = 'Rp ' . number_format($nominal, 0, ',', '.');
        $perPaket         = 'Rp ' . number_format(100000, 0, ',', '.');

        return "💰 *NOTIFIKASI PEMBAYARAN DATA ENTRY*\n\n" .
            "Halo *{$namaDataEntry}*!\n\n" .
            "Pembayaran untuk Data Entry Anda telah disetujui dan sedang diproses.\n\n" .
            "📊 *Detail Pembayaran:*\n" .
            "• Jumlah Data  : *{$jumlahData} data*\n" .
            "• Jumlah Paket : *{$jumlahPaket} paket*\n" .
            "• Tarif/Paket  : *{$perPaket}*\n" .
            "• Total        : *{$nominalFormatted}*\n\n" .
            "✅ *Pembayaran telah dikonfirmasi.*\n\n" .
            "Customer Service\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL* \n" .
            "+62 897-6774-482";
    }
}
