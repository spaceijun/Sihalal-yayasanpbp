<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        // Tarif sesuai entry_type
        $tarifPer15   = $dataEntry->entry_type === 'SIHALAL' ? 150000 : 100000;
        $kelipatanPer = 15;

        // Total semua data yang pernah dientry (action created)
        $totalDientry = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->count();

        // Total data yang sudah DITERIMA superadmin (basis penagihan)
        $totalDiterima = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->where('status', 'DITERIMA')
            ->count();

        // Total data masih PENDING (menunggu review)
        $totalPending = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->where('status', 'PENDING')
            ->count();

        // Total data REVISI (perlu diperbaiki)
        $totalRevisi = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->where('status', 'REVISI')
            ->count();

        // Total data DITOLAK
        $totalDitolak = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->where('status', 'DITOLAK')
            ->count();

        // Paket & penghasilan dihitung dari data DITERIMA
        $kelipatan        = (int) floor($totalDiterima / $kelipatanPer);
        $totalPenghasilan = $kelipatan * $tarifPer15;

        // Sisa data DITERIMA yang belum membentuk paket
        $sisaData = $totalDiterima % $kelipatanPer;

        // Riwayat penagihan
        $penagihans = DataEntryPenagihan::where('data_entry_id', $dataEntry->id)
            ->latest()
            ->get();

        return view('data-entry.dashboard', compact(
            'dataEntry',
            'totalDientry',
            'totalDiterima',
            'totalPending',
            'totalRevisi',
            'totalDitolak',
            'totalPenghasilan',
            'kelipatan',
            'sisaData',
            'kelipatanPer',
            'tarifPer15',
            'penagihans'
        ));
    }
}
