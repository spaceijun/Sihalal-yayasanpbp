<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiOcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    public function __construct(
        private GeminiOcrService $ocrService
    ) {}

    /**
     * Scan KTP using Gemini OCR
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function scanKtp(Request $request): JsonResponse
    {
        $request->validate([
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            // Get the uploaded image
            $file = $request->file('foto_ktp');

            // Convert to base64
            $base64Image = base64_encode(file_get_contents($file->getRealPath()));

            // Scan using Gemini
            $result = $this->ocrService->scanKtp($base64Image);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'KTP berhasil dipindai',
                    'data' => $result['data'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Gagal memindai KTP',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memindai KTP',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
