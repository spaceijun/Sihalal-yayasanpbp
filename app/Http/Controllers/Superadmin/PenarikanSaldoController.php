<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use App\Models\DataEntryPenarikan;
use App\Services\KawuloHalalService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PenarikanSaldoController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private KawuloHalalService  $kawulo,
    ) {}

    public function index()
    {
        $penarikan = DataEntryPenarikan::with(['dataEntry', 'penagihans'])
            ->latest()
            ->paginate(20);

        $totalMenunggu = DataEntryPenarikan::where('status', 'Menunggu')->count();
        $totalDiproses = DataEntryPenarikan::where('status', 'Diproses')->count();
        $totalDisetujui = DataEntryPenarikan::where('status', 'Disetujui')->sum('nominal');
        $totalDitolak  = DataEntryPenarikan::where('status', 'Ditolak')->count();

        return view('superadmin.penarikan-saldo.index', compact(
            'penarikan',
            'totalMenunggu',
            'totalDiproses',
            'totalDisetujui',
            'totalDitolak',
        ));
    }

    /**
     * Setujui penarikan saldo → tandai penagihan sebagai "Dibayar" → insert cashflow.
     */
    public function setujui(Request $request, DataEntryPenarikan $penarikan): RedirectResponse
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        if (!in_array($penarikan->status, ['Menunggu', 'Diproses'])) {
            return redirect()->back()->with('warning', 'Penarikan tidak dapat disetujui.');
        }

        $penarikan->update([
            'status'           => 'Disetujui',
            'catatan_admin'    => $request->catatan_admin,
            'tanggal_diproses' => now(),
        ]);

        // Update semua penagihan yang dicakup menjadi 'Dibayar'
        $penarikan->penagihans()->update([
            'status'          => 'Dibayar',
            'tanggal_dibayar' => now(),
        ]);

        // Hitung total & keterangan cashflow
        $dataEntry = $penarikan->dataEntry;
        $jumlahData  = $penarikan->penagihans->sum('jumlah_data');
        $jumlahPaket = $penarikan->penagihans->sum('jumlah_paket');

        Cashflow::create([
            'data_lapangan_id' => null,
            'tipe'             => 'Pengeluaran',
            'jumlah'           => $penarikan->nominal,
            'keterangan'       => 'Penarikan saldo data entry ' . $dataEntry->nama_lengkap .
                ' — ' . $jumlahData . ' data (' . $jumlahPaket . ' paket)',
            'tanggal'          => now()->toDateString(),
        ]);

        // Kirim notifikasi WA
        $notificationSent = false;
        if ($dataEntry && $dataEntry->telephone) {
            try {
                $notificationSent = $this->notificationService->sendPembayaranDataEntryNotification(
                    $dataEntry->nama_lengkap,
                    $dataEntry->telephone,
                    $jumlahData,
                    $jumlahPaket,
                    $penarikan->nominal,
                );
            } catch (\Exception $e) {
                Log::error('Penarikan: gagal kirim WA', [
                    'penarikan_id' => $penarikan->id,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $message  = 'Penarikan saldo Rp ' . number_format($penarikan->nominal, 0, ',', '.') . ' berhasil disetujui dan dicatat di cashflow.';
        $message .= $notificationSent ? ' Notifikasi WhatsApp telah dikirim.' : ' Namun notifikasi WhatsApp gagal dikirim.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Tolak penarikan saldo.
     */
    public function tolak(Request $request, DataEntryPenarikan $penarikan): RedirectResponse
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);

        if ($penarikan->status === 'Disetujui') {
            return redirect()->back()->with('warning', 'Penarikan yang sudah disetujui tidak dapat ditolak.');
        }

        $penarikan->update([
            'status'        => 'Ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('warning', 'Penarikan saldo telah ditolak.');
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    private function resolvePhone(?string $phone): ?string
    {
        if (!$phone) return null;
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        $len = strlen($phone);
        return ($len >= 10 && $len <= 15) ? $phone : null;
    }
}
