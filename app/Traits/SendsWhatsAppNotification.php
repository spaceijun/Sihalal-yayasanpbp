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
            "🔗 https://webmail.swaraningcode.com/\n\n" .
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
            "Jika terkendala, silahkan hubungi koordinator masing-masing.\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*";
    }
}
