<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\FaceMatchJob;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FaceMatchController extends Controller
{
    use HasRoutePrefix;

    const MAX_IMAGE_SIZE = 768;

    // -------------------------------------------------------------------------
    // Halaman utama — upload foto + pilih enumerator (multi-select)
    // -------------------------------------------------------------------------
    public function index()
    {
        $enumerators = Enumerator::whereIn(
            'id',
            DataLapangan::whereNotNull('foto_pendamping')
                ->where('foto_pendamping', '!=', '')
                ->whereNotNull('enumerator_id')
                ->pluck('enumerator_id')
                ->unique()
        )
            ->withCount([
                'dataLapangans as foto_count' => fn($q) =>
                $q->whereNotNull('foto_pendamping')->where('foto_pendamping', '!=', '')
            ])
            ->orderBy('nama_lengkap')
            ->get();

        $totalFoto       = $enumerators->sum('foto_count');
        $totalEnumerator = $enumerators->count();

        $routePrefix = $this->routePrefix();

        return view('superadmin.face-match.index', compact(
            'enumerators',
            'totalFoto',
            'totalEnumerator',
            'routePrefix',
        ));
    }

    // -------------------------------------------------------------------------
    // Terima upload foto + enumerator_ids → dispatch jobs → redirect
    // -------------------------------------------------------------------------
    public function match(Request $request)
    {
        $request->validate([
            'foto_query'       => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'enumerator_ids'   => 'required|array|min:1',
            'enumerator_ids.*' => 'integer|exists:enumerators,id',
        ], [
            'foto_query.required'     => 'Foto wajah wajib diunggah.',
            'foto_query.image'        => 'File harus berupa gambar.',
            'foto_query.mimes'        => 'Format gambar harus JPG atau PNG.',
            'foto_query.max'          => 'Ukuran gambar maksimal 5MB.',
            'enumerator_ids.required' => 'Pilih minimal 1 enumerator.',
            'enumerator_ids.min'      => 'Pilih minimal 1 enumerator.',
        ]);

        $selectedIds = $request->input('enumerator_ids');

        // Simpan foto query dan hitung usianya
        $tmpPath      = $request->file('foto_query')->store('tmp/face-match', 'public');
        $queryUrl     = asset('storage/' . $tmpPath);
        $queryPath    = storage_path('app/public/' . $tmpPath);

        // Usia foto query = waktu upload (baru saja) → 0 bulan
        $queryAgeMonths = 0;

        $dataLapangans = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->whereIn('enumerator_id', $selectedIds)
            ->select('id', 'enumerator_id', 'nama_pu', 'nik', 'telephone', 'foto_pendamping')
            ->orderBy('enumerator_id')
            ->orderBy('id')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return back()->with('error', 'Tidak ada data foto pendamping untuk enumerator yang dipilih.');
        }

        $sessionKey = 'face_match_' . Str::uuid();

        // Store session ownership - only the current user can access this session
        $userId = $request->user()?->id ?? 'anonymous';
        Cache::put($sessionKey . '_owner', $userId, now()->addHours(2));

        $enumeratorNames = Enumerator::whereIn('id', collect($selectedIds))
            ->pluck('nama_lengkap', 'id');

        $queryBase64 = self::resizeAndEncode($queryPath);
        if (!$queryBase64) {
            return back()->with('error', 'Foto wajah tidak dapat diproses. Coba foto lain.');
        }
        Cache::put($sessionKey . '_query_b64', $queryBase64, now()->addHours(2));

        Cache::put($sessionKey . '_meta', [
            'query_url'           => $queryUrl,
            'total'               => $dataLapangans->count(),
            'started_at'          => now()->toDateTimeString(),
            'selected_enum_count' => count($selectedIds),
            'selected_enum_names' => $enumeratorNames->values()->take(3)->implode(', ')
                . (count($selectedIds) > 3 ? ', ...' : ''),
            'query_age_months'    => $queryAgeMonths,
        ], now()->addHours(2));

        $jobs = [];
        foreach ($dataLapangans as $data) {
            $dbPath = storage_path('app/public/' . $data->foto_pendamping);
            if (!file_exists($dbPath)) {
                continue;
            }

            // Hitung usia foto DB dari mtime file
            $dbAgeMonths = 0;
            $mtime = @filemtime($dbPath);
            if ($mtime) {
                $dbAgeMonths = (int) now()->diffInMonths(\Carbon\Carbon::createFromTimestamp($mtime));
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
                dbPath: $dbPath,
                enumeratorId: $data->enumerator_id,
                namaEnumerator: $namaEnumerator,
                queryAgeMonths: $queryAgeMonths,
                dbAgeMonths: $dbAgeMonths,
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
    // Halaman status - dengan validasi ownership
    // -------------------------------------------------------------------------
    public function status(Request $request)
    {
        $sessionKey = $request->query('key');

        // SECURITY: Validasi ownership sesi
        if (!$this->validateSessionOwnership($sessionKey, $request)) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $meta = Cache::get($sessionKey . '_meta');

        if (!$meta) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau sudah kadaluarsa.');
        }

        $routePrefix = $this->routePrefix();

        return view('superadmin.face-match.status', compact('sessionKey', 'meta', 'routePrefix'));
    }

    // -------------------------------------------------------------------------
    // Endpoint AJAX polling - dengan validasi ownership
    // -------------------------------------------------------------------------
    public function poll(Request $request)
    {
        $sessionKey = $request->query('key');

        // SECURITY: Validasi ownership sesi
        if (!$this->validateSessionOwnership($sessionKey, $request)) {
            return response()->json(['error' => 'Sesi tidak ditemukan.'], 404);
        }

        $batchId = Cache::get($sessionKey . '_batch');
        $meta = Cache::get($sessionKey . '_meta');

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

        $allResults = Cache::get($sessionKey, []);

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
    // Halaman hasil - dengan validasi ownership
    // -------------------------------------------------------------------------
    public function result(Request $request)
    {
        $sessionKey = $request->query('key');

        // SECURITY: Validasi ownership sesi
        if (!$this->validateSessionOwnership($sessionKey, $request)) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $meta = Cache::get($sessionKey . '_meta');
        $allResults = Cache::get($sessionKey, []);

        if (!$meta) {
            return redirect()->route('superadmin.face-match.index')
                ->with('error', 'Sesi tidak ditemukan atau sudah kadaluarsa.');
        }

        $filtered = array_values(array_filter($allResults, fn($r) => ($r['confidence'] ?? 0) >= 80));
        usort($filtered, fn($a, $b) => $b['confidence'] - $a['confidence']);
        $results = array_slice($filtered, 0, 3);

        $queryUrl        = $meta['query_url'];
        $totalDianalisis = count($allResults);
        $totalDitemukan  = count($filtered);

        $routePrefix = $this->routePrefix();

        return view('superadmin.face-match.result', compact(
            'results',
            'queryUrl',
            'totalDianalisis',
            'totalDitemukan',
            'routePrefix',
        ));
    }

    // -------------------------------------------------------------------------
    // SECURITY: Validasi ownership sesi face match
    // -------------------------------------------------------------------------
    private function validateSessionOwnership(string $sessionKey, Request $request): bool
    {
        $owner = Cache::get($sessionKey . '_owner');
        $userId = $request->user()?->id ?? 'anonymous';

        // Jika tidak ada owner yang tersimpan, berarti sesi lama - validasi berdasarkan session
        if ($owner === null) {
            // Untuk backward compatibility dengan sesi lama, tetap izinkan
            // Tapi log untuk audit
            Log::warning('Face match session accessed without ownership check', [
                'session_key' => $sessionKey,
                'user_id' => $userId,
            ]);
            return true;
        }

        return $owner === $userId;
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
