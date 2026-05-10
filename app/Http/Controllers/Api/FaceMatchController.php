<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\FaceMatchJob;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FaceMatchController extends Controller
{
    const MAX_IMAGE_SIZE = 768;

    // -------------------------------------------------------------------------
    // Halaman utama — hanya upload foto, tanpa dropdown enumerator
    // -------------------------------------------------------------------------
    public function index()
    {
        $totalFoto = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->count();

        $totalEnumerator = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->distinct('enumerator_id')
            ->count('enumerator_id');

        return view('superadmin.face-match.index', compact('totalFoto', 'totalEnumerator'));
    }

    // -------------------------------------------------------------------------
    // Terima upload foto → dispatch jobs untuk SEMUA enumerator → redirect status
    // -------------------------------------------------------------------------
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

        // Simpan foto query
        $tmpPath   = $request->file('foto_query')->store('tmp/face-match', 'public');
        $queryUrl  = asset('storage/' . $tmpPath);
        $queryPath = storage_path('app/public/' . $tmpPath);

        // Ambil SEMUA data lapangan yang punya foto pendamping (semua enumerator)
        $dataLapangans = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->select('id', 'enumerator_id', 'nama_pu', 'nik', 'telephone', 'foto_pendamping')
            ->orderBy('enumerator_id')
            ->orderBy('id')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return back()->with('error', 'Tidak ada data foto pendamping yang tersedia.');
        }

        $sessionKey = 'face_match_' . Str::uuid();

        Cache::put($sessionKey . '_meta', [
            'query_url'  => $queryUrl,
            'total'      => $dataLapangans->count(),
            'started_at' => now()->toDateTimeString(),
        ], now()->addHours(2));

        // Preload enumerator names agar tidak N+1 di loop
        $enumeratorNames = Enumerator::whereIn('id', $dataLapangans->pluck('enumerator_id')->unique())
            ->pluck('nama_lengkap', 'id');

        // Buat jobs untuk SEMUA foto dari SEMUA enumerator
        $jobs = [];
        foreach ($dataLapangans as $data) {
            $dbPath = storage_path('app/public/' . $data->foto_pendamping);
            if (!file_exists($dbPath)) {
                continue;
            }

            $namaEnumerator = $enumeratorNames[$data->enumerator_id]
                ?? 'Enumerator #' . $data->enumerator_id;

            $jobs[] = new FaceMatchJob(
                sessionKey: $sessionKey,
                dataId: $data->id,
                namaPu: $data->nama_pu ?? '',
                nik: $data->nik ?? '',
                telephone: $data->telephone ?? '',
                fotoPendamping: $data->foto_pendamping,
                queryPath: $queryPath,
                dbPath: $dbPath,
                enumeratorId: $data->enumerator_id,
                namaEnumerator: $namaEnumerator,
            );
        }

        if (empty($jobs)) {
            return back()->with('error', 'Tidak ada foto yang dapat diproses.');
        }

        $batch = Bus::batch($jobs)
            ->name('face-match:' . $sessionKey)
            ->allowFailures()
            ->dispatch();

        Cache::put($sessionKey . '_batch', $batch->id, now()->addHours(2));

        return redirect()->route('superadmin.face-match.status', ['key' => $sessionKey]);
    }

    // -------------------------------------------------------------------------
    // Halaman status
    // -------------------------------------------------------------------------
    public function status(Request $request)
    {
        $sessionKey = $request->query('key');
        $meta       = Cache::get($sessionKey . '_meta');

        if (!$meta) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau sudah kadaluarsa.');
        }

        return view('superadmin.face-match.status', compact('sessionKey', 'meta'));
    }

    // -------------------------------------------------------------------------
    // Endpoint AJAX polling
    // -------------------------------------------------------------------------
    public function poll(Request $request)
    {
        $sessionKey = $request->query('key');
        $batchId    = Cache::get($sessionKey . '_batch');
        $meta       = Cache::get($sessionKey . '_meta');

        if (!$batchId || !$meta) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 404);
        }

        $batch = Bus::findBatch($batchId);

        if (!$batch) {
            return response()->json(['error' => 'Batch tidak ditemukan.'], 404);
        }

        $processed  = $batch->processedJobs();
        $total      = $batch->totalJobs;
        $finished   = $batch->finished();
        $percentage = $total > 0 ? (int) round(($processed / $total) * 100) : 0;

        // Ambil hasil terkini dari cache untuk ditampilkan live di status page
        $allResults = Cache::get($sessionKey, []);

        // Urutan TIDAK diubah (append-only) supaya lastRendered di JS tetap akurat
        $recentActivity = collect($allResults)
            ->map(fn($r) => [
                'nama_pu'         => $r['data']['nama_pu'] ?? '-',
                'nama_enumerator' => $r['data']['nama_enumerator'] ?? '-',
                'confidence'      => $r['confidence'],
                'match'           => ($r['confidence'] ?? 0) >= 80,
            ])
            ->values()
            ->toArray();

        $matchCount = count(array_filter($allResults, fn($r) => ($r['confidence'] ?? 0) >= 80));

        return response()->json([
            'finished'        => $finished,
            'processed'       => $processed,
            'total'           => $total,
            'percentage'      => $percentage,
            'failed'          => $batch->failedJobs,
            'match_count'     => $matchCount,
            'recent_activity' => $recentActivity,
        ]);
    }

    // -------------------------------------------------------------------------
    // Halaman hasil — filter ≥80%, ambil TOP 3 saja
    // -------------------------------------------------------------------------
    public function result(Request $request)
    {
        $sessionKey = $request->query('key');
        $meta       = Cache::get($sessionKey . '_meta');
        $allResults = Cache::get($sessionKey, []);

        if (!$meta) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau sudah kadaluarsa.');
        }

        // Filter hanya yang confidence ≥ 80%
        $filtered = array_values(array_filter($allResults, fn($r) => ($r['confidence'] ?? 0) >= 80));

        // Urutkan: confidence tertinggi dulu
        usort($filtered, fn($a, $b) => $b['confidence'] - $a['confidence']);

        // Ambil TOP 3 saja
        $results = array_slice($filtered, 0, 3);

        $queryUrl       = $meta['query_url'];
        $totalDianalisis = count($allResults);
        $totalDitemukan  = count($filtered); // total ≥80% sebelum dipotong 3

        return view('superadmin.face-match.result', compact(
            'results',
            'queryUrl',
            'totalDianalisis',
            'totalDitemukan',
        ));
    }

    // -------------------------------------------------------------------------
    // Helper: resize & encode gambar ke base64 JPEG
    // -------------------------------------------------------------------------
    public static function resizeAndEncode(string $filePath): ?string
    {
        $info = @getimagesize($filePath);
        if (!$info) {
            return null;
        }

        [$origW, $origH, $type] = $info;
        $max   = self::MAX_IMAGE_SIZE;
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
