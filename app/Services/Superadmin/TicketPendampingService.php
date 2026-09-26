<?php

namespace App\Services\Superadmin;

use App\Models\TicketPendamping;
use Illuminate\Support\Facades\DB;

class TicketPendampingService
{
    public function updateStatus(TicketPendamping $ticket, string $status): TicketPendamping
    {
        return DB::transaction(function () use ($ticket, $status) {
            $ticket->update(['status' => $status]);

            return $ticket->fresh();
        });
    }

    public function delete(TicketPendamping $ticket): void
    {
        DB::transaction(function () use ($ticket) {
            $ticket->delete();
        });
    }
}
