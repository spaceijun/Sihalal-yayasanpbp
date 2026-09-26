<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cashflow;
use App\Models\DataEntryPenarikan;
use App\Services\KawuloHalalService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PenarikanSaldoController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private KawuloHalalService  $kawulo,
    ) {}

    public function index()
    {
        $totalMenunggu = DataEntryPenarikan::where('status', 'Menunggu')->count();
        $totalDiproses = DataEntryPenarikan::where('status', 'Diproses')->count();
        $totalDisetujui = DataEntryPenarikan::where('status', 'Disetujui')->sum('nominal');
        $totalDitolak  = DataEntryPenarikan::where('status', 'Ditolak')->count();

        return view('superadmin.penarikan-saldo.index', compact(
            'totalMenunggu',
            'totalDiproses',
            'totalDisetujui',
            'totalDitolak',
        ));
    }

    /**
     * Return DataTables JSON for the penarikan saldo listing.
     */
    public function data(Request $request)
    {
        $query = DataEntryPenarikan::with(['dataEntry', 'penagihans']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('dataentry_cell', function ($p) {
                $inisial = strtoupper(substr($p->dataEntry->nama_lengkap, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.e($inisial).'</div>
                    <div>
                        <div style="font-weight:600;font-size:13px;">'.e($p->dataEntry->nama_lengkap).'</div>
                        <div style="font-size:11.5px;color:var(--adm-text-muted);">'.e($p->dataEntry->email).'</div>
                    </div>
                </div>';
            })
            ->addColumn('tanggal_fmt', fn ($p) => $p->tanggal_pengajuan->format('d M Y, H:i'))
            ->addColumn('tagihan_badge', fn ($p) => '<span class="adm-badge adm-badge-info">'.$p->penagihans->count().' Tagihan</span>')
            ->addColumn('nominal_fmt', fn ($p) => 'Rp '.number_format($p->nominal, 0, ',', '.'))
            ->addColumn('status_badge', fn ($p) => match ($p->status) {
                'Menunggu' => '<span class="adm-badge adm-badge-pending"><span class="dot"></span>Menunggu</span>',
                'Diproses' => '<span class="adm-badge adm-badge-info"><span class="dot"></span>Diproses</span>',
                'Disetujui' => '<span class="adm-badge adm-badge-success"><span class="dot"></span>Disetujui</span>',
                'Ditolak' => '<span class="adm-badge adm-badge-danger"><span class="dot"></span>Ditolak</span>',
                default => '<span class="adm-badge">'.e($p->status).'</span>',
            })
            ->addColumn('catatan_de_cell', fn ($p) => $p->catatan_de
                ? '<span style="font-size:12px;color:var(--adm-text-muted);">'.e($p->catatan_de).'</span>'
                : '<span style="color:var(--adm-text-faint);">—</span>')
            ->addColumn('aksi', function ($p) {
                if (in_array($p->status, ['Menunggu', 'Diproses'])) {
                    return '<div class="adm-actions" style="justify-content:center;gap:5px;">
                        <button type="button" class="adm-btn success" style="font-size:11.5px;padding:5px 10px;"
                            onclick="bukaModalSetujui('.$p->id.', \''.e(addslashes($p->dataEntry->nama_lengkap)).'\', \''.e(number_format($p->nominal, 0, ',', '.')).'\', '.$p->penagihans->count().')">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Setujui
                        </button>
                        <button type="button" class="adm-btn danger" style="font-size:11.5px;padding:5px 10px;"
                            onclick="bukaModalTolak('.$p->id.', \''.e(addslashes($p->dataEntry->nama_lengkap)).'\', \''.e(number_format($p->nominal, 0, ',', '.')).'\')">
                            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tolak
                        </button>
                    </div>';
                }

                $catatan = $p->catatan_admin;
                if (! $catatan) {
                    return '<span style="color:var(--adm-text-faint);text-align:center;display:block;">—</span>';
                }

                $color = $p->status === 'Disetujui' ? 'var(--adm-blue)' : 'var(--adm-blue)';

                return '<span title="'.e($catatan).'" style="cursor:help;color:'.$color.';display:flex;justify-content:center;">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>';
            })
            ->rawColumns(['dataentry_cell', 'tagihan_badge', 'status_badge', 'catatan_de_cell', 'aksi'])
            ->make(true);
    }

    /**
     * Setujui penarikan saldo → tandai penagihan sebagai "Dibayar" → insert cashflow.
     */
    public function setujui(Request $request, DataEntryPenarikan $penarikan): JsonResponse
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        if (!in_array($penarikan->status, ['Menunggu', 'Diproses'])) {
            return response()->json(['message' => 'Penarikan tidak dapat disetujui.'], 422);
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

        return response()->json(['message' => $message]);
    }

    /**
     * Tolak penarikan saldo.
     */
    public function tolak(Request $request, DataEntryPenarikan $penarikan): JsonResponse
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ]);

        if ($penarikan->status === 'Disetujui') {
            return response()->json(['message' => 'Penarikan yang sudah disetujui tidak dapat ditolak.'], 422);
        }

        $penarikan->update([
            'status'        => 'Ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return response()->json(['message' => 'Penarikan saldo telah ditolak.']);
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
