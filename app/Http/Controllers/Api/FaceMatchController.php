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
    // -------------------------------------------------------------------------
    // Halaman utama — tampilkan statistik enumerator
    // -------------------------------------------------------------------------
    public function index()
    {
        // Ambil enumerator yang punya minimal 2 foto (bisa dibandingkan)
        $enumerators = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->selectRaw('enumerator_id, COUNT(*) as foto_count')
            ->groupBy('enumerator_id')
            ->having('foto_count', '>=', 1)
            ->get()
            ->map(function ($row) {
                // Ambil nama enumerator
                $enumerator = Enumerator::find($row->enumerator_id);
                $row->nama_lengkap = $enumerator?->nama_lengkap ?? 'Enumerator #' . $row->enumerator_id;
                return $row;
            })
            ->sortByDesc('foto_count')
            ->values();

        $totalEnumerator = $enumerators->count();
        $totalFoto       = $enumerators->sum('foto_count');

        // Hitung total kombinasi: n*(n-1)/2 per enumerator
        $totalKombinasi = $enumerators->sum(function ($e) {
            $n = $e->foto_count;
            return $n * ($n - 1) / 2;
        });

        // Estimasi: 50 req/menit (Tier 1), ditambah buffer 20%
        $estimasiMenit = $totalKombinasi > 0
            ? (int) ceil(($totalKombinasi / 50) * 1.2)
            : 0;

        return view('superadmin.face-match.index', compact(
            'enumerators',
            'totalEnumerator',
            'totalFoto',
            'totalKombinasi',
            'estimasiMenit',
        ));
    }

    // -------------------------------------------------------------------------
    // Dispatch batch jobs — semua kombinasi pasangan per enumerator
    // -------------------------------------------------------------------------
    public function match(Request $request)
    {
        // Ambil semua data yang punya foto, group per enumerator
        $grouped = DataLapangan::whereNotNull('foto_pendamping')
            ->where('foto_pendamping', '!=', '')
            ->whereNotNull('enumerator_id')
            ->select('id', 'enumerator_id', 'nama_pu', 'nik', 'telephone', 'foto_pendamping')
            ->orderBy('enumerator_id')
            ->orderBy('id')
            ->get()
            ->groupBy('enumerator_id');

        if ($grouped->isEmpty()) {
            return back()->with('error', 'Tidak ada data foto pendamping di database.');
        }

        $sessionKey = 'face_match_' . Str::uuid();
        $jobs       = [];
        $metaEnum   = []; // simpan info enumerator untuk ditampilkan di result

        foreach ($grouped as $enumeratorId => $dataList) {
            // Minimal 2 foto agar bisa dibandingkan
            if ($dataList->count() < 2) {
                continue;
            }

            $enumerator = Enumerator::find($enumeratorId);
            $namaEnum   = $enumerator?->nama_lengkap ?? 'Enumerator #' . $enumeratorId;

            $metaEnum[$enumeratorId] = $namaEnum;

            $items = $dataList->values();

            // Buat kombinasi pasangan unik: (i, j) where j > i
            for ($i = 0; $i < $items->count(); $i++) {
                for ($j = $i + 1; $j < $items->count(); $j++) {
                    $dataA = $items[$i];
                    $dataB = $items[$j];

                    $pathA = storage_path('app/public/' . $dataA->foto_pendamping);
                    $pathB = storage_path('app/public/' . $dataB->foto_pendamping);

                    if (!file_exists($pathA) || !file_exists($pathB)) {
                        continue;
                    }

                    $jobs[] = new FaceMatchJob(
                        sessionKey: $sessionKey,
                        enumeratorId: $enumeratorId,
                        dataIdA: $dataA->id,
                        namaPuA: $dataA->nama_pu ?? '',
                        nikA: $dataA->nik ?? '',
                        telephoneA: $dataA->telephone ?? '',
                        fotoPendampingA: $dataA->foto_pendamping,
                        pathA: $pathA,
                        dataIdB: $dataB->id,
                        namaPuB: $dataB->nama_pu ?? '',
                        nikB: $dataB->nik ?? '',
                        telephoneB: $dataB->telephone ?? '',
                        fotoPendampingB: $dataB->foto_pendamping,
                        pathB: $pathB,
                    );
                }
            }
        }

        if (empty($jobs)) {
            return back()->with('error', 'Tidak ada kombinasi foto yang dapat diproses.');
        }

        // Simpan metadata sesi
        Cache::put($sessionKey . '_meta', [
            'total'      => count($jobs),
            'started_at' => now()->toDateTimeString(),
            'enumerators' => $metaEnum,
        ], now()->addHours(3));

        // Dispatch batch
        $batch = Bus::batch($jobs)
            ->name('face-match:' . $sessionKey)
            ->allowFailures()
            ->dispatch();

        Cache::put($sessionKey . '_batch', $batch->id, now()->addHours(3));

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
    // Halaman hasil
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

        // Filter hanya yang ≥80% confidence
        $filtered = array_filter($results, fn($r) => $r['confidence'] >= 80);

        // Kelompokkan per enumerator
        $grouped = [];
        foreach ($filtered as $r) {
            $eid = $r['enumerator_id'];
            if (!isset($grouped[$eid])) {
                $grouped[$eid] = [
                    'nama'  => $meta['enumerators'][$eid] ?? 'Enumerator #' . $eid,
                    'pairs' => [],
                ];
            }
            $grouped[$eid]['pairs'][] = $r;
        }

        // Urutkan tiap enumerator: confidence tertinggi dulu
        foreach ($grouped as &$g) {
            usort($g['pairs'], fn($a, $b) => $b['confidence'] - $a['confidence']);
        }

        // Urutkan enumerator: yang paling banyak duplikat dulu
        uasort($grouped, fn($a, $b) => count($b['pairs']) - count($a['pairs']));

        $totalDuplikat  = count($filtered);
        $totalEnumerator = count($grouped);
        $totalDianalisis = count($results);

        return view('superadmin.face-match.result', compact(
            'grouped',
            'totalDuplikat',
            'totalEnumerator',
            'totalDianalisis',
            'meta',
        ));
    }
}
