<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupEventService;
use App\Services\Integrasi\Wrgroup\WrgroupPayloadBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class WrgroupReportNihil extends Command
{
    protected $signature = 'wrgroup:nihil {period? : Triwulan YYYY-Qn (default: triwulan sebelumnya)}';

    protected $description = 'Laporkan triwulan tanpa kasus sertifikasi lunas (laporan nihil) ke WRGROUP';

    public function handle(WrgroupEventService $events, WrgroupPayloadBuilder $builder): int
    {
        $explicit = $this->argument('period');
        $period = $explicit ?: $builder->previousPeriod();

        // Otomatis: hanya triwulan yang dimulai pada/setelah pilar aktif di WRGROUP.
        $start = config('wrgroup.start_date');
        if (! $explicit && $start && $period < $builder->period(Carbon::parse($start))) {
            $this->line("Triwulan {$period} sebelum tanggal aktif WRGROUP ({$start}) — dilewati.");

            return self::SUCCESS;
        }

        $result = $events->reportNihil($period);

        // Tidak diminta eksplisit: kondisi "belum saatnya / ada transaksi / sudah tercatat" normal.
        if (! $result['ok'] && ! $explicit) {
            $this->line($result['message']);

            return self::SUCCESS;
        }

        $result['ok'] ? $this->info($result['message']) : $this->error($result['message']);

        return $result['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
