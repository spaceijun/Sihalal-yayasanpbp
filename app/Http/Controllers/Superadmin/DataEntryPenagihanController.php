<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use App\Models\DataEntryPenagihan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DataEntryPenagihanController extends Controller
{
    public function index()
    {
        $penagihans = DataEntryPenagihan::with(['dataEntry', 'user'])
            ->latest()
            ->paginate(20);

        $totalMenunggu = DataEntryPenagihan::where('status', 'Menunggu')->count();
        $totalDiproses = DataEntryPenagihan::where('status', 'Diproses')->count();
        $totalDibayar  = DataEntryPenagihan::where('status', 'Dibayar')->sum('nominal');
        $totalDitolak  = DataEntryPenagihan::where('status', 'Ditolak')->count();

        return view('superadmin.penagihan.index', compact(
            'penagihans',
            'totalMenunggu',
            'totalDiproses',
            'totalDibayar',
            'totalDitolak'
        ));
    }

    public function proses(DataEntryPenagihan $penagihan): RedirectResponse
    {
        if ($penagihan->status !== 'Menunggu') {
            return redirect()->back()->with('warning', 'Tagihan tidak dalam status Menunggu.');
        }

        $penagihan->update(['status' => 'Diproses']);

        return redirect()->back()->with('info', 'Tagihan sedang diproses.');
    }

    /**
     * Approve tagihan yang sedang diproses.
     */
    public function approve(Request $request, DataEntryPenagihan $penagihan)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        if (!in_array($penagihan->status, ['Menunggu', 'Diproses'])) {
            return redirect()->back()->with('warning', 'Tagihan tidak dapat disetujui.');
        }

        $penagihan->update([
            'status'          => 'Dibayar',
            'catatan'         => $request->catatan,
            'tanggal_dibayar' => now(),
        ]);

        // Hitung tarif per paket dari nominal aktual (bukan hardcode)
        $tarifPerPaket = $penagihan->jumlah_paket > 0
            ? (int) round($penagihan->nominal / $penagihan->jumlah_paket)
            : 0;

        // Insert ke cashflow sebagai Pengeluaran
        Cashflow::create([
            'data_lapangan_id' => null,
            'tipe'             => 'Pengeluaran',
            'jumlah'           => $penagihan->nominal,
            'keterangan'       => 'Pembayaran data entry ' . $penagihan->dataEntry->nama_lengkap .
                ' sebanyak ' . $penagihan->jumlah_data . ' data' .
                ' (' . $penagihan->jumlah_paket . ' paket x Rp ' .
                number_format($tarifPerPaket, 0, ',', '.') . ')',
            'tanggal'          => now()->toDateString(),
        ]);

        // Kirim notifikasi WhatsApp ke data entry
        $dataEntry        = $penagihan->dataEntry;
        $notificationSent = false;

        if ($dataEntry && $dataEntry->telephone) {
            $notificationSent = $penagihan->sendPembayaranDataEntryNotification(
                $dataEntry->nama_lengkap,
                $dataEntry->telephone,
                $penagihan->jumlah_data,
                $penagihan->jumlah_paket,
                $penagihan->nominal,
            );
        }

        $message = 'Tagihan Rp ' . number_format($penagihan->nominal, 0, ',', '.') . ' berhasil disetujui dan dicatat di cashflow.';
        $message .= $notificationSent
            ? ' Notifikasi WhatsApp telah dikirim ke data entry.'
            : ' Namun notifikasi WhatsApp gagal dikirim.';

        return redirect()->back()->with('success', $message);
    }

    public function tolak(Request $request, DataEntryPenagihan $penagihan): RedirectResponse
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ]);

        if ($penagihan->status === 'Dibayar') {
            return redirect()->back()->with('warning', 'Tagihan yang sudah dibayar tidak dapat ditolak.');
        }

        $penagihan->update([
            'status'  => 'Ditolak',
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('warning', 'Tagihan telah ditolak.');
    }
}
