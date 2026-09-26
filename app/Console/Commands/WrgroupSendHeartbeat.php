<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupDeliveryService;
use Illuminate\Console\Command;

class WrgroupSendHeartbeat extends Command
{
    protected $signature = 'wrgroup:heartbeat';

    protected $description = 'Kirim heartbeat ke WRGROUP Super Apps (status koneksi pilar)';

    public function handle(WrgroupDeliveryService $delivery): int
    {
        $result = $delivery->heartbeat();

        if ($result['ok']) {
            $this->info($result['message']);

            return self::SUCCESS;
        }

        // Tanpa kode HTTP = integrasi nonaktif / belum dikonfigurasi: bukan kegagalan scheduler.
        if ($result['http'] === null) {
            $this->line($result['message']);

            return self::SUCCESS;
        }

        $this->error($result['message']);

        return self::FAILURE;
    }
}
