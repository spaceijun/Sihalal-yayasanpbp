<?php

namespace App\Services;

use App\Models\Superadmin\Settingwebsite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiOcrService
 *
 * Menggunakan Google Gemini API untuk mengekstrak
 * data dari foto KTP Indonesia dengan akurasi tinggi (~95-99%).
 *
 * Dokumentasi resmi: https://ai.google.dev/gemini-api/docs/get-started
 * Model: gemini-3.6-flash (terbaru GA, Juli 2026)
 * Endpoint: POST /v1beta/models/{model}:generateContent
 *
 * CATATAN: Model yang deprecated akan langsung di-skip (404).
 * Updated: Juli 2026 - gemini-2.x dan gemini-3-flash sudah tidak tersedia.
 */
class GeminiOcrService
{
    /**
     * Urutan model yang dicoba, dari yang paling diutamakan.
     * Referensi: https://ai.google.dev/gemini-api/docs/models (Juli 2026)
     *
     * ⚠️ PERHATIAN: Google sering deprecated model lama.
     * Jika model pertama error 404, berarti sudah dihapus - perlu update.
     *
     * gemini-3.6-flash       — GA terbaru, high efficiency
     * gemini-3.5-flash       — GA stable, balanced
     * gemini-3.5-flash-lite  — GA stable, budget/low-latency
     * gemini-3.1-flash-lite  — GA stable, oldest but most compatible
     */
    private const MODEL_CHAIN = [
        'gemini-3.6-flash',       // Primary: GA terbaru
        'gemini-3.5-flash',       // Fallback 1
        'gemini-3.5-flash-lite',  // Fallback 2
        'gemini-3.1-flash-lite',  // Fallback 3: oldest but most compatible
    ];

    /**
     * Status HTTP yang dianggap sementara → perlu retry/fallback (bukan error permanen)
     */
    private const RETRYABLE_STATUSES = [429, 500, 502, 503, 504];

    /**
     * Jumlah retry maksimal per model sebelum pindah ke fallback berikutnya
     */
    private const MAX_RETRY_PER_MODEL = 2;

    protected ?string $apiKey = null;

    public function __construct()
    {
        $setting      = Settingwebsite::first();
        $this->apiKey = $setting?->gemini_api_key ?? env('GEMINI_API_KEY', '');
    }

