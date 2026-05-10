<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\FaceMatchJob;
use App\Models\DataLapangan;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FaceMatchController extends Controller
{
    const MAX_IMAGE_SIZE = 768;

    // -------------------------------------------------------------------------
    // Halaman utama
    // -------------------------------------------------------------------------
    public function index()
    {
        $totalData = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->count();

        return view('superadmin.face-match.index', compact('totalData'));
    }

    // -------------------------------------------------------------------------
    // Terima upload → dispatch batch jobs → redirect ke halaman status
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

        // Resize & encode foto query
        $queryBase64 = $this->resizeAndEncode($request->file('foto_query')->getRealPath());
        if (!$queryBase64) {
            return back()->with('error', 'Gagal memproses foto yang diupload.');
        }

        // Ambil semua data yang punya foto pendamping
        $dataLapangans = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->select('id', 'nama_pu', 'nik', 'telephone', 'foto_pendamping')
            ->orderBy('id')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return back()->with('error', 'Tidak ada data foto pendamping di database.');
        }

        // Key unik untuk sesi pencocokan ini
        $sessionKey = 'face_match_' . Str::uuid();

        // Simpan foto query sementara untuk preview
        $tmpPath  = $request->file('foto_query')->store('tmp/face-match', 'public');
        $queryUrl = asset('storage/' . $tmpPath);

        // Simpan metadata sesi ke cache
        Cache::put($sessionKey . '_meta', [
            'query_url' => $queryUrl,
            'total'     => $dataLapangans->count(),
            'started_at' => now()->toDateTimeString(),
        ], now()->addHours(2));

        // Buat jobs — resize tiap foto di sini (masih di web process tapi sekali jalan)
        $jobs = [];
        foreach ($dataLapangans as $data) {
            $fotoPath = storage_path('app/public/' . $data->foto_pendamping);
            if (!file_exists($fotoPath)) {
                continue;
            }

            $dbBase64 = $this->resizeAndEncode($fotoPath);
            if (!$dbBase64) {
                continue;
            }

            $jobs[] = new FaceMatchJob(
                sessionKey: $sessionKey,
                dataId: $data->id,
                namaPu: $data->nama_pu ?? '',
                nik: $data->nik ?? '',
                telephone: $data->telephone ?? '',
                fotoPendamping: $data->foto_pendamping,
                queryBase64: $queryBase64,
                dbBase64: $dbBase64,
            );

            unset($dbBase64);
        }

        unset($queryBase64);

        if (empty($jobs)) {
            return back()->with('error', 'Tidak ada foto yang dapat diproses.');
        }

        // Dispatch sebagai Batch
        $batch = Bus::batch($jobs)
            ->name('face-match:' . $sessionKey)
            ->allowFailures()   // lanjut meski ada job yang gagal
            ->dispatch();

        // Simpan batch ID ke cache agar bisa dicek statusnya
        Cache::put($sessionKey . '_batch', $batch->id, now()->addHours(2));

        return redirect()->route('superadmin.face-match.status', ['key' => $sessionKey]);
    }

    // -------------------------------------------------------------------------
    // Halaman status — polling hingga batch selesai
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
    // Endpoint AJAX — dicall tiap 2 detik oleh halaman status
    // -------------------------------------------------------------------------
    public function poll(Request $request)
    {
        $sessionKey = $request->query('key');
        $batchId    = Cache::get($sessionKey . '_batch');
        $meta       = Cache::get($sessionKey . '_meta');

        if (!$batchId || !$meta) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 404);
        }

        // Ambil status batch dari database jobs_batches
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
    // Halaman hasil — ditampilkan setelah batch selesai
    // -------------------------------------------------------------------------
    public function result(Request $request)
    {
        $sessionKey = $request->query('key');
        $meta       = Cache::get($sessionKey . '_meta');
        $results    = Cache::get($sessionKey, []);

        if (!$meta) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau sudah kadaluarsa.');
        }

        // Urutkan: match duluan → confidence tertinggi
        usort(
            $results,
            fn($a, $b) =>
            $a['match'] !== $b['match']
                ? (int) $b['match'] - (int) $a['match']
                : $b['confidence'] - $a['confidence']
        );

        $queryUrl = $meta['query_url'];

        return view('superadmin.face-match.result', compact('results', 'queryUrl'));
    }

    // -------------------------------------------------------------------------
    // Helper: resize & encode gambar ke base64 JPEG
    // -------------------------------------------------------------------------
    private function resizeAndEncode(string $filePath): ?string
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
