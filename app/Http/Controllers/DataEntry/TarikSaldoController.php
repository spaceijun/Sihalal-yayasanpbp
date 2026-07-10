<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryPenarikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TarikSaldoController extends Controller
{
    public function index()
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        // Penagihan yang statusnya 'Menunggu' dan belum ada penarikan aktif (Menunggu/Diproses)
        $penagihansMenunggu = DataEntryPenagihan::where('data_entry_id', $dataEntry->id)
            ->where('status', 'Menunggu')
            ->whereDoesntHave('penarikan', fn($q) => $q->whereIn('status', ['Menunggu', 'Diproses']))
            ->latest()
            ->get();

        // Total saldo yang bisa ditarik (penagihan status Menunggu yang belum diajukan penarikan)
        $totalBisaDitarik = $penagihansMenunggu->sum('nominal');

        // Riwayat penarikan
        $penarikan = DataEntryPenarikan::where('data_entry_id', $dataEntry->id)
            ->with('penagihans')
            ->latest()
            ->get();

        // Total yang sudah dibayar
        $totalDibayar = DataEntryPenarikan::where('data_entry_id', $dataEntry->id)
            ->where('status', 'Disetujui')
            ->sum('nominal');

        return view('data-entry.tarik-saldo.index', compact(
            'dataEntry',
            'penagihansMenunggu',
            'totalBisaDitarik',
            'penarikan',
            'totalDibayar',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'penagihan_ids'  => 'required|array|min:1',
            'penagihan_ids.*' => 'integer',
            'catatan_de'     => 'nullable|string|max:500',
        ]);

        $dataEntry = DataEntry::where('user_id', Auth::id())->firstOrFail();

        // Cek apakah sudah ada penarikan yang sedang diproses
        $adaPenarikanAktif = DataEntryPenarikan::where('data_entry_id', $dataEntry->id)
            ->whereIn('status', ['Menunggu', 'Diproses'])
            ->exists();

        if ($adaPenarikanAktif) {
            return redirect()->back()->with('warning', 'Anda masih memiliki penarikan yang sedang diproses. Tunggu hingga selesai sebelum mengajukan lagi.');
        }

        // Ambil penagihan yang dipilih & validasi kepemilikan
        $penagihans = DataEntryPenagihan::whereIn('id', $request->penagihan_ids)
            ->where('data_entry_id', $dataEntry->id)
            ->where('status', 'Menunggu')
            ->whereDoesntHave('penarikan', fn($q) => $q->whereIn('status', ['Menunggu', 'Diproses']))
            ->get();

        if ($penagihans->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada tagihan valid yang dipilih.');
        }

        $nominal = $penagihans->sum('nominal');

        DB::transaction(function () use ($dataEntry, $penagihans, $nominal, $request) {
            $penarikan = DataEntryPenarikan::create([
                'user_id'           => $dataEntry->user_id,
                'data_entry_id'     => $dataEntry->id,
                'nominal'           => $nominal,
                'catatan_de'        => $request->catatan_de,
                'status'            => 'Menunggu',
                'tanggal_pengajuan' => now(),
            ]);

            $penarikan->penagihans()->attach($penagihans->pluck('id'));
        });

        return redirect()->back()->with('success', 'Pengajuan penarikan saldo sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil dikirim. Tunggu persetujuan admin.');
    }
}
