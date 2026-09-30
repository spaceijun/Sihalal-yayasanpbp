<?php

namespace App\Console\Commands;

use App\Services\Integrasi\Wrgroup\WrgroupEventService;
use App\Services\Integrasi\Wrgroup\WrgroupPayloadBuilder;
use App\Services\Superadmin\CashflowService;
use Illuminate\Console\Command;

/**
 * Laporkan pendapatan bersih (Pemasukan − Pengeluaran, ledger Arus Kas) sebagai dasar komisi/CSR KH
 * di WRGROUP — pengganti invoice per-kasus (lihat WrgroupEventService::reportPendapatanBersih() dan
 * .agent/workflows/wrgroup-integrasi.md § Pendapatan Bersih).
 */
class WrgroupReportPendapatanBersih extends Command
{
    protected $signature = 'wrgroup:pendapatan-bersih {period? : Triwulan YYYY-Qn (default: triwulan berjalan & sebelumnya)}';

    protected $description = 'Laporkan pendapatan bersih ledger Arus Kas satu triwulan sebagai dasar komisi WRGROUP';

    public function handle(WrgroupEventService $events, WrgroupPayloadBuilder $builder, CashflowService $cashflow): int
    {
        $explicit = $this->argument('period');

        if ($explicit) {
            return $this->laporkan($explicit, true, $events, $builder, $cashflow) ? self::SUCCESS : self::FAILURE;
        }

        // Tanpa argumen (jadwal terjadwal): triwulan berjalan (koreksi berkala seiring entri Arus Kas
        // baru) DAN triwulan sebelumnya (masih bisa dikoreksi selama belum ditagihkan WRGROUP).
        $sekarang = $builder->period($builder->now());
        $sebelumnya = $builder->previousPeriod();

        $okSekarang = $this->laporkan($sekarang, false, $events, $builder, $cashflow);
        $okSebelumnya = $this->laporkan($sebelumnya, false, $events, $builder, $cashflow);

        return ($okSekarang && $okSebelumnya) ? self::SUCCESS : self::FAILURE;
    }

    private function laporkan(string $period, bool $eksplisit, WrgroupEventService $events, WrgroupPayloadBuilder $builder, CashflowService $cashflow): bool
    {
        [$mulai, $selesai] = $builder->periodRange($period);
        $nilai = $cashflow->netPeriode($mulai, $selesai);

        $result = $events->reportPendapatanBersih($period, $nilai);

        // Tidak diminta eksplisit: "sudah ditagihkan / tidak ada perubahan / belum saatnya" normal.
        if (! $result['ok'] && ! $eksplisit) {
            $this->line($result['message']);

            return true;
        }

        $result['ok'] ? $this->info($result['message']) : $this->error($result['message']);

        return $result['ok'];
    }
}