    /**
     * Cek apakah Gemini API key sudah terkonfigurasi
     */
    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }

    /**
     * Wrapper backward-compatible (dipanggil OcrController & BackfillKtpOcr).
     *
     * @param  string  $base64Image  Base64-encoded image (tanpa prefix data:image/...)
     * @return array ['success' => bool, 'data' => [...] | 'error' => string]
     */
    public function scanKtp(string $base64Image): array
    {
        return $this->extractKtpData($base64Image);
    }

    /**
     * Ekstrak data KTP dari gambar menggunakan Gemini generateContent API.
     * Mencoba setiap model dalam MODEL_CHAIN secara berurutan.
     * Untuk setiap model, melakukan retry dengan exponential backoff pada error sementara (503, 429, dll).
     *
     * @param  string  $imageBase64  Base64-encoded image (tanpa prefix data:image/...)
     * @param  string  $mimeType     MIME type gambar (image/jpeg, image/png)
     * @return array ['success' => bool, 'data' => [...] | 'error' => string]
     */
    public function extractKtpData(string $imageBase64, string $mimeType = 'image/jpeg'): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error'   => 'Gemini API Key belum dikonfigurasi. Silakan isi di menu Setting Website.',
            ];
        }

        $prompt    = $this->buildPrompt();
        $lastError = 'Semua model tidak tersedia';

        foreach (self::MODEL_CHAIN as $model) {
            $result = $this->tryModelWithRetry($model, $imageBase64, $mimeType, $prompt);

            if ($result['success']) {
                return $result;
            }

            // Jika auth error (401/403) → API key salah, jangan coba model lain
            if (($result['auth_error'] ?? false)) {
                return [
                    'success' => false,
                    'error'   => $result['error'],
                ];
            }

            $lastError = $result['error'] ?? $lastError;
        }

        Log::error('GeminiOcrService: Semua model dalam chain gagal', [
            'models_tried' => self::MODEL_CHAIN,
            'last_error'   => $lastError,
        ]);

        return [
            'success' => false,
            'error'   => 'Layanan OCR sementara tidak tersedia. Silakan coba lagi dalam beberapa menit. ('.$lastError.')',
        ];
    }

    /**
     * Coba satu model dengan retry exponential backoff untuk error sementara (503, 429, dll).
     *
     * @return array dengan key 'success', 'error' (opsional), 'auth_error' (opsional)
     */
    private function tryModelWithRetry(
        string $model,
        string $imageBase64,
        string $mimeType,
        string $prompt
    ): array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        for ($attempt = 1; $attempt <= self::MAX_RETRY_PER_MODEL; $attempt++) {
            try {
                $response = Http::timeout(60)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url . '?key=' . $this->apiKey, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt],
                                    [
                                        'inline_data' => [
                                            'mime_type' => $mimeType,
                                            'data'      => $imageBase64,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature'      => 0.1,
                            'maxOutputTokens'  => 512,
                            'responseMimeType' => 'application/json',
                            // Nonaktifkan "Thinking" mode — penyebab latency 20-40 detik
                            // Ref: https://ai.google.dev/gemini-api/docs/thinking
                            'thinkingConfig'   => [
                                'thinkingBudget' => 0,
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $body    = $response->json();
                    $rawText = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

                    if ($rawText) {
                        if ($attempt > 1 || $model !== self::MODEL_CHAIN[0]) {
                            Log::info('GeminiOcrService: Berhasil', [
                                'model'   => $model,
                                'attempt' => $attempt,
                            ]);
                        }

                        return $this->parseAndSanitize($rawText);
                    }

                    // Response 200 tapi teks kosong — tidak perlu retry
                    return [
                        'success' => false,
                        'error'   => 'Gemini tidak menghasilkan teks. Coba upload foto yang lebih jelas.',
                    ];
                }

                $status    = $response->status();
                $errorBody = $response->json();
                $errorMsg  = $errorBody['error']['message'] ?? ('HTTP '.$status);

                // Auth error → jangan coba lagi (model manapun)
                if ($status === 401 || $status === 403) {
                    Log::error('GeminiOcrService: Auth error', ['model' => $model, 'status' => $status]);
                    return [
                        'success'    => false,
                        'auth_error' => true,
                        'error'      => 'API Key tidak valid atau tidak memiliki akses: '.$errorMsg,
                    ];
                }

                // Model tidak ada (404) → langsung skip ke model berikutnya
                if ($status === 404) {
                    Log::warning('GeminiOcrService: Model tidak tersedia (404)', ['model' => $model]);
                    return [
                        'success' => false,
                        'error'   => "Model {$model} tidak tersedia (404)",
                    ];
                }

                // Error sementara (503/429/500) → retry dengan backoff
                if (in_array($status, self::RETRYABLE_STATUSES)) {
                    Log::error('Gemini OCR Error', [
                        'status'  => $status,
                        'body'    => $response->body(),
                        'model'   => $model,
                        'attempt' => "{$attempt}/".self::MAX_RETRY_PER_MODEL,
                    ]);

                    if ($attempt < self::MAX_RETRY_PER_MODEL) {
                        // Exponential backoff: attempt 1 → 2 detik, attempt 2 → 4 detik
                        $sleepSeconds = pow(2, $attempt);
                        Log::info("GeminiOcrService: Retry dalam {$sleepSeconds} detik", [
                            'model'   => $model,
                            'attempt' => $attempt,
                        ]);
                        sleep($sleepSeconds);
                        continue; // retry
                    }

                    // Habis retry → fallback ke model berikutnya
                    return [
                        'success' => false,
                        'error'   => "{$model} overloaded ({$status}): {$errorMsg}",
                    ];
                }

                // Error lain yang tidak dikenal → skip ke model berikutnya
                Log::warning('GeminiOcrService: Error tidak dikenal', [
                    'model'  => $model,
                    'status' => $status,
                    'msg'    => $errorMsg,
                ]);
                return [
                    'success' => false,
                    'error'   => "Model {$model} error {$status}: {$errorMsg}",
                ];

            } catch (\Exception $e) {
                Log::error('GeminiOcrService: Exception', [
                    'model'   => $model,
                    'attempt' => $attempt,
                    'message' => $e->getMessage(),
                ]);

                if ($attempt < self::MAX_RETRY_PER_MODEL) {
                    sleep(pow(2, $attempt));
                    continue;
                }

                return [
                    'success' => false,
                    'error'   => 'Gagal menghubungi Gemini API: '.$e->getMessage(),
                ];
            }
        }

        // Seharusnya tidak pernah sampai sini
        return ['success' => false, 'error' => 'Retry loop habis'];
    }

    /**
     * Bangun prompt OCR untuk KTP Indonesia
     */
    private function buildPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah sistem OCR untuk KTP Indonesia. Ekstrak data dari gambar KTP ini dan kembalikan HANYA JSON valid.

