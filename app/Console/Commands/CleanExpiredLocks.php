<?php

namespace App\Console\Commands;

use App\Models\DataLapangan;
use Illuminate\Console\Command;

class CleanExpiredLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-lapangan:clean-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DataLapangan::where('is_being_edited', true)
            ->where('edit_expires_at', '<', now())
            ->update([
                'is_being_edited' => false,
                'edited_by'       => null,
                'edit_expires_at' => null,
            ]);
    }
}
