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
    public int $tries   = 2;

    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $enumeratorId,
        // Data A
        private readonly int    $dataIdA,
        private readonly string $namaPuA,
        private readonly string $nikA,
        private readonly string $telephoneA,
        private readonly string $fotoPendampingA,
        private readonly string $pathA,
        // Data B
        private readonly int    $dataIdB,
        private readonly string $namaPuB,
        private readonly string $nikB,
        private readonly string $telephoneB,
        private readonly string $fotoPendampingB,
        private readonly string $pathB,
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Resize kedua foto di worker
        $base64A = $this->resizeAndEncode($this->pathA);
        $base64B = $this->resizeAndEncode($this->pathB);

        if (!$base64A || !$base64B) {
            return;
        }

        $result = $this->callClaude($base64A, $base64B);

        if (!$result) {
            return;
        }

        // Simpan dengan lock agar tidak race condition
        $lock = Cache::lock($this->sessionKey . '_lock', 5);
        try {
            $lock->block(5);

            $existing   = Cache::get($this->sessionKey, []);
            $existing[] = [
                'enumerator_id' => $this->enumeratorId,
                'data_a' => [
                    'id'              => $this->dataIdA,
                    'nama_pu'         => $this->namaPuA,
                    'nik'             => $this->nikA,
                    'telephone'       => $this->telephoneA,
                    'foto_pendamping' => $this->fotoPendampingA,
                ],
                'data_b' => [
                    'id'              => $this->dataIdB,
                    'nama_pu'         => $this->namaPuB,
                    'nik'             => $this->nikB,
                    'telephone'       => $this->telephoneB,
                    'foto_pendamping' => $this->fotoPendampingB,
                ],
                'confidence' => (int) ($result['confidence'] ?? 0),
                'match'      => (bool) ($result['match'] ?? false),
                'reason'     => $result['reason'] ?? '-',
            ];

            Cache::put($this->sessionKey, $existing, now()->addHours(3));
        } finally {
            $lock->release();
        }
    }

    private function callClaude(string $base64A, string $base64B): ?array
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
                                ['type' => 'text', 'text' => 'GAMBAR 1:'],
                                [
                                    'type'   => 'image',
                                    'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $base64A],
                                ],
                                ['type' => 'text', 'text' => 'GAMBAR 2:'],
                                [
                                    'type'   => 'image',
                                    'source' => ['type' => 'base64', 'media_type' => 'image/jpeg', 'data' => $base64B],
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
                    $wait = ($i + 1) * 20;
                    Log::warning("FaceMatchJob [{$this->dataIdA}↔{$this->dataIdB}]: rate limited, tunggu {$wait}s");
                    sleep($wait);
                    continue;
                }
                Log::warning("FaceMatchJob [{$this->dataIdA}↔{$this->dataIdB}]: " . $e->getMessage());
                return null;
            } catch (\Throwable $e) {
                Log::warning("FaceMatchJob [{$this->dataIdA}↔{$this->dataIdB}]: " . $e->getMessage());
                return null;
            }
        }

        return null;
    }

    private function resizeAndEncode(string $filePath): ?string
    {
        $info = @getimagesize($filePath);
        if (!$info) {
            return null;
        }

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
