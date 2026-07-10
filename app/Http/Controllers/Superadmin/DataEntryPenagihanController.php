<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryPenarikan;
use App\Services\KawuloHalalService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DataEntryPenagihanController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private PdfService          $pdfService,
        private KawuloHalalService  $kawulo,
    ) {}

    public function index()
    {
        $penagihans = DataEntryPenagihan::with(['dataEntry', 'user', 'penarikan'])
            ->latest()
            ->paginate(20);

        $totalMenunggu = DataEntryPenagihan::where('status', 'Menunggu')->count();
        $totalDibayar  = DataEntryPenagihan::where('status', 'Dibayar')->sum('nominal');

        // Tagihan status 'Menunggu' yang belum pernah diajukan penarikan
        // (tidak ada penarikan aktif yang mencakup tagihan tersebut)
        $totalNominalBelumDiajukan = DataEntryPenagihan::where('status', 'Menunggu')
            ->whereDoesntHave('penarikan', fn($q) => $q->whereIn('status', ['Menunggu', 'Diproses', 'Disetujui']))
            ->sum('nominal');
        $totalCountBelumDiajukan = DataEntryPenagihan::where('status', 'Menunggu')
            ->whereDoesntHave('penarikan', fn($q) => $q->whereIn('status', ['Menunggu', 'Diproses', 'Disetujui']))
            ->count();

        return view('superadmin.penagihan.index', compact(
            'penagihans',
            'totalMenunggu',
            'totalDibayar',
            'totalNominalBelumDiajukan',
            'totalCountBelumDiajukan'
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
     * Setelah approve: generate PDF receipt → simpan ke storage → kirim WA via KawuloHalalService.
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

        // ── Generate & simpan PDF Receipt ──────────────────────────────────
        $dataEntry = $penagihan->dataEntry;
        $pdfUrl    = null;

        try {
            $pdf         = $this->pdfService->generateReceiptPdf($penagihan);
            $filename    = 'receipt_penagihan_' . $penagihan->id . '_' . now()->format('YmdHis') . '.pdf';
            $storagePath = 'receipts/' . $filename;

            // Simpan ke storage/app/public/receipts/
            Storage::disk('public')->put($storagePath, $pdf->output());

            // Simpan path receipt ke penagihan agar bisa di-download nanti
            $penagihan->update(['receipt_path' => $storagePath]);

            $pdfUrl = Storage::disk('public')->url($storagePath);
        } catch (\Exception $e) {
            Log::error('Penagihan: gagal generate receipt PDF', [
                'penagihan_id' => $penagihan->id,
                'error'        => $e->getMessage(),
            ]);
        }

        // ── Kirim Notifikasi WhatsApp via KawuloHalalService ───────────────
        $notificationSent = false;

        if ($dataEntry && $dataEntry->telephone) {
            $phone = $this->resolvePhone($dataEntry->telephone);

            if ($phone && $pdfUrl) {
                // Kirim PDF receipt sebagai dokumen WhatsApp
                try {
                    $caption          = $this->buildReceiptCaption($dataEntry->nama_lengkap, $penagihan);
                    $result           = $this->kawulo->sendDocument($phone, $pdfUrl, $caption);
                    $notificationSent = $result['status'] ?? false;
                } catch (\Exception $e) {
                    Log::error('Penagihan: gagal kirim WA dokumen receipt', [
                        'penagihan_id' => $penagihan->id,
                        'error'        => $e->getMessage(),
                    ]);
                }
            }

            // Fallback: kirim teks biasa jika PDF/dokumen WA gagal
            if (!$notificationSent) {
                $notificationSent = $this->notificationService->sendPembayaranDataEntryNotification(
                    $dataEntry->nama_lengkap,
                    $dataEntry->telephone,
                    $penagihan->jumlah_data,
                    $penagihan->jumlah_paket,
                    $penagihan->nominal,
                );
            }
        }

        $waLabel  = ($pdfUrl && $notificationSent) ? ' + receipt PDF' : '';
        $message  = 'Tagihan Rp ' . number_format($penagihan->nominal, 0, ',', '.') . ' berhasil disetujui dan dicatat di cashflow.';
        $message .= $notificationSent
            ? " Notifikasi WhatsApp{$waLabel} telah dikirim ke data entry."
            : ' Namun notifikasi WhatsApp gagal dikirim.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Download receipt PDF untuk penagihan yang sudah Dibayar.
     */
    public function downloadReceipt(DataEntryPenagihan $penagihan)
    {
        if ($penagihan->status !== 'Dibayar') {
            abort(403, 'Receipt hanya tersedia untuk tagihan yang sudah Dibayar.');
        }

        $pdf      = $this->pdfService->generateReceiptPdf($penagihan);
        $filename = 'Receipt_Pembayaran_' . str_pad($penagihan->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
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

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function resolvePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }
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

    private function buildReceiptCaption(string $nama, DataEntryPenagihan $penagihan): string
    {
        return "💰 *PEMBAYARAN DATA ENTRY DIKONFIRMASI*\n\n" .
            "Halo *{$nama}*!\n\n" .
            "Pembayaran untuk tagihan Anda telah disetujui.\n\n" .
            "📋 *Ringkasan:*\n" .
            "• Jumlah Data  : *{$penagihan->jumlah_data} data*\n" .
            "• Jumlah Paket : *{$penagihan->jumlah_paket} paket*\n" .
            "• Total        : *Rp " . number_format($penagihan->nominal, 0, ',', '.') . "*\n\n" .
            "📎 Receipt PDF terlampir.\n\n" .
            "✅ *Terima kasih atas kerja kerasmu!*\n\n" .
            "_Dikirim otomatis oleh sistem._\n" .
            "Best Regards,\n" .
            "*TIM KAWULO HALAL*\n" .
            "+62 897-6774-482";
    }
}
