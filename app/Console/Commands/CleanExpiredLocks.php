<?php

namespace App\Console\Commands;

use App\Models\DataLapangan;
use Illuminate\Console\Command;

class CleanExpiredLocks extends Command
{
    protected $signature = 'data-lapangan:clean-locks';
    protected $description = 'Bersihkan lock data lapangan yang sudah expired';

    public function handle()
    {
        $count = DataLapangan::where('is_being_edited', true)
            ->where('edit_expires_at', '<', now())
            ->update([
                'is_being_edited' => false,
                'edited_by'       => null,
                'edit_expires_at' => null,
            ]);

        $this->info("Berhasil membersihkan {$count} lock yang expired.");
    }
}
