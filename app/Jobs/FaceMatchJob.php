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

    public int $timeout = 30; // diperpendek: 60 → 30 dtk
    public int $tries   = 1;  // tidak retry: hemat waktu jika gagal

    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $dataId,
        private readonly string $namaPu,
        private readonly string $nik,
        private readonly string $telephone,
        private readonly string $fotoPendamping,
        private readonly string $dbPath,         // path absolut foto pendamping (query sudah di-cache)
        private readonly int    $enumeratorId,
        private readonly string $namaEnumerator,
    ) {}

    const MATCH_LIMIT = 3;

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Early exit tanpa lock — jika sudah cukup, batalkan batch sekalian
        if ($this->hasReachedLimit()) {
            $this->batch()?->cancel();
            return;
        }

        // Ambil base64 foto query dari cache (di-encode 1x saat dispatch, bukan per-job)
        $queryBase64 = Cache::get($this->sessionKey . '_query_b64');
        if (!$queryBase64) {
            return;
        }

        // Resize foto pendamping (ini memang harus per-job karena berbeda tiap job)
        $dbBase64 = FaceMatchController::resizeAndEncode($this->dbPath);
        if (!$dbBase64) {
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

            $existing   = Cache::get($this->sessionKey, []);
            $matchCount = count(array_filter($existing, fn($r) => ($r['confidence'] ?? 0) >= 80));

            // Cek ulang di dalam lock — slot mungkin sudah penuh
            if ($matchCount >= self::MATCH_LIMIT) {
                $this->batch()?->cancel();
                return;
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

            // Hitung ulang setelah insert — jika sudah penuh, cancel batch
            $newMatchCount = count(array_filter($existing, fn($r) => ($r['confidence'] ?? 0) >= 80));
            if ($newMatchCount >= self::MATCH_LIMIT) {
                $this->batch()?->cancel();
            }
        } finally {
            $lock->release();
        }
    }

    private function hasReachedLimit(): bool
    {
        $existing   = Cache::get($this->sessionKey, []);
        $matchCount = count(array_filter($existing, fn($r) => ($r['confidence'] ?? 0) >= 80));
        return $matchCount >= self::MATCH_LIMIT;
    }

    private function callClaude(string $queryBase64, string $dbBase64): ?array
    {
        try {
            $client   = new Client(['timeout' => 25]);
            $response = $client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key'         => env('ANTHROPIC_API_KEY'),
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => 'claude-haiku-4-5-20251001', // Haiku: lebih cepat & murah untuk vision task ini
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
            $status = $e->getResponse()->getStatusCode();
            if ($status === 429) {
                // Rate limit — release ke antrian supaya worker lain bisa ambil
                $this->release(15);
            }
            Log::warning("FaceMatchJob ID {$this->dataId}: HTTP {$status} " . $e->getMessage());
            return null;
        } catch (\Throwable $e) {
            Log::warning("FaceMatchJob ID {$this->dataId}: " . $e->getMessage());
            return null;
        }
    }
}
