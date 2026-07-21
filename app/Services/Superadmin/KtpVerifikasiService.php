<?php

namespace App\Services\Superadmin;

use App\Http\Controllers\Api\FaceMatchController;
use App\Models\DataLapangan;
use App\Models\Superadmin\Settingwebsite;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class KtpVerifikasiService
{
    /**
     * Bangun prompt forensik KYC untuk Gemini Flash.
     * Gambar 1 = foto KTP, Gambar 2 = foto_pendamping dari DataLapangan.
     */
    private function buildForensikPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah seorang ahli forensik digital dan verifikasi identitas (KYC Specialist).

Tugas: Lakukan analisis komparatif mendalam untuk menentukan tingkat kemiripan dan probabilitas kecocokan identitas antara individu dalam "Gambar 1: Foto KTP" dan individu target dalam "Gambar 2: Foto Pendamping.

Instruksi Analisis:
Analisis Anda harus mencakup tiga lapisan verifikasi berikut:

Langkah 1: Ekstraksi Data Tekstual KTP (Gambar 1)
Baca dan catat data berikut dari Gambar 1 sebagai referensi dasar:
* Nama: [Ekstrak Nama Lengkap]
* Nomor Induk Kependudukan (NIK): [Ekstrak NIK]
* Jenis Kelamin: [Ekstrak Jenis Kelamin]
* Pasfoto KTP: Gunakan pasfoto berlatar belakang warna (merah/biru) sebagai referensi visual utama.

Langkah 2: Analisis Komparatif Biometrik (Gambar 1 vs Gambar 2)
Bandingkan Pasfoto KTP dengan wajah individu target di Foto Pendamping. Abaikan perbedaan pencahayaan, ekspresi, atau pose mikro. Fokus pada fitur anatomi yang permanen dan tekstur wajah.
Berikan analisis mendetail pada poin-poin berikut:
1. Kesesuaian Jenis Kelamin (Gender Match): Apakah jenis kelamin target di Gambar 2 sesuai dengan data teks Jenis Kelamin pada KTP di Gambar 1? (Wajib Sesuai).
2. Geometri Wajah (Facial Geometry): Bandingkan bentuk wajah keseluruhan (oval, bulat, kotak, tirus), struktur tulang pipi, dan garis rahang.
3. Tekstur dan Fitur Area Mata (Periocular Analysis): Bandingkan bentuk mata, kelopak mata (monolid/double eyelid), jarak antar mata, dan bentuk alis.
4. Morfologi Hidung dan Mulut: Bandingkan bentuk batang hidung, cuping hidung, bentuk bibir, dan garis senyum.
5. Tanda Khusus/Tekstur Kulit (Distinctive Features): Cari kesamaan pada tahi lalat, bekas luka, atau pola kerutan permanen.
6. Fitur Eksternal (Rambut/Telinga): Bandingkan garis rambut (hairline) dan bentuk telinga (jika terlihat).

Langkah 3: Kesimpulan dan Skor Kemiripan
Balas HANYA dalam format JSON berikut tanpa teks lain:
{"nama_ktp":"nama lengkap dari KTP","nik_ktp":"NIK dari KTP","status":"Terverifikasi|Tidak Cocok|Keraguan Tinggi","confidence":0-100,"justifikasi":"2-3 poin utama singkat bahasa Indonesia"}
PROMPT;
    }

    /**
     * Kirim dua gambar ke Gemini Flash dan parsing hasilnya.
     *
     * @param  string  $ktpBase64  base64 foto KTP
     * @param  string  $pendBase64  base64 foto pendamping
     * @param  string  $apiKey  Gemini API key
     */
    private function callGemini(string $ktpBase64, string $pendBase64, string $apiKey): ?array
    {
        try {
            $client = new Client(['timeout' => 30]);

            // Gunakan Interactions API dengan model gemini-3.5-flash (sesuai dokumentasi terbaru)
            $url = 'https://generativelanguage.googleapis.com/v1beta/interactions';

            $response = $client->post($url, [
                'headers' => [
                    'x-goog-api-key' => $apiKey,
                    'Content-Type'   => 'application/json',
                ],
                'json' => [
                    'model' => 'gemini-3.5-flash',
                    'input' => [
                        ['type' => 'text',  'text' => $this->buildForensikPrompt()],
                        ['type' => 'text',  'text' => 'Gambar 1 (Foto KTP):'],
                        ['type' => 'image', 'source' => [
                            'type'       => 'base64',
                            'media_type' => 'image/jpeg',
                            'data'       => $ktpBase64,
                        ]],
                        ['type' => 'text',  'text' => 'Gambar 2 (Foto Pendamping):'],
                        ['type' => 'image', 'source' => [
                            'type'       => 'base64',
                            'media_type' => 'image/jpeg',
                            'data'       => $pendBase64,
                        ]],
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            // Interactions API: output ada di steps[].content[].text dengan type=model_output
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

            // Fallback ke output_text langsung jika ada
            if (empty($text) && isset($body['output_text'])) {
                $text = $body['output_text'];
            }

            $text = trim($text);

            // Bersihkan markdown fence jika ada
            $text = preg_replace('/```json\s*/i', '', $text);
            $text = preg_replace('/```\s*/', '', $text);

            $parsed = json_decode(trim($text), true);

            return is_array($parsed) ? $parsed : null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $status = $e->getResponse()->getStatusCode();
            Log::warning("KtpVerifikasi Gemini HTTP {$status}: ".$e->getMessage());

            return null;
        } catch (\Throwable $e) {
            Log::warning('KtpVerifikasi Gemini error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Ambil Gemini API key dari database.
     */
    public function getApiKey(): string
    {
        return (string) (Settingwebsite::value('gemini_api_key') ?? '');
    }

    /**
     * Lakukan verifikasi KTP terhadap semua foto_pendamping di database.
     * Mengembalikan top 3 hasil terbaik diurutkan berdasarkan confidence tertinggi.
     *
     * @param  string  $ktpPath  absolute path ke foto KTP yang diunggah
     * @param  string  $apiKey  Gemini API key
     * @return array{results: array, total_scanned: int, ktp_info: array}
     */
    public function verifikasiKtp(string $ktpPath, string $apiKey): array
    {
        // Encode foto KTP
        $ktpBase64 = FaceMatchController::resizeAndEncode($ktpPath);
        if (! $ktpBase64) {
            return ['results' => [], 'total_scanned' => 0, 'ktp_info' => []];
        }

        // Ambil semua data yang memiliki foto_pendamping
        $dataLapangans = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->with('enumerator')
            ->select('id', 'enumerator_id', 'nama_pu', 'nik', 'no_registrasi', 'foto_pendamping')
            ->get();

        $allResults = [];
        $ktpInfo = [];
        $totalScanned = 0;

        foreach ($dataLapangans as $dl) {
            $pendPath = storage_path('app/public/'.$dl->foto_pendamping);
            if (! file_exists($pendPath)) {
                continue;
            }

            $pendBase64 = FaceMatchController::resizeAndEncode($pendPath);
            if (! $pendBase64) {
                continue;
            }

            $result = $this->callGemini($ktpBase64, $pendBase64, $apiKey);
            $totalScanned++;

            if (! $result) {
                continue;
            }

            // Simpan info KTP dari hasil pertama yang berhasil parse
            if (empty($ktpInfo) && ! empty($result['nama_ktp'])) {
                $ktpInfo = [
                    'nama' => $result['nama_ktp'] ?? '-',
                    'nik' => $result['nik_ktp'] ?? '-',
                ];
            }

            $allResults[] = [
                'data' => [
                    'id' => $dl->id,
                    'hashed_id' => $dl->hashed_id,
                    'nama_pu' => $dl->nama_pu,
                    'nik' => $dl->nik,
                    'no_registrasi' => $dl->no_registrasi ?? '-',
                    'foto_pendamping' => $dl->foto_pendamping,
                    'nama_enumerator' => $dl->enumerator?->nama_lengkap ?? '-',
                    'enumerator_id' => $dl->enumerator_id,
                ],
                'confidence' => (int) ($result['confidence'] ?? 0),
                'status' => $result['status'] ?? 'Tidak Cocok',
                'justifikasi' => $result['justifikasi'] ?? '-',
            ];
        }

        // Urutkan berdasarkan confidence tertinggi dan ambil top 3
        usort($allResults, fn ($a, $b) => $b['confidence'] - $a['confidence']);
        $top3 = array_slice($allResults, 0, 3);

        return [
            'results' => $top3,
            'total_scanned' => $totalScanned,
            'ktp_info' => $ktpInfo,
        ];
    }
}
