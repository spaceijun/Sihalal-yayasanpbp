<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FaceMatchController extends Controller
{
    /**
     * Tampilkan halaman face matching
     */
    public function index()
    {
        return view('superadmin.face-match.index');
    }

    /**
     * Proses pencocokan wajah dengan semua data di database
     */
    public function match(Request $request)
    {
        $request->validate([
            'foto_query' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'foto_query.required' => 'Foto wajah wajib diunggah.',
            'foto_query.image'    => 'File harus berupa gambar.',
            'foto_query.mimes'    => 'Format gambar harus JPG atau PNG.',
            'foto_query.max'      => 'Ukuran gambar maksimal 5MB.',
        ]);

        // Baca foto yang diupload & encode base64
        $queryImageData = base64_encode(file_get_contents($request->file('foto_query')->getRealPath()));
        $queryMediaType = $request->file('foto_query')->getMimeType();

        // Ambil semua data yang punya foto pendamping
        $dataLapangans = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->select('id', 'nama_pendamping', 'foto_pendamping', 'nama_usaha', 'no_hp')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return back()->with('error', 'Tidak ada data foto pendamping di database.');
        }

        $results = [];

        foreach ($dataLapangans as $data) {
            // Baca foto dari storage
            $fotoPath = storage_path('app/public/' . $data->foto_pendamping);
            if (!file_exists($fotoPath)) {
                continue;
            }

            $dbImageData  = base64_encode(file_get_contents($fotoPath));
            $dbMediaType  = mime_content_type($fotoPath);

            // Kirim ke Claude API
            $response = Http::withHeaders([
                'x-api-key'         => env('ANTHROPIC_API_KEY'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-sonnet-4-5',
                'max_tokens' => 256,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type'   => 'text',
                                'text'   => 'Kamu adalah sistem pencocokan wajah. Bandingkan wajah pada GAMBAR 1 (foto query) dengan wajah pada GAMBAR 2 (foto KTP dari database). Fokus HANYA pada fitur wajah: bentuk wajah, mata, hidung, mulut, alis, dan struktur tulang. Abaikan perbedaan pencahayaan, sudut, usia, atau aksesori. Jawab HANYA dalam format JSON seperti ini persis, tanpa teks lain: {"match": true/false, "confidence": 0-100, "reason": "alasan singkat dalam bahasa Indonesia"}',
                            ],
                            [
                                'type'   => 'text',
                                'text'   => 'GAMBAR 1 (Foto Query - wajah yang dicari):',
                            ],
                            [
                                'type'   => 'image',
                                'source' => [
                                    'type'       => 'base64',
                                    'media_type' => $queryMediaType,
                                    'data'       => $queryImageData,
                                ],
                            ],
                            [
                                'type'   => 'text',
                                'text'   => 'GAMBAR 2 (Foto KTP dari database):',
                            ],
                            [
                                'type'   => 'image',
                                'source' => [
                                    'type'       => 'base64',
                                    'media_type' => $dbMediaType ?: 'image/jpeg',
                                    'data'       => $dbImageData,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                continue;
            }

            $content = $response->json('content.0.text', '');

            // Bersihkan JSON dari markdown fence jika ada
            $content = preg_replace('/```json|```/', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);

            if (!$parsed) {
                continue;
            }

            $results[] = [
                'data'       => $data,
                'match'      => (bool) ($parsed['match'] ?? false),
                'confidence' => (int) ($parsed['confidence'] ?? 0),
                'reason'     => $parsed['reason'] ?? '-',
                'foto_url'   => asset('storage/' . $data->foto_pendamping),
            ];
        }

        // Urutkan: yang match duluan, lalu berdasarkan confidence tertinggi
        usort($results, function ($a, $b) {
            if ($a['match'] !== $b['match']) {
                return $b['match'] <=> $a['match'];
            }
            return $b['confidence'] <=> $a['confidence'];
        });

        // Simpan foto query sementara untuk ditampilkan
        $queryUrl = null;
        if ($request->hasFile('foto_query')) {
            $tmpPath  = $request->file('foto_query')->store('tmp/face-match', 'public');
            $queryUrl = asset('storage/' . $tmpPath);
        }

        return view('superadmin.face-match.result', compact('results', 'queryUrl'));
    }
}
