<?php

namespace App\Console\Commands;

use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Services\DataEntryPenagihanService;
use Illuminate\Console\Command;

class FixOssPenagihanMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:oss-penagihan-missing
                            {--dry-run : Hanya tampilkan data, tidak membuat tagihan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki data entry OSS yang progress-nya sudah DITERIMA ≥ 15 tapi belum ada tagihan (retroaktif fix bug Admin Umum)';

    public function __construct(protected DataEntryPenagihanService $penagihanService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('=== Fix OSS Penagihan Missing ===');
        if ($isDryRun) {
            $this->warn('[DRY-RUN] Tidak ada tagihan yang akan dibuat.');
        }
        $this->newLine();

        // Ambil semua data entry OSS
        $dataEntries = DataEntry::where('entry_type', 'OSS')
            ->whereHas('progress', function ($q) {
                $q->where('action', 'created')->where('status', 'DITERIMA');
            })
            ->with('user')
            ->get();

        if ($dataEntries->isEmpty()) {
            $this->info('Tidak ada data entry OSS dengan progress DITERIMA.');

            return self::SUCCESS;
        }

        $this->info("Ditemukan {$dataEntries->count()} data entry OSS dengan progress DITERIMA.");
        $this->newLine();

        $totalTagihanDibuat = 0;
        $totalSkip = 0;
        $tableRows = [];

        foreach ($dataEntries as $dataEntry) {
            // Cek apakah masih ada PENDING yang menunggu
            $adaPending = DataEntryProgress::where('data_entry_id', $dataEntry->id)
                ->where('action', 'created')
                ->where('status', 'PENDING')
                ->exists();

            // Hitung progress DITERIMA yang belum masuk tagihan aktif
            $progressDiterima = DataEntryProgress::where('data_entry_id', $dataEntry->id)
                ->where('action', 'created')
                ->where('status', 'DITERIMA')
                ->where(function ($query) {
                    $query->whereDoesntHave('penagihanDetails')
                        ->orWhereHas('penagihanDetails', function ($q) {
                            $q->whereHas('penagihan', function ($q2) {
                                $q2->where('status', 'Ditolak');
                            });
                        });
                })
                ->count();

            $layak = ! $adaPending && $progressDiterima >= 15;
            $jumlahPaket = (int) floor($progressDiterima / 15);

            $tableRows[] = [
                $dataEntry->id,
                $dataEntry->user?->name ?? '-',
                $progressDiterima,
                $adaPending ? 'YA' : 'Tidak',
                $layak ? "✓ Layak ({$jumlahPaket} paket)" : '✗ Skip',
            ];

            if (! $layak) {
                $totalSkip++;
                continue;
            }

            if (! $isDryRun) {
                $penagihan = $this->penagihanService->cekDanBuatTagihan($dataEntry);
                if ($penagihan) {
                    $totalTagihanDibuat++;
                    $this->line("  ✓ Tagihan dibuat untuk [{$dataEntry->user?->name}]: Rp ".number_format($penagihan->nominal, 0, ',', '.')." ({$penagihan->jumlah_paket} paket)");
                } else {
                    $this->line("  - Tidak ada tagihan baru untuk [{$dataEntry->user?->name}] (mungkin sudah ada tagihan aktif).");
                    $totalSkip++;
                }
            }
        }

        $this->table(
            ['ID', 'Nama Data Entry', 'Progress DITERIMA (belum ditagih)', 'Ada PENDING?', 'Status'],
            $tableRows
        );

        $this->newLine();

        if ($isDryRun) {
            $layakCount = collect($tableRows)->filter(fn ($r) => str_contains($r[4], 'Layak'))->count();
            $this->info("[DRY-RUN] {$layakCount} data entry OSS akan dibuatkan tagihan jika dijalankan tanpa --dry-run.");
        } else {
            $this->info("Selesai! {$totalTagihanDibuat} tagihan baru dibuat. {$totalSkip} dilewati.");
        }

        return self::SUCCESS;
    }
}
