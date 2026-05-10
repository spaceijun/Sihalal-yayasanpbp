<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\FaceMatchJob;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\User; // sesuaikan jika model enumerator bukan User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FaceMatchController extends Controller
{
    const MAX_IMAGE_SIZE = 768;

    // -------------------------------------------------------------------------
    // Halaman utama — dropdown enumerator
    // -------------------------------------------------------------------------
    public function index()
    {
        $enumerators = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->selectRaw('enumerator_id, COUNT(*) as foto_count')
            ->groupBy('enumerator_id')
            ->having('foto_count', '>=', 1)
            ->get()
            ->map(function ($row) {
                $enumerator        = Enumerator::find($row->enumerator_id);
                $row->nama_lengkap = $enumerator?->nama_lengkap ?? 'Enumerator #' . $row->enumerator_id;
                return $row;
            })
            ->sortBy('nama_lengkap')
            ->values();

        return view('superadmin.face-match.index', compact('enumerators'));
    }

    // -------------------------------------------------------------------------
    // Terima upload + enumerator_id → dispatch jobs → redirect status
    // -------------------------------------------------------------------------
    public function match(Request $request)
    {
        $request->validate([
            'foto_query'    => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'enumerator_id' => 'required|integer',
        ], [
            'foto_query.required'    => 'Foto wajah wajib diunggah.',
            'foto_query.image'       => 'File harus berupa gambar.',
            'foto_query.mimes'       => 'Format gambar harus JPG atau PNG.',
            'foto_query.max'         => 'Ukuran gambar maksimal 5MB.',
            'enumerator_id.required' => 'Pilih enumerator terlebih dahulu.',
        ]);

        $enumeratorId = (int) $request->enumerator_id;

        // Simpan foto query
        $tmpPath  = $request->file('foto_query')->store('tmp/face-match', 'public');
        $queryUrl = asset('storage/' . $tmpPath);
        $queryPath = storage_path('app/public/' . $tmpPath);

        // Ambil data milik enumerator yang dipilih saja
        $dataLapangans = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->where('enumerator_id', $enumeratorId)
            ->select('id', 'enumerator_id', 'nama_pu', 'nik', 'telephone', 'foto_pendamping')
            ->orderBy('id')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return back()->with('error', 'Enumerator ini tidak memiliki data foto pendamping.');
        }

        $enumerator     = Enumerator::find($enumeratorId);
        $namaEnumerator = $enumerator?->nama_lengkap ?? 'Enumerator #' . $enumeratorId;

        $sessionKey = 'face_match_' . Str::uuid();

        Cache::put($sessionKey . '_meta', [
            'query_url'      => $queryUrl,
            'total'          => $dataLapangans->count(),
            'started_at'     => now()->toDateTimeString(),
            'enumerator_id'  => $enumeratorId,
            'nama_enumerator' => $namaEnumerator,
        ], now()->addHours(2));

        // Buat jobs — kirim path, resize dilakukan di worker
        $jobs = [];
        foreach ($dataLapangans as $data) {
            $dbPath = storage_path('app/public/' . $data->foto_pendamping);
            if (!file_exists($dbPath)) {
                continue;
            }

            $jobs[] = new FaceMatchJob(
                sessionKey: $sessionKey,
                dataId: $data->id,
                namaPu: $data->nama_pu ?? '',
                nik: $data->nik ?? '',
                telephone: $data->telephone ?? '',
                fotoPendamping: $data->foto_pendamping,
                queryPath: $queryPath,
                dbPath: $dbPath,
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

        return response()->json([
            'finished'   => $finished,
            'processed'  => $processed,
            'total'      => $total,
            'percentage' => $percentage,
            'failed'     => $batch->failedJobs,
        ]);
    }

    // -------------------------------------------------------------------------
    // Halaman hasil — filter hanya ≥80%
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

        // Filter ≥80%
        $results = array_values(array_filter($allResults, fn($r) => $r['confidence'] >= 80));

        // Urutkan: confidence tertinggi dulu
        usort($results, fn($a, $b) => $b['confidence'] - $a['confidence']);

        $queryUrl        = $meta['query_url'];
        $namaEnumerator  = $meta['nama_enumerator'] ?? '-';
        $totalDianalisis = count($allResults);

        return view('superadmin.face-match.result', compact(
            'results',
            'queryUrl',
            'namaEnumerator',
            'totalDianalisis',
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
