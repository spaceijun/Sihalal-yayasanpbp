<?php

namespace App\Http\Controllers\Api\Enumerator;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use App\Models\SettingWebsite;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller API untuk Download ID Card PDF
 * Endpoint: GET /api/enumerator/id-card
 * Auth: Sanctum + role enumerator
 */
class IdCardPdfController extends Controller
{
    /**
     * Download ID Card PDF untuk enumerator yang sedang login.
     */
    public function __invoke(): JsonResponse|StreamedResponse|Response
    {
        // Ambil enumerator yang login
        $enumerator = Enumerator::where('user_id', Auth::id())->first();

        if (!$enumerator) {
            return response()->json([
                'success' => false,
                'message' => 'Data enumerator tidak ditemukan untuk user ini.',
            ], 404);
        }

        // Load foto_diri
        if (!$enumerator->foto_diri) {
            return response()->json([
                'success' => false,
                'message' => 'Foto diri enumerator belum diupload. Silakan upload foto diri terlebih dahulu.',
            ], 400);
        }

        // Ambil setting website untuk favicon/logo
        $settingWebsite = SettingWebsite::first() ?? new SettingWebsite();

        // ── Generate PDF ──
        $pdf = Pdf::loadView(
            'superadmin.enumerator.partials.idcard',
            compact('enumerator', 'settingWebsite')
        )
            ->setPaper([0, 0, 590, 1004], 'portrait') // Custom size sesuai ID card
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 150,
                'enable_css_float' => true,
                'margin-top' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,
            ]);

        $filename = 'id-card-'
            . str_replace(' ', '-', strtolower($enumerator->nama_lengkap))
            . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
