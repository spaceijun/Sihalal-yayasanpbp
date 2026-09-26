<?php

namespace App\Jobs;

use App\Models\Superadmin\WrgroupOutbox;
use App\Services\Integrasi\Wrgroup\WrgroupDeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Kirim satu event outbox ke WRGROUP segera setelah tercatat.
 *
 * Retry/backoff TIDAK memakai mekanisme queue — dikelola di kolom
 * wrgroup_outbox.next_attempt_at (dieksekusi scheduler `wrgroup:process`),
 * sehingga tetap jalan walau queue worker mati dan event_id selalu sama.
 */
class SendWrgroupEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public int $outboxId)
    {
        // Baru diantrikan setelah transaksi bisnis + baris outbox ter-commit.
        $this->afterCommit();
    }

    public function handle(WrgroupDeliveryService $delivery): void
    {
        $row = WrgroupOutbox::find($this->outboxId);

        if ($row && $row->status === 'pending') {
            $delivery->deliver($row);
        }
    }
}
