<?php

namespace App\Jobs;

use App\Http\Controllers\Api\FaceMatchController;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FaceMatchJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 2;

    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $dataId,
        private readonly string $namaPu,
        private readonly string $nik,
        private readonly string $telephone,
        private readonly string $fotoPendamping,
        private readonly string $queryPath,      // path absolut foto query
        private readonly string $dbPath,         // path absolut foto pendamping
        private readonly int    $enumeratorId,   // ← BARU
        private readonly string $namaEnumerator, // ← BARU
    ) {}

    /** Jumlah hasil ≥80% yang menjadi batas stop */
    const MATCH_LIMIT = 3;

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        // ── Early termination ────────────────────────────────────────────────
        // Cek dulu tanpa lock (cepat). Jika sudah cukup hasil ≥80%, skip job ini
        // tanpa membuang API call ke Claude sama sekali.
        if ($this->hasReachedLimit()) {
            return;
        }

        // Resize di worker — tidak membebani web process
        $queryBase64 = FaceMatchController::resizeAndEncode($this->queryPath);
        $dbBase64    = FaceMatchController::resizeAndEncode($this->dbPath);

        if (!$queryBase64 || !$dbBase64) {
            return;
        }

        $result = $this->callClaude($queryBase64, $dbBase64);

        if (!$result) {
            return;
        }

        // Simpan dengan lock agar tidak race condition
        $lock = Cache::lock($this->sessionKey . '_lock', 5);
        try {
            $lock->block(5);

            $existing = Cache::get($this->sessionKey, []);

            // Cek ulang di dalam lock — ada job lain yang mungkin sudah mengisi
            // slot terakhir tepat sebelum lock ini diperoleh.
            $matchCount = count(array_filter($existing, fn($r) => ($r['confidence'] ?? 0) >= 80));
            if ($matchCount >= self::MATCH_LIMIT) {
                return; // slot sudah penuh, buang hasil ini
            }

            $existing[] = [
                'data' => [
                    'id'              => $this->dataId,
                    'nama_pu'         => $this->namaPu,
                    'nik'             => $this->nik,
                    'telephone'       => $this->telephone,
                    'foto_pendamping' => $this->fotoPendamping,
                    'enumerator_id'   => $this->enumeratorId,
                    'nama_enumerator' => $this->namaEnumerator,
                ],
                'match'      => (bool) ($result['match'] ?? false),
                'confidence' => (int) ($result['confidence'] ?? 0),
                'reason'     => $result['reason'] ?? '-',
            ];
            Cache::put($this->sessionKey, $existing, now()->addHours(2));
        } finally {
            $lock->release();
        }
    }

    /**
     * Cek apakah hasil ≥80% sudah mencapai batas tanpa menggunakan lock
     * (baca cepat untuk early-exit sebelum resize & API call).
     */
    private function hasReachedLimit(): bool
    {
        $existing   = Cache::get($this->sessionKey, []);
        $matchCount = count(array_filter($existing, fn($r) => ($r['confidence'] ?? 0) >= 80));
        return $matchCount >= self::MATCH_LIMIT;
    }

    private function callClaude(string $queryBase64, string $dbBase64): ?array
    {
        $maxRetry = 3;
        for ($i = 0; $i < $maxRetry; $i++) {
            try {
                $client   = new Client(['timeout' => 50]);
                $response = $client->post('https://api.anthropic.com/v1/messages', [
                    'headers' => [
                        'x-api-key'         => env('ANTHROPIC_API_KEY'),
                        'anthropic-version' => '2023-06-01',
                        'content-type'      => 'application/json',
                    ],
                    'json' => [
                        'model'      => 'claude-sonnet-4-20250514',
                        'max_tokens' => 128,
                        'messages'   => [[
                            'role'    => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Bandingkan wajah GAMBAR 1 dan GAMBAR 2. Fokus hanya pada fitur wajah (bentuk, mata, hidung, mulut, alis, struktur tulang). Abaikan pencahayaan, sudut, usia, aksesori. Balas HANYA JSON tanpa teks lain: {"match":true/false,"confidence":0-100,"reason":"alasan singkat bahasa Indonesia"}',
                                ],
                                ['type' => 'text', 'text' => 'GAMBAR 1 (foto query):'],
                                [
                                    'type'   => 'image',
                                    'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $queryBase64],
                                ],
                                ['type' => 'text', 'text' => 'GAMBAR 2 (foto pendamping):'],
                                [
                                    'type'   => 'image',
                                    'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $dbBase64],
                                ],
                            ],
                        ]],
                    ],
                ]);

                $body    = json_decode($response->getBody()->getContents(), true);
                $content = trim(preg_replace('/```json|```/', '', $body['content'][0]['text'] ?? ''));

                return json_decode($content, true) ?: null;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                if ($e->getResponse()->getStatusCode() === 429) {
                    $wait = ($i + 1) * 20; // 20s, 40s, 60s
                    Log::warning("FaceMatchJob ID {$this->dataId}: rate limited, tunggu {$wait}s");
                    sleep($wait);
                    continue;
                }
                Log::warning("FaceMatchJob ID {$this->dataId}: " . $e->getMessage());
                return null;
            } catch (\Throwable $e) {
                Log::warning("FaceMatchJob ID {$this->dataId}: " . $e->getMessage());
                return null;
            }
        }

        return null;
    }
}
