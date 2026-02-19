<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard of data entry
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        // Hanya hitung action 'created' sebagai data yang berhasil dientry
        $totalDientry = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->count();

        $totalPerAction = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->selectRaw('action, COUNT(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action');

        //  Ambil riwayat penagihan milik data entry ini
        $penagihans = DataEntryPenagihan::where('data_entry_id', $dataEntry->id)
            ->latest()
            ->get();

        // Hitung penghasilan: per 15 data = Rp 100.000
        $tarifPer15    = 100000;
        $kelipatanPer  = 15;
        $kelipatan     = (int) floor($totalDientry / $kelipatanPer);
        $totalPenghasilan = $kelipatan * $tarifPer15;

        // Sisa data yang belum mencapai kelipatan 15
        $sisaData = $totalDientry % $kelipatanPer;

        return view('data-entry.dashboard', compact(
            'totalDientry',
            'totalPerAction',
            'totalPenghasilan',
            'kelipatan',
            'sisaData',
            'kelipatanPer',
            'tarifPer15',
            'penagihans'
        ));
    }
}
