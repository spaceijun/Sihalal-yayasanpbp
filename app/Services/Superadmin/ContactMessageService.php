<?php

namespace App\Services\Superadmin;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

class ContactMessageService
{
    /**
     * Mark a single message as read (no-op if already read).
     */
    public function markAsRead(ContactMessage $message): void
    {
        DB::transaction(function () use ($message) {
            if (! $message->read_at) {
                $message->markAsRead();
            }
        });
    }

    public function updateStatus(ContactMessage $message, array $data): ContactMessage
    {
        return DB::transaction(function () use ($message, $data) {
            $message->update($data);

            return $message->fresh();
        });
    }

    public function delete(ContactMessage $message): void
    {
        DB::transaction(function () use ($message) {
            $message->delete();
        });
    }

    /**
     * Mark all pending messages as read.
     */
    public function markAllRead(): void
    {
        DB::transaction(function () {
            ContactMessage::pending()->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        });
    }
}
