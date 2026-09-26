<?php

namespace App\Services\Superadmin;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Tutup tiket. Melempar exception jika tiket sudah berstatus closed.
     */
    public function close(Ticket $ticket): Ticket
    {
        return DB::transaction(function () use ($ticket) {
            if ($ticket->status === 'closed') {
                throw new \Exception('Tiket sudah ditutup.');
            }

            $ticket->update(['status' => 'closed']);

            return $ticket->fresh();
        });
    }

    public function delete(Ticket $ticket): void
    {
        DB::transaction(function () use ($ticket) {
            $ticket->delete();
        });
    }
}
