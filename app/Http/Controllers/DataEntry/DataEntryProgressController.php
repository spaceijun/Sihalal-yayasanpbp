<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataEntryProgressController extends Controller
{
    // Lihat progress milik data entry yang sedang login
    public function myProgress()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        $totalKeseluruhan = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->count();

        $totalPerAction = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->selectRaw('action, COUNT(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action');

        $totalDataLapangan = DataLapangan::all()->count();

        return view('data-entry.dashboard', compact('progress'));
    }

    // Lihat progress semua data entry (untuk admin)
    public function allProgress()
    {
        $progress = DataEntryProgress::with(['user', 'dataEntry', 'dataLapangan'])
            ->latest('actioned_at')
            ->paginate(20);

        return view('progress.all', compact('progress'));
    }

    // Ringkasan progress per data entry
    public function summary()
    {
        $summary = DataEntry::withCount([
            'progress as total_created' => fn($q) => $q->where('action', 'created'),
            'progress as total_updated' => fn($q) => $q->where('action', 'updated'),
            'progress as total_deleted' => fn($q) => $q->where('action', 'deleted'),
        ])->get();

        return view('progress.summary', compact('summary'));
    }
}
