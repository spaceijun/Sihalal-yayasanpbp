<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Controller API untuk Download Surat Tugas PDF
 * Endpoint: GET /api/enumerator/surat-tugas
 * Auth: Sanctum + role enumerator
 */
class SuratTugasPdfController extends Controller
{
    /**
     * Download Surat Tugas PDF untuk enumerator yang sedang login.
     */
    public function __invoke(): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
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

        // Load koordinator jika belum ter-load
        if (!$enumerator->relationLoaded('koordinator')) {
            $enumerator->load('koordinator');
        }

        // ── Generate PDF ──
        $pdf = Pdf::loadView(
            'superadmin.enumerator.partials.surat',
            compact('enumerator')
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Times New Roman',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'enable_css_float' => true,
            ]);

        $filename = 'surat-tugas-'
            . str_replace(' ', '-', strtolower($enumerator->nama_lengkap))
            . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
