<?php

namespace App\Services\Integrasi;

use App\Models\Superadmin\Settingwebsite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * UrusinService
 *
 * HTTP client untuk API pihak ketiga "Urusin Secara Online" — layanan pengurusan NIB (OSS)
 * dan Sertifikat Halal. Spesifikasi lengkap ada di .agent/workflows/data-entry-integrasi.md §3.
 *
 * Konfigurasi (base URL + API key) disimpan di database (tabel settingwebsites, diisi lewat
 * Superadmin > Setting Website > tab API Keys), bukan di .env — mengikuti pola yang sudah ada
 * untuk Gemini/Anthropic API key (lihat SettingwebsiteService) dan WA Gateway (KawuloHalalService).
 *
 * Semua method publik mengembalikan array bentuk seragam:
 *   ['status' => bool, 'data' => array|null, 'error' => string|null]
 * mengikuti konvensi KawuloHalalService::makeApiRequest().
 */
class UrusinService
{
    protected ?string $baseUrl;

    protected ?string $apiKey;

    public function __construct()
    {
        $setting = Settingwebsite::first();

        $baseUrl = $setting?->urusin_base_url ? rtrim($setting->urusin_base_url, '/') : null;

        // Toleransi kesalahan konfigurasi umum: jika admin mengisi Base URL yang sudah menyertakan
        // "/api/v1" (mis. menyalin langsung dari contoh dokumentasi mereka), endpoint() di bawah
        // akan menambahkan "/api/v1/" lagi sehingga jadi ".../api/v1/api/v1/nib/submit" (404).
        // Ditemukan nyata di storage/logs/laravel.log saat test sandbox — dinormalisasi di sini
        // supaya baik base URL polos maupun yang menyertakan /api/v1 sama-sama berfungsi.
        if ($baseUrl && preg_match('#/api/v1$#i', $baseUrl)) {
            $baseUrl = preg_replace('#/api/v1$#i', '', $baseUrl);
        }

        $this->baseUrl = $baseUrl;
        $this->apiKey = $setting?->urusin_api_key ?: null;
    }

    /**
     * Apakah base URL & API key sudah dikonfigurasi.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->baseUrl) && ! empty($this->apiKey);
    }

    /**
     * Deteksi mode dari prefix API key (§3.2 data-entry-integrasi.md).
     * Tidak mempengaruhi request — hanya untuk ditampilkan di UI.
     */
    public function detectMode(): ?string
    {
        if (! $this->apiKey) {
            return null;
        }

        if (str_starts_with($this->apiKey, 'ua_sandbox_')) {
            return 'sandbox';
        }

        if (str_starts_with($this->apiKey, 'ua_live_')) {
            return 'production';
        }

        return 'unknown';
    }

    protected function endpoint(string $path): string
    {
        return $this->baseUrl.'/api/v1/'.ltrim($path, '/');
    }

