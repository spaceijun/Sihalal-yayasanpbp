<?php

namespace App\Console\Commands;

use App\Models\DataLapangan;
use App\Services\Integrasi\UrusinService;
use App\Services\Integrasi\UrusinSubmissionService;
use Illuminate\Console\Command;

/**
 * Polling terjadwal untuk sinkronisasi status NIB & Sertifikat Halal dari Urusin Secara Online.
 *
 * Dokumentasi API yang diberikan hanya menyediakan endpoint GET status (tidak ada spek webhook
 * masuk) — lihat §5.4 .agent/workflows/data-entry-integrasi.md — jadi polling adalah satu-satunya
 * cara mengetahui kapan dokumen NIB/Sertifikat Halal sudah terbit.
 *
 * Sejak Bundle API (§3.7 data-entry-integrasi.md), status NIB+Halal dicek sekaligus lewat SATU
 * panggilan `GET /bundle/{nib_submission_id}/status` (UrusinSubmissionService::syncBundle()) —
 * bukan dua panggilan terpisah seperti desain awal. Record lama yang sempat disubmit terpisah
 * (tidak punya urusin_nib_submission_id tapi punya urusin_halal_submission_id) tetap disinkronkan
 * lewat syncHalal() lama sebagai fallback.
 *
 * Dijadwalkan lewat routes/console.php (pola Schedule::call()+Artisan::call(), bukan
 * Schedule::command(), mengikuti konvensi command lain di app ini agar tidak bergantung pada
 * proc_open yang sering dinonaktifkan di shared hosting).
 */
class SyncUrusinSubmissions extends Command
{
    protected $signature = 'urusin:sync';

    protected $description = 'Sinkronisasi status & unduh dokumen NIB/Sertifikat Halal yang sudah terbit dari Urusin Secara Online';

    public function handle(UrusinService $urusinService, UrusinSubmissionService $submissionService): int
    {
        if (! $urusinService->isConfigured()) {
            $this->warn('Urusin Secara Online belum dikonfigurasi (Superadmin > Setting Website > API Keys). Sync dilewati.');

            return self::SUCCESS;
        }

        $doneStatuses = ['completed', 'failed', 'rejected', 'gagal_kirim'];

        // Jalur utama: record dengan urusin_nib_submission_id (hasil submitBundle()) yang salah
        // satu jenisnya masih berjalan — dicek sekaligus lewat GET /bundle/{nib_id}/status.
        $pendingBundle = DataLapangan::whereNotNull('urusin_nib_submission_id')
            ->where(function ($q) use ($doneStatuses) {
                $q->whereNotIn('urusin_nib_status', $doneStatuses)
                    ->orWhereNull('urusin_halal_submission_id')
                    ->orWhereNotIn('urusin_halal_status', $doneStatuses);
            })
            ->get();

        foreach ($pendingBundle as $dataLapangan) {
            $submissionService->syncBundle($dataLapangan);
        }

        // Fallback: record lama yang punya Halal submission_id tapi TIDAK punya NIB submission_id
        // (disubmit terpisah sebelum Bundle API dipakai) — tidak bisa dicek lewat bundle status.
        $pendingHalalOnly = DataLapangan::whereNull('urusin_nib_submission_id')
            ->whereNotNull('urusin_halal_submission_id')
            ->whereNotIn('urusin_halal_status', $doneStatuses)
            ->get();

        foreach ($pendingHalalOnly as $dataLapangan) {
            $submissionService->syncHalal($dataLapangan);
        }

        $this->info("Sync selesai — Bundle dicek: {$pendingBundle->count()}, Halal-saja (fallback) dicek: {$pendingHalalOnly->count()}.");

        return self::SUCCESS;
    }
}
