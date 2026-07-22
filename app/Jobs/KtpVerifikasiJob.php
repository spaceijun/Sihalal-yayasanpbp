<?php

namespace App\Jobs;

use App\Http\Controllers\Api\FaceMatchController;
use App\Models\KtpVerifikasiSession;
use App\Models\Superadmin\Settingwebsite;
use GuzzleHttp\Client;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KtpVerifikasiJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 4;  // lebih banyak retry untuk handle 429

    /**
     * Delay antar retry (detik): [30s, 60s, 120s] — exponential backoff untuk 429.
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }

    /**
     * 1 job = 1 foto dari ZIP.
     * KTP dibaca dari disk (session folder) agar payload job kecil.
     *
     * @param string $sessionKey
     * @param int    $ktpCount   Jumlah KTP yang diupload (untuk baca file dari disk)
     * @param string $photoBase64 Base64 foto dari ZIP
     * @param string $namaFile   Nama file foto dari ZIP
     */
    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $ktpCount,
        private readonly string $photoBase64,
        private readonly string $namaFile,
    ) {}

    /**
     * Kunci cache unik per foto dalam session ini.
     * Digunakan untuk mencegah double-increment pada retry.
     */
    private function processedCacheKey(): string
    {
        return "ktp_ver_processed_{$this->sessionKey}_" . md5($this->namaFile);
    }

    /**
     * Increment 'processed' sekali per foto, tidak peduli berapa kali job diretry.
     * Capped agar tidak melebihi total_photos.
     */
    private function incrementProcessedOnce(): void
    {
        $cacheKey = $this->processedCacheKey();

        // Jika sudah pernah di-increment untuk foto ini, skip
        if (Cache::has($cacheKey)) {
            return;
        }

        // Tandai bahwa foto ini sudah di-increment (TTL 1 hari)
        Cache::put($cacheKey, 1, now()->addDay());

        // Increment tapi tidak melebihi total_photos
        $session = KtpVerifikasiSession::where('session_key', $this->sessionKey)->first();
        if ($session && $session->processed < $session->total_photos) {
            KtpVerifikasiSession::where('session_key', $this->sessionKey)
                ->where('processed', '<', $session->total_photos)
                ->increment('processed');
        }
    }

    // ── PROMPT: Multi-KTP vs 1 Foto ──────────────────────────────────────────

    private function buildPrompt(int $ktpCount): string
    {
        $labels = implode(', ', array_map(fn ($i) => "KTP-{$i}", range(0, $ktpCount - 1)));

        return <<<PROMPT
Anda adalah ahli forensik digital dan verifikasi identitas (KYC Specialist).

Anda menerima {$ktpCount} foto KTP (berlabel {$labels}) dan 1 "Foto Pendamping" yang akan dicocokkan.

Tugas: Tentukan KTP mana yang paling cocok dengan individu dalam Foto Pendamping.

Instruksi Analisis:
Langkah 1: Ekstrak nama lengkap dan NIK dari setiap KTP.
Langkah 2: Analisis wajah individu dalam Foto Pendamping.
Langkah 3: Bandingkan wajah Foto Pendamping dengan pasfoto pada setiap KTP menggunakan:
  - Geometri wajah (bentuk, tulang pipi, rahang)
  - Area periocular (mata, alis, kelopak)
  - Morfologi hidung dan mulut
  - Tanda khusus (tahi lalat, bekas luka)
  - Kesesuaian jenis kelamin (wajib sesuai)

Langkah 4: Pilih KTP dengan skor kemiripan biometrik tertinggi.

Balas HANYA dalam format JSON berikut tanpa teks lain:
{"best_ktp_index":0,"nama_ktp":"nama lengkap KTP terpilih","nik_ktp":"NIK KTP terpilih","confidence":0-100,"status":"Terverifikasi|Tidak Cocok|Keraguan Tinggi","justifikasi":"2-3 poin singkat bahasa Indonesia"}

Catatan: Gunakan best_ktp_index -1 jika tidak ada KTP yang cocok sama sekali (confidence < 25).
PROMPT;
    }

    // ── LOAD KTP DARI DISK ───────────────────────────────────────────────────

    /**
     * Baca dan encode semua KTP dari folder session di disk.
     * Tidak perlu pass base64 KTP via payload job — hemat memory queue.
     *
     * @return array  [ ['index'=>0, 'base64'=>'...'], ... ]
     */
    private function loadKtpImages(): array
    {
        $ktpImages = [];

        for ($i = 0; $i < $this->ktpCount; $i++) {
            // Cari file ktp_{i}.* (jpg/jpeg/png)
            $pattern = storage_path(
                "app/public/ktp-verifikasi-sessions/{$this->sessionKey}/ktp_{$i}.*"
            );
            $files = glob($pattern);

            if (empty($files)) {
                Log::warning("KtpVerifikasiJob: KTP file not found for index {$i} session {$this->sessionKey}");
                continue;
            }

            $b64 = FaceMatchController::resizeAndEncode($files[0]);
            if ($b64) {
                $ktpImages[] = ['index' => $i, 'base64' => $b64];
            }
        }

        return $ktpImages;
    }

    // ── GEMINI API CALL ──────────────────────────────────────────────────────

    private function callGemini(string $apiKey, array $ktpImages): ?array
    {
        // ── Rate throttle: max 1 request per 2 detik per session ──────────────
        // Mencegah jobs yang berjalan paralel flood Gemini API sekaligus.
        $throttleKey = "gemini_throttle_{$this->sessionKey}";
        $attempt = 0;
        while (Cache::has($throttleKey) && $attempt < 15) {
            sleep(2);
            $attempt++;
        }
        Cache::put($throttleKey, 1, now()->addSeconds(2));

        try {
            // Build input: prompt → setiap KTP → foto pendamping
            $input = [
                ['type' => 'text', 'text' => $this->buildPrompt(count($ktpImages))],
            ];

            foreach ($ktpImages as $ktp) {
                $input[] = ['type' => 'text',  'text'      => "KTP-{$ktp['index']}:"];
                $input[] = ['type' => 'image', 'data'      => $ktp['base64'],
                                               'mime_type' => 'image/jpeg'];
            }

            $input[] = ['type' => 'text',  'text'      => 'Foto Pendamping (yang akan dicocokkan):'];
            $input[] = ['type' => 'image', 'data'      => $this->photoBase64,
                                           'mime_type' => 'image/jpeg'];

            $client   = new Client(['timeout' => 55]);
            $response = $client->post(
                'https://generativelanguage.googleapis.com/v1beta/interactions',
                [
                    'headers' => [
                        'x-goog-api-key' => $apiKey,
                        'Content-Type'   => 'application/json',
                    ],
                    'json' => [
                        'model' => 'gemini-3.5-flash',
                        'input' => $input,
                    ],
                ]
            );

            $body = json_decode($response->getBody()->getContents(), true);

            $text = '';
            foreach ($body['steps'] ?? [] as $step) {
                if (($step['type'] ?? '') === 'model_output') {
                    foreach ($step['content'] ?? [] as $part) {
                        if (($part['type'] ?? '') === 'text') {
                            $text .= $part['text'];
                        }
                    }
                }
            }
            if (empty($text)) {
                $text = $body['output_text'] ?? '';
            }

            $text   = preg_replace('/```json\s*/i', '', trim($text));
            $text   = preg_replace('/```\s*/', '', $text);
            $parsed = json_decode(trim($text), true);

            return is_array($parsed) ? $parsed : null;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            if ($status === 429) {
                // Rate limit — baca Retry-After header jika ada
                $retryAfter = (int) ($e->getResponse()->getHeaderLine('Retry-After') ?: 30);
                Log::warning("KtpVerifikasiJob 429 [{$this->namaFile}]: Rate limited. Retry after {$retryAfter}s. Attempt {$this->attempts()}/{$this->tries}");

                // Lempar exception agar job masuk antrian retry dengan backoff
                // (JANGAN increment processed — foto ini belum diproses)
                $this->release($retryAfter > 0 ? $retryAfter : 30);
                return null;  // return null di sini tapi release sudah handle retry
            }

            Log::warning("KtpVerifikasiJob Gemini HTTP {$status} [{$this->namaFile}]: " . $e->getMessage());
            return null;

        } catch (\Throwable $e) {
            Log::warning("KtpVerifikasiJob Gemini error [{$this->namaFile}]: " . $e->getMessage());
            return null;
        }
    }

    // ── HANDLE ───────────────────────────────────────────────────────────────

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $session = KtpVerifikasiSession::where('session_key', $this->sessionKey)->first();
        if (! $session || $session->status === 'failed') {
            return;
        }

        // Load semua KTP dari disk (bukan dari payload job)
        $ktpImages = $this->loadKtpImages();
        if (empty($ktpImages)) {
            $this->incrementProcessedOnce();
            return;
        }

        $apiKey = (string) (Settingwebsite::value('gemini_api_key') ?? '');
        $result = $this->callGemini($apiKey, $ktpImages);

        // Jika callGemini memanggil $this->release() (kasus 429), job akan di-retry.
        // Cek apakah job sudah di-release — jika ya, jangan increment processed.
        if ($this->isReleased()) {
            return;
        }

        // Increment counter sekali per foto (guard terhadap retry double-count)
        $this->incrementProcessedOnce();

        if (! $result) {
            return;
        }

        $bestKtpIndex = (int) ($result['best_ktp_index'] ?? -1);

        // Abaikan jika AI tidak menemukan kecocokan
        if ($bestKtpIndex < 0 || $bestKtpIndex >= $this->ktpCount) {
            return;
        }

        // Gunakan lock untuk mencegah race condition saat update JSON
        Cache::lock("ktp_ver_{$this->sessionKey}", 10)->block(8, function () use ($result, $bestKtpIndex) {
            $session = KtpVerifikasiSession::where('session_key', $this->sessionKey)->first();
            if (! $session) {
                return;
            }

            $existing = $session->results;
            if (is_string($existing)) {
                $existing = json_decode($existing, true) ?? [];
            }
            if (! is_array($existing)) {
                $existing = [];
            }

            // Pastikan slot KTP ada (seharusnya sudah diinisialisasi oleh controller)
            if (! isset($existing[$bestKtpIndex])) {
                return;
            }

            // Isi nama & NIK dari KTP yang dicocokkan (ambil dari hasil pertama)
            if (empty($existing[$bestKtpIndex]['ktp_nama']) && ! empty($result['nama_ktp'])) {
                $existing[$bestKtpIndex]['ktp_nama'] = $result['nama_ktp'];
                $existing[$bestKtpIndex]['ktp_nik']  = $result['nik_ktp'] ?? null;
            }

            // Tambahkan foto ini ke pool kandidat KTP yang paling cocok
            // Exclusivity sudah inherent: 1 foto → 1 API call → 1 KTP saja
            $existing[$bestKtpIndex]['top_candidates'][] = [
                'nama_file'   => $this->namaFile,
                'foto_base64' => 'data:image/jpeg;base64,' . $this->photoBase64,
                'confidence'  => (int) ($result['confidence'] ?? 0),
                'status'      => $result['status']      ?? 'Tidak Cocok',
                'justifikasi' => $result['justifikasi'] ?? '-',
            ];

            // Sort dan simpan top 10 sementara (trim ke 3 di finalization)
            usort(
                $existing[$bestKtpIndex]['top_candidates'],
                fn ($a, $b) => $b['confidence'] - $a['confidence']
            );
            $existing[$bestKtpIndex]['top_candidates'] = array_slice(
                $existing[$bestKtpIndex]['top_candidates'], 0, 10
            );

            $session->results = $existing;
            if ($bestKtpIndex === 0 && empty($session->ktp_nama) && ! empty($result['nama_ktp'])) {
                $session->ktp_nama = $result['nama_ktp'];
                $session->ktp_nik  = $result['nik_ktp'];
            }
            $session->save();
        });
    }

    // ── FAILED ───────────────────────────────────────────────────────────────

    public function failed(\Throwable $e): void
    {
        Log::error("KtpVerifikasiJob failed [{$this->namaFile}]: " . $e->getMessage());
        // Gunakan incrementProcessedOnce agar retry tidak double-count
        $this->incrementProcessedOnce();
    }
}
