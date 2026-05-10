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

    public int $timeout = 60;
    public int $tries = 2;

    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $dataId,
        private readonly string $namaPu,
        private readonly string $nik,
        private readonly string $telephone,
        private readonly string $fotoPendamping,
        private readonly string $queryPath,  // ← path, bukan base64
        private readonly string $dbPath,     // ← path, bukan base64
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Resize di sini — dijalankan oleh worker, bukan web process
        $queryBase64 = $this->resizeAndEncode($this->queryPath);
        $dbBase64    = $this->resizeAndEncode($this->dbPath);

        if (!$queryBase64 || !$dbBase64) {
            return;
        }

        $result = $this->callClaude($queryBase64, $dbBase64);

        if (!$result) {
            return;
        }

        $lock = Cache::lock($this->sessionKey . '_lock', 5);
        try {
            $lock->block(5);
            $existing   = Cache::get($this->sessionKey, []);
            $existing[] = [
                'data' => [
                    'id'              => $this->dataId,
                    'nama_pu'         => $this->namaPu,
                    'nik'             => $this->nik,
                    'telephone'       => $this->telephone,
                    'foto_pendamping' => $this->fotoPendamping,
                ],
                'match'      => (bool) ($result['match'] ?? false),
                'confidence' => (int) ($result['confidence'] ?? 0),
                'reason'     => $result['reason'] ?? '-',
                'foto_pendamping' => $this->fotoPendamping, // path saja, URL di blade
            ];
            Cache::put($this->sessionKey, $existing, now()->addHours(2));
        } finally {
            $lock->release();
        }
    }

    private function callClaude(string $queryBase64, string $dbBase64): ?array
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
        } catch (\Throwable $e) {
            Log::warning("FaceMatchJob ID {$this->dataId}: " . $e->getMessage());
            return null;
        }
    }

    private function resizeAndEncode(string $filePath): ?string
    {
        $info = @getimagesize($filePath);
        if (!$info) return null;

        [$origW, $origH, $type] = $info;
        $max   = 768;
        $ratio = max($origW, $origH) > $max ? $max / max($origW, $origH) : 1.0;
        $newW  = (int) round($origW * $ratio);
        $newH  = (int) round($origH * $ratio);

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
            IMAGETYPE_PNG  => @imagecreatefrompng($filePath),
            IMAGETYPE_GIF  => @imagecreatefromgif($filePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($filePath),
            default        => null,
        };

        if (!$src) {
            $raw = @file_get_contents($filePath);
            return $raw ? base64_encode($raw) : null;
        }

        $dst = imagecreatetruecolor($newW, $newH);

        if ($type === IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefilledrectangle(
                $dst,
                0,
                0,
                $newW,
                $newH,
                imagecolorallocatealpha($dst, 255, 255, 255, 127)
            );
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagedestroy($src);

        ob_start();
        imagejpeg($dst, null, 80);
        $jpeg = ob_get_clean();
        imagedestroy($dst);

        return $jpeg ? base64_encode($jpeg) : null;
    }
}
