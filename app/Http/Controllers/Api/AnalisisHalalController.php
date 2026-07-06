<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PanduanHalalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AnalisisHalalController
 *
 * Rule-based halal analysis using PanduanHalalService.
 * No longer uses Claude API.
 *
 * POST /api/data-entry/analisis-halal
 * Body: { prompt: string (product name), save?: bool, bahan?: array, proses?: array }
 */
class AnalisisHalalController extends Controller
{
    protected PanduanHalalService $service;

    public function __construct(PanduanHalalService $service)
    {
        $this->service = $service;
    }

    /**
     * Analyze product for halal guidance
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'prompt'   => 'required|string|max:6000',
            'save' => 'nullable|boolean',
            'bahan' => 'nullable|array',
            'proses' => 'nullable|array',
            'kemasan' => 'nullable|string',
            'catatan_halal' => 'nullable|string',
            'status_halal' => 'nullable|string|in:AMAN,PERLU_VERIFIKASI,BERISIKO,TIDAK_HALAL',
        ]);

        $productQuery = trim($request->prompt);
        $productName = $this->extractProductName($productQuery);

        Log::info('AnalisisHalal: Processing request', [
            'original_prompt' => $productQuery,
            'extracted_name' => $productName,
            'save_requested' => $request->boolean('save'),
        ]);

        try {
            // Get user ID if authenticated
            $userId = $request->user()?->id;

            // Get halal guidance from service
            try {
                $result = $this->service->getPanduan($productName, $userId);
            } catch (\Throwable $e) {
                Log::error('PanduanHalalService error', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }

            // Validate result is an array
            if (!is_array($result)) {
                Log::error('PanduanHalalService returned non-array', ['type' => gettype($result)]);
                $result = [];
            }

            // If user wants to save the analysis (with provided data)
            if ($request->boolean('save') && $request->has('bahan')) {
                $saveData = [
                    'product_name' => $productName,
                    'kemasan' => $request->input('kemasan', $result['kemasan']),
                    'bahan' => $request->input('bahan'),
                    'proses' => $request->input('proses', $result['proses']),
                    'catatan_halal' => $request->input('catatan_halal', $result['catatan_halal']),
                    'status_halal' => $request->input('status_halal', 'PERLU_VERIFIKASI'),
                    'google_search_url' => $result['google_search']['utama'] ?? null,
                ];

                $analisis = $this->service->saveToDatabase($saveData, $userId);
                $result['saved_to_database'] = true;
                $result['analisis_id'] = $analisis->id;
            }

            // Transform to match frontend expected format
            $response = $this->transformToFrontendFormat($result, $productName);

            return response()->json([
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                    ],
                ],
                'model' => 'rule-based-service',
                'source' => $result['sumber'] ?? 'google_search',
                'found' => $result['found_in_database'] ?? false,
            ]);

        } catch (\Exception $e) {
            Log::error('AnalisisHalal error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode([
                            'kemasan' => 'Error occurred',
                            'bahan' => [],
                            'proses' => [],
                            'catatan_halal' => 'Terjadi kesalahan: ' . $e->getMessage(),
                            'status_halal' => 'ERROR',
                            'google_search_url' => [],
                        ], JSON_UNESCAPED_UNICODE),
                    ],
                ],
                'model' => 'rule-based-service',
                'source' => 'error',
            ], 500);
        }
    }

    /**
     * Save analysis result to database
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'kemasan' => 'nullable|string',
            'bahan' => 'required|array',
            'proses' => 'required|array',
            'catatan_halal' => 'nullable|string',
            'status_halal' => 'nullable|string',
            'data_lapangan_id' => 'nullable|integer|exists:data_lapangans,id',
        ]);

        $userId = $request->user()?->id;
        $dataLapanganId = $request->input('data_lapangan_id');

        $analisis = $this->service->saveToDatabase([
            'product_name' => $request->product_name,
            'kemasan' => $request->kemasan,
            'bahan' => $request->bahan,
            'proses' => $request->proses,
            'catatan_halal' => $request->catatan_halal,
            'status_halal' => $request->status_halal ?? 'PERLU_VERIFIKASI',
            'google_search_url' => "https://www.google.com/search?q=" . urlencode($request->product_name . " sertifikasi halal MUI"),
        ], $userId, $dataLapanganId);

        return response()->json([
            'success' => true,
            'message' => 'Analisis berhasil disimpan',
            'data' => $analisis,
        ]);
    }

    /**
     * Get verification links for a product
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verificationLinks(Request $request)
    {
        $request->validate([
            'product' => 'required|string|max:500',
        ]);

        $productName = $this->extractProductName($request->product);
        $searchUrls = $this->generateSearchUrls($productName);

        return response()->json([
            'success' => true,
            'product_name' => $productName,
            'links' => $searchUrls,
        ]);
    }

    /**
     * Search products in database
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:200',
        ]);

        $results = $this->service->searchAnalyzed($request->keyword);

        return response()->json([
            'success' => true,
            'count' => count($results),
            'results' => $results,
        ]);
    }

    /**
     * Get pending approvals (admin)
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pendingApprovals(Request $request)
    {
        // Check if user is admin
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $pending = $this->service->getPendingApprovals();

        return response()->json([
            'success' => true,
            'count' => count($pending),
            'data' => $pending,
        ]);
    }

    /**
     * Approve an analysis (admin)
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:analisis_products,id',
        ]);

        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $success = $this->service->approve($request->id, $user->id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Analisis berhasil diapprove' : 'Gagal approve analisis',
        ]);
    }

    /**
     * Get statistics (admin)
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'stats' => $this->service->getStats(),
        ]);
    }

    /**
     * Extract product name from prompt text
     *
     * @param  string  $prompt
     * @return string
     */
    private function extractProductName(string $prompt): string
    {
        // Format: "Produk: \"PRODUCT NAME\""
        if (preg_match('/Produk:\s*["\"]?([^"\n]+)["\"]?/i', $prompt, $matches)) {
            return trim($matches[1]);
        }

        // Format: "Analisis produk [PRODUCT NAME]"
        if (preg_match('/analisis\s+(?:produk\s+)?(.+)/i', $prompt, $matches)) {
            $product = trim($matches[1]);
            $product = preg_replace('/\s+Beri.*$/is', '', $product);
            $product = preg_replace('/\s+Sertakan.*$/is', '', $product);
            if (strlen($product) > 2) {
                return $product;
            }
        }

        // Format: "Panduan halal untuk [PRODUCT NAME]"
        if (preg_match('/(?:panduan\s+halal|analisis\s+halal)\s+(?:untuk\s+)?(.+)/i', $prompt, $matches)) {
            $product = trim($matches[1]);
            $product = preg_replace('/\s+Beri.*$/is', '', $product);
            $product = preg_replace('/\s+Sertakan.*$/is', '', $product);
            $product = preg_replace('/\s+Balas.*$/is', '', $product);
            if (strlen($product) > 2) {
                return $product;
            }
        }

        // Format: Direct product name at beginning
        $firstLine = trim(explode("\n", $prompt)[0]);
        $firstLine = preg_replace('/^[^\w]*/', '', $firstLine);
        if (strlen($firstLine) >= 3 && strlen($firstLine) <= 100) {
            return $firstLine;
        }

        return trim($prompt);
    }

