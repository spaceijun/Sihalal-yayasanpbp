<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupSuratService;
use Illuminate\Console\Command;

class WrgroupSuratSync extends Command
{
    protected $signature = 'wrgroup:surat-sync';

    protected $description = 'Sinkronkan status surat yang diajukan ke WRGROUP dan unduh PDF saat terbit';

    public function handle(WrgroupSuratService $service): int
    {
        if (! config('wrgroup.enabled')) {
            $this->line('Integrasi WRGROUP tidak aktif — dilewati.');

            return self::SUCCESS;
        }

        $jumlah = $service->sinkronStatus();
        $this->info("Sinkron selesai: {$jumlah} surat diperbarui.");

        return self::SUCCESS;
    }
}
