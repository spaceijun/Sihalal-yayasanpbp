<?php

namespace App\Console\Commands;

use App\Models\Enumerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckEnumeratorActivity extends Command
{
    protected $signature   = 'enumerator:check-activity';
    protected $description = 'Nonaktifkan enumerator yang tidak mengajukan minimal 20 data lapangan dalam 30 hari terakhir';

    public function handle(): int
    {
        $this->info('Memeriksa aktivitas enumerator...');

        $cutoff = Carbon::now()->subDays(30);

        // Ambil semua enumerator yang masih Aktif
        $enumerators = Enumerator::where('status', 'Aktif')->get();

        $deactivated = 0;

        foreach ($enumerators as $enumerator) {
            $jumlahData = $enumerator->dataLapangans()
                ->where('created_at', '>=', $cutoff)
                ->count();

            if ($jumlahData < 20) {
                $enumerator->update(['status' => 'Tidak Aktif']);

                $this->warn(
                    "Dinonaktifkan: [{$enumerator->no_registrasi}] {$enumerator->nama_lengkap} " .
                        "— {$jumlahData} data dalam 30 hari terakhir"
                );

                $deactivated++;
            }
        }

        $this->info("Selesai. Total dinonaktifkan: {$deactivated} enumerator.");

        return Command::SUCCESS;
    }
}
