<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupBackfillService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class WrgroupBackfill extends Command
{
    protected $signature = 'wrgroup:backfill
        {--since= : Tanggal mulai YYYY-MM-DD (bawaan: Tanggal Operasional pilar dari WRGROUP)}
        {--dry-run : Hanya hitung yang akan dicatat, tanpa menulis apa pun}';

    protected $description = 'Catat kasus sertifikasi lama (sejak Tanggal Operasional di WRGROUP) ke outbox agar dikirim ke WRGROUP';

    public function handle(WrgroupBackfillService $backfill): int
    {
        $since = null;
        if ($this->option('since')) {
            try {
                $since = Carbon::createFromFormat('!Y-m-d', (string) $this->option('since'));
            } catch (\Throwable) {
                $this->error('Format --since harus YYYY-MM-DD.');

                return self::FAILURE;
            }
        }

        $result = $backfill->run($since, (bool) $this->option('dry-run'));

        if ($result['skipped']) {
            $this->line($result['skipped']);

            return self::SUCCESS;
        }

        $this->info(($result['dry_run'] ? '[DRY-RUN] ' : '')."Backfill data sejak {$result['since']}:");
        $this->table(
            ['Sumber', 'Dokumen ditemukan', $result['dry_run'] ? 'Dokumen belum pernah dicatat' : 'Baris outbox baru'],
            [
                ['Kasus sertifikasi lunas (invoice + pembayaran)', $result['kasus']['kandidat'], $result['kasus']['dicatat']],
            ],
        );
        $this->line('Laporan nihil: '.($result['nihil'] ? implode(', ', $result['nihil']) : '-'));

        if (! $result['dry_run']) {
            $this->line('Pengiriman dilakukan bertahap oleh `wrgroup:process` (scheduler tiap menit).');
        }

        return self::SUCCESS;
    }
}
