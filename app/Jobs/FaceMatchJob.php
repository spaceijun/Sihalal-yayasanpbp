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

    public int $timeout = 30;
    public int $tries   = 1;

    public function __construct(
        private readonly string $sessionKey,
        private readonly int    $dataId,
        private readonly string $namaPu,
        private readonly string $nik,
        private readonly string $telephone,
        private readonly string $fotoPendamping,
        private readonly string $dbPath,
        private readonly int    $enumeratorId,
        private readonly string $namaEnumerator,
        // Usia foto dalam bulan — dihitung dari mtime file saat dispatch
        private readonly int    $queryAgeMonths = 0,
        private readonly int    $dbAgeMonths    = 0,
    ) {}

    const MATCH_LIMIT = 3;

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        if ($this->hasReachedLimit()) {
            $this->batch()?->cancel();
            return;
        }

        $queryBase64 = Cache::get($this->sessionKey . '_query_b64');
        if (!$queryBase64) {
            return;
        }

        $dbBase64 = FaceMatchController::resizeAndEncode($this->dbPath);
        if (!$dbBase64) {
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
            $matchCount = count(array_filter($existing, fn($r) => ($r['confidence'] ?? 0) >= 80));

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
                'match'          => (bool) ($result['match'] ?? false),
                'confidence'     => (int) ($result['confidence'] ?? 0),
                'reason'         => $result['reason'] ?? '-',
                'age_note'       => $result['age_note'] ?? null,
            ];

            Cache::put($this->sessionKey, $existing, now()->addHours(2));

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

    private function buildAgeContext(): string
    {
        $parts = [];

        if ($this->queryAgeMonths > 0) {
            $parts[] = sprintf(
                'GAMBAR 1 (foto query) diambil sekitar %s yang lalu.',
                $this->formatMonths($this->queryAgeMonths)
            );
        }

        if ($this->dbAgeMonths > 0) {
            $parts[] = sprintf(
                'GAMBAR 2 (foto pendamping) diambil sekitar %s yang lalu.',
                $this->formatMonths($this->dbAgeMonths)
            );
        }

        $ageDiff = abs($this->queryAgeMonths - $this->dbAgeMonths);
        if ($ageDiff >= 6) {
            $parts[] = sprintf(
                'Selisih waktu antar foto sekitar %s — pertimbangkan kemungkinan perubahan penampilan (rambut, berat badan, wajah lebih tua/muda) dan tetap fokus pada struktur wajah yang tidak berubah.',
                $this->formatMonths($ageDiff)
            );
        }

        return $parts ? implode(' ', $parts) : '';
    }

    private function formatMonths(int $months): string
    {
        if ($months < 12) {
            return "{$months} bulan";
        }
        $years  = intdiv($months, 12);
        $remain = $months % 12;
        $str    = "{$years} tahun";
        if ($remain > 0) {
            $str .= " {$remain} bulan";
        }
        return $str;
    }

    private function callClaude(string $queryBase64, string $dbBase64): ?array
    {
        try {
            $ageContext = $this->buildAgeContext();

            // Bangun teks instruksi — sertakan konteks usia jika tersedia
            $instruction = 'Bandingkan wajah GAMBAR 1 dan GAMBAR 2. '
                . 'Fokus pada fitur wajah struktural yang tidak berubah seiring waktu: '
                . 'bentuk wajah, proporsi mata-hidung-mulut, jarak antar mata, struktur tulang pipi dan rahang, bentuk alis. '
                . 'Abaikan: pencahayaan, sudut pengambilan, aksesori (kacamata, topi, hijab), riasan, warna/gaya rambut. ';

            if ($ageContext) {
                $instruction .= $ageContext . ' ';
            }

            $instruction .= 'Jika ada selisih usia, sesuaikan toleransi untuk perubahan wajar (kerutan halus, perubahan berat badan ringan). '
                . 'Balas HANYA JSON tanpa teks lain: '
                . '{"match":true/false,"confidence":0-100,"reason":"alasan singkat bahasa Indonesia","age_note":"catatan singkat jika perbedaan usia mempengaruhi analisis, atau null"}';

            $client   = new Client(['timeout' => 25]);
            $response = $client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key'         => env('ANTHROPIC_API_KEY'),
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    // Claude Sonnet 4.6 — model terbaru per Mei 2026
                    'model'      => 'claude-sonnet-4-6',
                    'max_tokens' => 256,
                    'messages'   => [[
                        'role'    => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $instruction,
                            ],
                            ['type' => 'text', 'text' => 'GAMBAR 1 (foto query):'],
                            [
                                'type'   => 'image',
                                'source' => [
                                    'type'       => 'base64',
                                    'media_type' => 'image/jpeg',
                                    'data'       => $queryBase64,
                                ],
                            ],
                            ['type' => 'text', 'text' => 'GAMBAR 2 (foto pendamping):'],
                            [
                                'type'   => 'image',
                                'source' => [
                                    'type'       => 'base64',
                                    'media_type' => 'image/jpeg',
                                    'data'       => $dbBase64,
                                ],
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