    /**
     * Generate Google Search URLs
     */
    private function generateSearchUrls(string $productName): array
    {
        $encoded = urlencode($productName);

        return [
            'utama' => "https://www.google.com/search?q=" . urlencode("resep {$productName} bahan lengkap") . "&hl=id&gl=id",
            'sertifikasi' => "https://www.google.com/search?q=" . urlencode("{$productName} sertifikasi halal MUI") . "&hl=id&gl=id",
            'kemasan' => "https://www.google.com/search?q=" . urlencode("{$productName} kemasan label halal MUI") . "&hl=id&gl=id",
            'bpom' => "https://www.google.com/search?q=" . urlencode("{$productName} BPOM halal") . "&hl=id&gl=id",
        ];
    }

    /**
     * Transform service result to frontend expected format
     *
     * @param  array  $result
     * @param  string  $originalQuery
     * @return array
     */
    private function transformToFrontendFormat(array $result, string $originalQuery): array
    {
        // Ensure bahan is always a valid array with proper structure
        $bahanRaw = $result['bahan'] ?? [];
        $bahan = [];
        if (is_array($bahanRaw)) {
            foreach ($bahanRaw as $b) {
                if (is_array($b)) {
                    $bahan[] = [
                        'nama' => $b['nama'] ?? 'Bahan tidak diketahui',
                        'kategori' => $b['kategori'] ?? 'UTAMA',
                        'status_halal' => $b['status_halal'] ?? 'AMAN',
                        'keterangan' => $b['keterangan'] ?? '',
                    ];
                }
            }
        }

        // Ensure proses is always a valid array with proper structure
        $prosesRaw = $result['proses'] ?? [];
        $proses = [];
        if (is_array($prosesRaw)) {
            foreach ($prosesRaw as $p) {
                if (is_array($p)) {
                    $proses[] = [
                        'langkah' => $p['langkah'] ?? 0,
                        'nama' => $p['nama'] ?? 'Langkah tidak diketahui',
                        'deskripsi' => $p['deskripsi'] ?? '',
                        'titik_kritis' => $p['titik_kritis'] ?? false,
                    ];
                }
            }
        }

        // Ensure google_search is always a valid object
        $googleSearchRaw = $result['google_search'] ?? [];
        $googleSearch = is_array($googleSearchRaw) ? $googleSearchRaw : [];

        return [
            'kemasan' => is_string($result['kemasan'] ?? '') ? $result['kemasan'] : 'Silakan riset kemasan produk ini melalui Google Search',
            'bahan' => $bahan,
            'proses' => $proses,
            'catatan_halal' => is_string($result['catatan_halal'] ?? '') ? $result['catatan_halal'] : 'Lakukan riset bahan melalui Google Search',
            'status_halal' => $result['status_halal'] ?? 'PERLU_VERIFIKASI',
            'sertifikasi' => is_string($result['sertifikasi'] ?? '') ? $result['sertifikasi'] : 'Perlu sertifikasi MUI',
            'google_search_url' => $googleSearch,
            'sumber' => $result['sumber'] ?? 'google_search',
            'produk_matched' => $result['produk_matched'] ?? null,
            'found_in_database' => $result['found_in_database'] ?? false,
            'warning' => $result['warning'] ?? null,
            'analisis_id' => $result['analisis_id'] ?? null,
            'saved_to_database' => $result['saved_to_database'] ?? false,
        ];
    }
}