    /**
     * Request JSON standar (GET/POST) dengan Bearer auth.
     */
    protected function request(string $method, string $path, array $payload = []): array
    {
        if (! $this->isConfigured()) {
            return ['status' => false, 'data' => null, 'error' => 'Urusin Secara Online belum dikonfigurasi (base URL / API key kosong).'];
        }

        try {
            $http = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(20)
                ->connectTimeout(8);

            $response = match (strtoupper($method)) {
                'GET' => $http->get($this->endpoint($path), $payload),
                'POST' => $http->post($this->endpoint($path), $payload),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            return $this->handleJsonResponse($response, $path, $method);
        } catch (\Exception $e) {
            Log::error('UrusinService: exception', ['path' => $path, 'message' => $e->getMessage()]);

            return ['status' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Request `multipart/form-data` dengan Bearer auth — dipakai untuk endpoint submit yang
     * mewajibkan lampiran foto (§3.5/§3.6/§3.7 data-entry-integrasi.md, ditambahkan setelah
     * revisi dokumentasi terbaru mengubah nib/submit & halal/submit dari JSON ke multipart
     * dengan foto wajib). $files berbentuk daftar ['name' => ..., 'path' => absolute path,
     * 'filename' => nama file terkirim].
     */
    protected function requestMultipart(string $method, string $path, array $fields, array $files = []): array
    {
        if (! $this->isConfigured()) {
            return ['status' => false, 'data' => null, 'error' => 'Urusin Secara Online belum dikonfigurasi (base URL / API key kosong).'];
        }

        try {
            $http = Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout(30)
                ->connectTimeout(8);

            foreach ($files as $file) {
                $http = $http->attach($file['name'], file_get_contents($file['path']), $file['filename'] ?? basename($file['path']));
            }

            $response = match (strtoupper($method)) {
                'POST' => $http->post($this->endpoint($path), $this->flattenForMultipart($fields)),
                default => throw new \InvalidArgumentException("Unsupported multipart method: {$method}"),
            };

            return $this->handleJsonResponse($response, $path, $method);
        } catch (\Exception $e) {
            Log::error('UrusinService: exception (multipart)', ['path' => $path, 'message' => $e->getMessage()]);

            return ['status' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ratakan array asosiatif/nested jadi pasangan `key => value` bernotasi kurung siku
     * (mis. `products[0][nama_produk]`). Dibutuhkan karena body `multipart` pada
     * `Http::post()` TIDAK meratakan array nested secara otomatis seperti body JSON biasa —
     * tanpa ini, field seperti `products` dikirim rusak/tidak terbaca sisi server.
     */
    private function flattenForMultipart(array $data, string $prefix = ''): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $fullKey = $prefix === '' ? (string) $key : "{$prefix}[{$key}]";

            if (is_array($value)) {
                $result += $this->flattenForMultipart($value, $fullKey);
            } elseif ($value !== null) {
                $result[$fullKey] = (string) $value;
            }
        }

        return $result;
    }

    private function handleJsonResponse($response, string $path, string $method): array
    {
        $json = $response->json();

        $logContext = [
            'endpoint' => $this->endpoint($path),
            'method' => $method,
            'http_status' => $response->status(),
        ];

        if ($response->failed() || ($json['success'] ?? null) === false) {
            Log::warning('UrusinService: request ditolak', $logContext + ['response' => $json]);

            return [
                'status' => false,
                'data' => $json['data'] ?? null,
                'error' => $json['message'] ?? ('HTTP '.$response->status()),
                'http_status' => $response->status(),
                'errors' => $json['errors'] ?? null,
            ];
        }

        Log::info('UrusinService: request berhasil', $logContext);

        return ['status' => true, 'data' => $json['data'] ?? null, 'error' => null];
    }

    /**
     * GET /api/v1/me — info & status API key (§3.4).
     */
    public function me(): array
    {
        return $this->request('GET', 'me');
    }

    /**
     * POST /api/v1/nib/submit (§3.5). Sejak revisi dokumentasi terbaru, endpoint ini
     * `multipart/form-data` dan mewajibkan lampiran `ktp_photo` — $files diteruskan ke
     * requestMultipart() (lihat UrusinSubmissionService::buildKtpFile()).
     */
    public function submitNib(array $payload, array $files = []): array
    {
        return $this->requestMultipart('POST', 'nib/submit', $payload, $files);
    }

    /**
     * POST /api/v1/bundle/submit — submit NIB + Halal sekaligus dalam satu request (§3.7).
     * Dipakai sebagai jalur submit utama karena sistem kita selalu mengirim NIB & Halal
     * bersamaan — menggantikan pola submitNib()+submitHalal() terpisah.
     *
     * Dikirim sebagai `multipart/form-data` dengan lampiran `ktp_photo`/`products[i][foto_produk]`
     * mengikuti pola §3.5/§3.6, WALAUPUN contoh dokumentasi Bundle sendiri masih JSON tanpa foto
     * — ini keputusan implementasi kita untuk mengatasi 500 berulang, belum dikonfirmasi resmi
     * oleh Urusin Secara Online. Lihat catatan lengkap di §3.7 data-entry-integrasi.md.
     */
    public function submitBundle(array $payload, array $files = []): array
    {
        return $this->requestMultipart('POST', 'bundle/submit', $payload, $files);
    }

    /**
     * GET /api/v1/bundle/{nib_submission_id}/status — cek status NIB & Halal sekaligus dalam
     * satu request, dikunci oleh submission_id sisi NIB (§3.7).
     */
    public function statusBundle(string $nibSubmissionId): array
    {
        return $this->request('GET', "bundle/{$nibSubmissionId}/status");
    }

    /**
     * GET /api/v1/nib/{submission_id} (§3.5).
     */
    public function statusNib(string $submissionId): array
    {
        return $this->request('GET', "nib/{$submissionId}");
    }

    /**
     * GET /api/v1/nib/{submission_id}/download — mengembalikan binary body, bukan JSON (§3.5).
     */
    public function downloadNib(string $submissionId): array
    {
        return $this->downloadBinary("nib/{$submissionId}/download");
    }

    /**
     * POST /api/v1/halal/submit (§3.6). Sejak revisi dokumentasi terbaru, endpoint ini
     * `multipart/form-data` dan mewajibkan lampiran `products[i][foto_produk]` per produk —
     * $files diteruskan ke requestMultipart() (lihat UrusinSubmissionService::buildProductFiles()).
     */
    public function submitHalal(array $payload, array $files = []): array
    {
        return $this->requestMultipart('POST', 'halal/submit', $payload, $files);
    }

    /**
     * GET /api/v1/halal/{submission_id} (§3.6).
     */
    public function statusHalal(string $submissionId): array
    {
        return $this->request('GET', "halal/{$submissionId}");
    }

    /**
     * GET /api/v1/halal/{submission_id}/download — binary (§3.6).
     */
    public function downloadHalal(string $submissionId): array
    {
        return $this->downloadBinary("halal/{$submissionId}/download");
    }

    /**
     * Unduh file biner (PDF) dari endpoint download. Mengembalikan isi file mentah di 'data'
     * (bukan array JSON) jika berhasil.
     */
    protected function downloadBinary(string $path): array
    {
        if (! $this->isConfigured()) {
            return ['status' => false, 'data' => null, 'error' => 'Urusin Secara Online belum dikonfigurasi.'];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->connectTimeout(8)
                ->get($this->endpoint($path));

            if ($response->failed()) {
                Log::warning('UrusinService: gagal download dokumen', [
                    'endpoint' => $this->endpoint($path),
                    'http_status' => $response->status(),
                ]);

                return ['status' => false, 'data' => null, 'error' => 'HTTP '.$response->status()];
            }

            return [
                'status' => true,
                'data' => $response->body(),
                'content_type' => $response->header('Content-Type'),
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('UrusinService: exception saat download', ['path' => $path, 'message' => $e->getMessage()]);

            return ['status' => false, 'data' => null, 'error' => $e->getMessage()];
        }
    }
}
