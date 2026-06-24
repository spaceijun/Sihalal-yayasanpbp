<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller API untuk Export PDF Laporan Enumerator
 * Endpoint: GET /api/enumerator/export-pdf
 * Auth: Sanctum + role enumerator
 */
class ExportPdfEnumeratorController extends Controller
{
    /**
     * Export PDF laporan enumerator untuk periode tertentu.
     * Hanya mengembalikan data enumerator yang sedang login.
     */
    public function __invoke(Request $request): JsonResponse|StreamedResponse|Response
    {
        // Ambil enumerator yang login
        $enumerator = Enumerator::with(['koordinator', 'bank'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$enumerator) {
            return response()->json([
                'success' => false,
                'message' => 'Data enumerator tidak ditemukan untuk user ini.',
            ], 404);
        }

        // Parameter periode (default: bulan & tahun sekarang)
        $target = 20;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        // Validasi bulan dan tahun
        if ($bulan < 1 || $bulan > 12) {
            return response()->json([
                'success' => false,
                'message' => 'Bulan tidak valid. Gunakan angka 1-12.',
            ], 422);
        }

        if ($tahun < 2020 || $tahun > 2100) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak valid.',
            ], 422);
        }

        // ── Hitung rentang tanggal 25 bulan sebelumnya s.d. 25 bulan ini ──
        $periodeAkhir = \Carbon\Carbon::create($tahun, $bulan, 25)->endOfDay();
        $periodeAwal = \Carbon\Carbon::create($tahun, $bulan, 25)
            ->subMonth()
            ->startOfDay();

        // ── Hitung total data untuk enumerator ini di periode tersebut ──
        $enumerator->total_data_bulan = DataLapangan::where('enumerator_id', $enumerator->id)
            ->whereBetween('created_at', [$periodeAwal, $periodeAkhir])
            ->count();

        // Collection untuk konsistensi dengan view
        $enumerators = collect([$enumerator]);

        // ── Label periode ──
        $labelAwal = $periodeAwal->locale('id')->isoFormat('D MMMM YYYY');
        $labelAkhir = $periodeAkhir->locale('id')->isoFormat('D MMMM YYYY');
        $periodeLabel = "{$labelAwal} – {$labelAkhir}";

        $exportedAt = now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm');

        // Bulan deadline = bulan yang dipilih (tanggal 25-nya)
        $deadlineLabel = $periodeAkhir->locale('id')->isoFormat('D MMMM YYYY');
        // Nama bulan akhir untuk keterangan tabel
        $namaBulanAkhir = $periodeAkhir->locale('id')->isoFormat('MMMM');

        // ── Generate PDF ──
        $pdf = Pdf::loadView(
            'superadmin.enumerator.partials.export-pdf',
            compact(
                'enumerators',
                'exportedAt',
                'target',
                'periodeLabel',
                'deadlineLabel',
                'namaBulanAkhir',
                'bulan',
                'tahun',
                'periodeAwal',
                'periodeAkhir'
            )
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'enable_css_float' => true,
            ]);

        $filename = 'laporan-enumerator-'
            . $tahun
            . str_pad($bulan, 2, '0', STR_PAD_LEFT)
            . '-enum-' . $enumerator->no_registrasi
            . '-' . now()->format('His') . '.pdf';

        return $pdf->download($filename);
    }
}
