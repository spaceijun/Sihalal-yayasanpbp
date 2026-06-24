<?php

namespace App\Console\Commands;

use App\Models\Enumerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEnumeratorActivity extends Command
{
    protected $signature = 'enumerator:check-activity';

    protected $description = 'Nonaktifkan enumerator yang tidak memenuhi target 20 data lapangan dalam periode 25 s.d. 25 tiap bulannya';

    public function handle(): int
    {
        $now = Carbon::now();

        // Hanya jalankan logika pada tanggal 25
        if ($now->day !== 25) {
            $this->info('Hari ini bukan tanggal 25. Perintah tidak dijalankan.');

            return Command::SUCCESS;
        }

        $this->info('Tanggal 25 — Memeriksa aktivitas enumerator...');

        // Rentang periode: tgl 25 bulan lalu s.d. tgl 25 bulan ini
        $periodStart = $now->copy()->subMonthNoOverflow()->setDay(25)->startOfDay();
        $periodEnd = $now->copy()->setDay(25)->endOfDay();

        // Ambil semua enumerator yang masih Aktif, eager-load aktivasiLogs
        $enumerators = Enumerator::where('status', 'Aktif')->get();

        $deactivated = 0;
        $skipped = 0;

        foreach ($enumerators as $enumerator) {
            $joinedAt = Carbon::parse($enumerator->created_at);

            // ① Skip enumerator yang baru bergabung bulan ini
            if ($joinedAt->isSameMonth($now) && $joinedAt->isSameYear($now)) {
                $this->line(
                    "Dilewati (baru bergabung bulan ini): [{$enumerator->no_registrasi}] {$enumerator->nama_lengkap}"
                );
                $skipped++;

                continue;
            }

            // ② Skip enumerator yang baru diaktifkan kembali bulan ini
            //    (grace period: baru diaktifkan → cek mulai bulan depan)
            $recentlyReactivated = $enumerator->aktivasiLogs()
                ->whereYear('tanggal_aktivasi', $now->year)
                ->whereMonth('tanggal_aktivasi', $now->month)
                ->exists();

            if ($recentlyReactivated) {
                $this->line(
                    "Dilewati (diaktifkan kembali bulan ini): [{$enumerator->no_registrasi}] {$enumerator->nama_lengkap}"
                );
                $skipped++;

                continue;
            }

            // Hitung data lapangan dalam periode 25 bulan lalu s.d. 25 bulan ini
            $jumlahData = $enumerator->dataLapangans()
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->count();

            if ($jumlahData < 20) {
                $enumerator->update(['status' => 'Tidak Aktif']);

                $this->warn(
                    "Dinonaktifkan: [{$enumerator->no_registrasi}] {$enumerator->nama_lengkap} ".
                    "— {$jumlahData} data (periode {$periodStart->format('d M')} s.d. {$periodEnd->format('d M Y')})"
                );

                $deactivated++;
            }
        }

        $this->info(
            "Selesai. Dinonaktifkan: {$deactivated} | ".
            "Dilewati (baru gabung / baru diaktifkan): {$skipped} enumerator."
        );

        return Command::SUCCESS;
    }
}
