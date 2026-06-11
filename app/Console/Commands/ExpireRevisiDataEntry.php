<?php

namespace App\Console\Commands;

use App\Models\DataEntryProgress;
use Illuminate\Console\Command;

class ExpireRevisiDataEntry extends Command
{
    protected $signature   = 'dataentry:expire-revisi';
    protected $description = 'Tandai DITOLAK progress REVISI yang tidak diselesaikan lebih dari 3 hari, dan kembalikan data ke available.';

    public function handle(): void
    {
        $deadline = now()->subDays(3);

        $expired = DataEntryProgress::where('status', 'REVISI')
            ->where('actioned_at', '<', $deadline)
            ->with('dataLapangan')
            ->get();

        $count = 0;

        foreach ($expired as $progress) {
            $dataLapangan = $progress->dataLapangan;

            // Tandai progress sebagai DITOLAK dengan keterangan otomatis
            $progress->update([
                'status'            => 'DITOLAK',
                'keterangan_revisi' => ($progress->keterangan_revisi ?? '')
                    . "\n[Otomatis: revisi tidak diselesaikan dalam 3 hari.]",
                'actioned_at'       => now(),
            ]);

            if (! $dataLapangan) {
                continue;
            }

            // Rollback status ke sebelum progress dibuat
            $oldStatus = $progress->old_data['status'] ?? null;
            if ($oldStatus) {
                $dataLapangan->status = $oldStatus;
            }

            // Pindahkan email_sihalal ke old_email_sihalal
            if ($dataLapangan->email_sihalal) {
                $dataLapangan->old_email_sihalal = $dataLapangan->email_sihalal;
                $dataLapangan->email_sihalal     = null;
            }

            // Lepas lock dan tandai available
            $dataLapangan->is_being_edited            = false;
            $dataLapangan->edited_by                  = null;
            $dataLapangan->edit_expires_at            = null;
            $dataLapangan->is_unlocked_for_data_entry = true;

            $dataLapangan->save();
            $count++;
        }

        $this->info("Selesai: {$count} progress REVISI kedaluwarsa ditandai DITOLAK.");
    }
}
