<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryProgress;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        // Tarif sesuai entry_type
        $tarifPer15 = $dataEntry->entry_type === 'SIHALAL'
            ? config('services.sihalal.tarif_per_paket_sihalal')
            : config('services.oss.tarif_per_paket_oss');
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
        $kelipatan = (int) floor($totalDiterima / $kelipatanPer);
        $totalPenghasilan = DataEntryPenagihan::where('data_entry_id', $dataEntry->id)
            ->where('status', 'Dibayar')
            ->sum('nominal');

        // Sisa data DITERIMA yang belum membentuk paket
        $sisaData = $totalDiterima % $kelipatanPer;

        // Riwayat penagihan
        $penagihans = DataEntryPenagihan::where('data_entry_id', $dataEntry->id)
            ->latest()
            ->get();

        // Cek pengumuman terbaru sesuai entry_type
        $pengumuman = Pengumuman::where('jenis', $dataEntry->entry_type)
            ->latest()
            ->first();

        // Tampilkan modal jika ada pengumuman baru yang belum dibaca
        $showPengumuman = $pengumuman &&
            $pengumuman->id !== $dataEntry->last_read_pengumuman_id;

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
            'penagihans',
            'pengumuman',
            'showPengumuman'
        ));
    }

    public function markPengumumanRead(Request $request)
    {
        $request->validate(['pengumuman_id' => 'required|exists:pengumumans,id']);

        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();
        $dataEntry->update(['last_read_pengumuman_id' => $request->pengumuman_id]);

        return response()->json(['success' => true]);
    }
}
