<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupDeliveryService;
use Illuminate\Console\Command;

class WrgroupProcessOutbox extends Command
{
    protected $signature = 'wrgroup:process {--limit= : Maks. event diproses (default config wrgroup.batch_size)}';

    protected $description = 'Kirim event outbox WRGROUP yang jatuh tempo (retry + exponential backoff)';

    public function handle(WrgroupDeliveryService $delivery): int
    {
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;
        $summary = $delivery->processDue($limit);

        if ($summary['skipped']) {
            $this->line($summary['skipped']);

            return self::SUCCESS;
        }

        if ($summary['processed'] > 0) {
            $this->info(sprintf(
                'Diproses %d: terkirim %d, menunggu %d, ditolak %d, gagal %d.',
                $summary['processed'], $summary['sent'], $summary['pending'], $summary['rejected'], $summary['failed'],
            ));
        }

        return self::SUCCESS;
    }
}
