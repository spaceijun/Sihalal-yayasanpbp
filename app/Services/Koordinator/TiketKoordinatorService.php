<?php

namespace App\Services\Koordinator;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TiketKoordinatorService
{
    public function store(int $userId, array $data, ?\Illuminate\Http\UploadedFile $file = null): Ticket
    {
        return DB::transaction(function () use ($userId, $data, $file) {
            $data['user_id'] = $userId;
            $data['no_ticket'] = 'KH-'.now()->format('YmdHis');
            $data['status'] = 'open';

            if ($file) {
                $data['file'] = $file->store('file-tiket', 'public');
            }

            return Ticket::create($data);
        });
    }
}
