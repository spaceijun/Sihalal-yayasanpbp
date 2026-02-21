<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataEntryProgressController extends Controller
{
    // Lihat progress milik data entry yang sedang login
    public function index()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();
        $progress = DataEntryProgress::with('dataLapangan')
            ->where('data_entry_id', $dataEntry->id)
            ->latest('actioned_at')
            ->paginate(20);
        return view('data-entry.progress.index', compact('progress'));
    }

    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        $entryType = DataEntry::where('user_id', Auth::id())
            ->value('entry_type');

        return view('data-entry.progress.show', compact('dataLapangan', 'entryType'));
    }


    // Lihat progress semua data entry (untuk admin)
    public function allProgress()
    {
        $progress = DataEntryProgress::with(['user', 'dataEntry', 'dataLapangan'])
            ->latest('actioned_at')
            ->paginate(20);

        return view('progress.index', compact('progress'));
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
