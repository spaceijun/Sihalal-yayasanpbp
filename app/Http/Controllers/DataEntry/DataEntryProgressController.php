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
    /**
     * Lihat progress milik data entry yang sedang login
     */
    public function index()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        $progress = DataEntryProgress::with('dataLapangan')
            ->where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->latest('actioned_at')
            ->paginate(20);

        return view('data-entry.progress.index', compact('progress'));
    }

    /**
     * Detail data lapangan dari halaman progress
     */
    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        $entryType = $dataEntry?->entry_type;

        // Ambil progress terbaru milik data lapangan ini untuk user yang login
        $latestProgress = $dataEntry
            ? DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('data_lapangan_id', $dataLapangan->id)
            ->where('action', 'created')
            ->latest('actioned_at')
            ->first()
            : null;

        return view('data-entry.progress.show', compact('dataLapangan', 'entryType', 'latestProgress'));
    }

    /**
     * Ringkasan progress per data entry (untuk admin)
     */
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
