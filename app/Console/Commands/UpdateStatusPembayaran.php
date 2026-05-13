<?php

namespace App\Console\Commands;

use App\Models\DataLapangan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateStatusPembayaran extends Command
{
    protected $signature = 'status:update-pembayaran';
    protected $description = 'Update status_pembayaran menjadi PENGAJUAN jika sudah 2 hari setelah TERBIT SH';
    public function handle()
    {
        $duaHariLalu = Carbon::now()->subDays(2);
        $updated = DataLapangan::where('status', 'TERBIT SH')
            ->where('status_pembayaran', 'PENDING')
            ->where('tanggal_verifikasi', '<=', $duaHariLalu)
            ->update(['status_pembayaran' => 'PENGAJUAN']);
        $this->info("Updated {$updated} records.");
    }
}
