<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupKomisiService;
use Illuminate\Console\Command;

class WrgroupKomisiSync extends Command
{
    protected $signature = 'wrgroup:komisi-sync';

    protected $description = 'Sinkronkan status verifikasi setoran komisi dari WRGROUP dan bukukan yang terverifikasi ke Arus Kas';

    public function handle(WrgroupKomisiService $service): int
    {
        if (! config('wrgroup.enabled')) {
            $this->line('Integrasi WRGROUP tidak aktif — dilewati.');

            return self::SUCCESS;
        }

        $hasil = $service->sinkronVerifikasi();

        if (! $hasil['ok']) {
            $this->error('Gagal sinkron: '.$hasil['error']);

            return self::FAILURE;
        }

        $this->info("Sinkron selesai: {$hasil['diperbarui']} status diperbarui, {$hasil['dibukukan']} setoran dibukukan ke Arus Kas.");

        return self::SUCCESS;
    }
}