Aturan penting:
- NIK harus TEPAT 16 digit angka (jangan ada spasi atau karakter lain)
- Tanggal lahir format: DD-MM-YYYY
- RT dan RW harus 3 digit (dengan leading zero, contoh: 005, 012)
- Jika field tidak terbaca, isi dengan null
- HANYA kembalikan JSON, tidak ada teks lain sebelum atau sesudah JSON

Format JSON yang diharapkan:
{
  "nik": "1234567890123456",
  "nama": "NAMA LENGKAP",
  "tempat_lahir": "NAMA KOTA",
  "tanggal_lahir": "01-01-1990",
  "jenis_kelamin": "LAKI-LAKI atau PEREMPUAN",
  "alamat": "NAMA JALAN DAN NOMOR",
  "rt": "001",
  "rw": "002",
  "kelurahan": "NAMA KELURAHAN",
  "kecamatan": "NAMA KECAMATAN",
  "kabupaten": "NAMA KABUPATEN/KOTA",
  "provinsi": "NAMA PROVINSI",
  "agama": "ISLAM/KRISTEN/dll",
  "status_perkawinan": "BELUM KAWIN/KAWIN/CERAI HIDUP/CERAI MATI",
  "pekerjaan": "NAMA PEKERJAAN",
  "kewarganegaraan": "WNI",
  "berlaku_hingga": "SEUMUR HIDUP atau tanggal"
}
PROMPT;
    }

    /**
     * Test koneksi ke Gemini API — coba semua model dalam MODEL_CHAIN.
     */
    public function testConnection(): array
    {
        if (! $this->isConfigured()) {
            return [
                'ok'    => false,
                'error' => 'Gemini API Key belum dikonfigurasi.',
            ];
        }

        $lastError = 'Tidak diketahui';

        foreach (self::MODEL_CHAIN as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

                $response = Http::timeout(20)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url . '?key=' . $this->apiKey, [
                        'contents' => [
                            [
                                'parts' => [['text' => 'Reply with exactly one word: OK']],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature'     => 0,
                            'maxOutputTokens' => 10,
                        ],
                    ]);

                if ($response->successful()) {
                    Log::info('GeminiOcrService: testConnection success', ['model' => $model]);
                    return [
                        'ok'      => true,
                        'message' => "Koneksi berhasil! Model aktif: <strong>{$model}</strong>. API Key valid dan siap digunakan untuk Scan KTP.",
                    ];
                }

                $status    = $response->status();
                $errorBody = $response->json();
                $errorMsg  = $errorBody['error']['message'] ?? ('HTTP '.$status);
                $lastError = "[{$model}] {$errorMsg}";

                Log::warning('GeminiOcrService: testConnection model failed', [
                    'model'   => $model,
                    'status'  => $status,
                    'message' => $errorMsg,
                ]);

                if ($status === 401 || $status === 403) {
                    return [
                        'ok'    => false,
                        'error' => 'API Key tidak valid atau tidak memiliki akses. Pesan: '.$errorMsg,
                    ];
                }

            } catch (\Exception $e) {
                Log::error('GeminiOcrService: testConnection exception', [
                    'model'   => $model,
                    'message' => $e->getMessage(),
                ]);
                return [
                    'ok'    => false,
                    'error' => 'Gagal terhubung ke server Gemini. Detail: '.$e->getMessage(),
                ];
            }
        }

        return [
            'ok'    => false,
            'error' => 'Semua model Gemini gagal. Error terakhir: <code>'.htmlspecialchars($lastError).'</code><br>'
                . 'Kemungkinan penyebab:<br>'
                . '• API Key salah atau sudah expired<br>'
                . '• Akun Google AI Studio belum aktif<br>'
                . '• Coba buat API Key baru di <a href="https://aistudio.google.com/apikey" target="_blank">aistudio.google.com/apikey</a>',
        ];
    }

    /**
     * Parse JSON dari teks Gemini dan sanitasi hasilnya
     */
    protected function parseAndSanitize(string $rawText): array
    {
        $rawText = preg_replace('/```json\s*/i', '', $rawText);
        $rawText = preg_replace('/```\s*/i', '', $rawText);
        $rawText = trim($rawText);

        $parsed = json_decode($rawText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $repaired = $this->repairTruncatedJson($rawText);
            $parsed   = json_decode($repaired, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('GeminiOcrService: JSON parse error', [
                    'raw'      => substr($rawText, 0, 500),
                    'repaired' => substr($repaired ?? '', 0, 500),
                ]);
                return [
                    'success' => false,
                    'error'   => 'Gagal membaca hasil dari Gemini. Format respons tidak valid. Coba upload foto yang lebih jelas.',
                ];
            }

            Log::info('GeminiOcrService: JSON diperbaiki otomatis (truncated response)');
        }

        return [
            'success' => true,
            'data'    => $this->sanitizeKtpData($parsed),
        ];
    }

    /**
     * Perbaiki JSON yang terpotong akibat maxOutputTokens tercapai.
     */
    protected function repairTruncatedJson(string $raw): string
    {
        $raw     = rtrim($raw, ', ');
        $stripped = preg_replace('/\\\\"/', '', $raw);
        $quoteCount = substr_count($stripped, '"');

        if ($quoteCount % 2 !== 0) {
            $raw .= '"';
        }

        if (! str_ends_with(rtrim($raw), '}')) {
            $raw .= '}';
        }

        return $raw;
    }

    /**
     * Sanitasi dan normalisasi data KTP hasil parsing Gemini
     */
    protected function sanitizeKtpData(array $data): array
    {
        // NIK: pastikan 16 digit bersih
        if (isset($data['nik'])) {
            $data['nik'] = preg_replace('/\D/', '', (string) $data['nik']);
            if (strlen($data['nik']) !== 16) {
                $data['nik'] = null;
            }
        }

        // Nama: huruf kapital semua
        if (! empty($data['nama'])) {
            $data['nama'] = strtoupper(trim($data['nama']));
        }

        // RT/RW: pastikan 3 digit dengan leading zero
        foreach (['rt', 'rw'] as $field) {
            if (! empty($data[$field])) {
                $num          = preg_replace('/\D/', '', (string) $data[$field]);
                $data[$field] = $num ? str_pad($num, 3, '0', STR_PAD_LEFT) : null;
            }
        }

        // Tanggal lahir: normalisasi ke DD-MM-YYYY
        if (! empty($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = $this->normalizeTanggalLahir($data['tanggal_lahir']);
        }

        // Uppercase untuk field-field lokasi
        foreach (['tempat_lahir', 'alamat', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'pekerjaan'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = strtoupper(trim($data[$field]));
            }
        }

        return $data;
    }

    /**
     * Normalisasi format tanggal lahir ke DD-MM-YYYY
     */
    protected function normalizeTanggalLahir(string $tgl): string
    {
        // Format DD-MM-YYYY atau DD/MM/YYYY
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $tgl, $m)) {
            return sprintf('%02d-%02d-%s', $m[1], $m[2], $m[3]);
        }

        // Format YYYY-MM-DD
        if (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/', $tgl, $m)) {
            return sprintf('%02d-%02d-%s', $m[3], $m[2], $m[1]);
        }

        return $tgl;
    }
}
