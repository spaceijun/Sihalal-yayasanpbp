<?php

namespace App\Observers;

use App\Models\DataLapangan;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use Illuminate\Support\Facades\Auth;

class DataLapanganObserver
{
    private function isDataEntry(): bool
    {
        return Auth::check() && Auth::user()->role === 'data_entry';
    }

    private function getDataEntryId(): ?int
    {
        if (!Auth::check()) return null;

        $dataEntry = DataEntry::where('user_id', Auth::id())->first();
        return $dataEntry?->id;
    }

    public function created(DataLapangan $dataLapangan): void
    {
        if (!$this->isDataEntry()) return;

        DataEntryProgress::create([
            'user_id'          => Auth::id(),
            'data_entry_id'    => $this->getDataEntryId(),
            'data_lapangan_id' => $dataLapangan->id,
            'action'           => 'created',
            'old_data'         => null,
            'new_data'         => $dataLapangan->toArray(),
            'actioned_at'      => now(),
        ]);
    }

    public function updated(DataLapangan $dataLapangan): void
    {
        if (!$this->isDataEntry()) return;

        // Skip jika hanya update field lock/unlock (is_being_edited, edited_by, edit_expires_at)
        $lockFields = ['is_being_edited', 'edited_by', 'edit_expires_at', 'updated_at'];
        $changedKeys = array_keys($dataLapangan->getChanges());

        if (empty(array_diff($changedKeys, $lockFields))) {
            return;
        }

        DataEntryProgress::create([
            'user_id'          => Auth::id(),
            'data_entry_id'    => $this->getDataEntryId(),
            'data_lapangan_id' => $dataLapangan->id,
            'action'           => 'updated',
            'old_data'         => $dataLapangan->getOriginal(),
            'new_data'         => $dataLapangan->getChanges(),
            'actioned_at'      => now(),
        ]);
    }
    public function deleted(DataLapangan $dataLapangan): void
    {
        if (!$this->isDataEntry()) return;

        DataEntryProgress::create([
            'user_id'          => Auth::id(),
            'data_entry_id'    => $this->getDataEntryId(),
            'data_lapangan_id' => $dataLapangan->id,
            'action'           => 'deleted',
            'old_data'         => $dataLapangan->toArray(),
            'new_data'         => null,
            'actioned_at'      => now(),
        ]);
    }
}
