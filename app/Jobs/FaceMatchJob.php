<?php

namespace App\Jobs;

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

    // Timeout per job (detik)
    public int $timeout = 60;

    // Coba ulang jika gagal
    public int $tries = 2;

    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $dataId,
        private readonly string $namaPu,
        private readonly string $nik,
        private readonly string $telephone,
        private readonly string $fotoPendamping,
        private readonly string $queryBase64,
        private readonly string $dbBase64,
    ) {}

    public function handle(): void
    {
        // Batalkan jika batch dibatalkan user
        if ($this->batch()?->cancelled()) {
            return;
        }

        $result = $this->callClaude();

        if (!$result) {
            return;
        }

        // Simpan hasil ke Cache (Redis/file), dikumpulkan di controller
        $existing   = Cache::get($this->sessionKey, []);
        $existing[] = [
            'data' => [
                'id'             => $this->dataId,
                'nama_pu'        => $this->namaPu,
                'nik'            => $this->nik,
                'telephone'      => $this->telephone,
                'foto_pendamping' => $this->fotoPendamping,
            ],
            'match'      => (bool) ($result['match'] ?? false),
            'confidence' => (int) ($result['confidence'] ?? 0),
            'reason'     => $result['reason'] ?? '-',
            'foto_url'   => asset('storage/' . $this->fotoPendamping),
        ];

        Cache::put($this->sessionKey, $existing, now()->addHours(2));
    }

    private function callClaude(): ?array
    {
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
                                'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $this->queryBase64],
                            ],
                            ['type' => 'text', 'text' => 'GAMBAR 2 (foto pendamping):'],
                            [
                                'type'   => 'image',
                                'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $this->dbBase64],
                            ],
                        ],
                    ]],
                ],
            ]);

            $body    = json_decode($response->getBody()->getContents(), true);
            $content = trim(preg_replace('/```json|```/', '', $body['content'][0]['text'] ?? ''));

            return json_decode($content, true) ?: null;
        } catch (\Throwable $e) {
            Log::warning("FaceMatchJob ID {$this->dataId}: " . $e->getMessage());
            return null;
        }
    }
}
