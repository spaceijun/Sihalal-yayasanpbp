<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Api\FaceMatchController;
use App\Http\Controllers\Controller;
use App\Jobs\KtpVerifikasiJob;
use App\Models\KtpVerifikasiSession;
use App\Services\Superadmin\KtpVerifikasiService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class KtpVerifikasiController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private KtpVerifikasiService $service
    ) {}

    // ── INDEX ────────────────────────────────────────────────────────────────

    public function index()
    {
        $geminiApiKey = $this->service->getApiKey();
        $routePrefix  = $this->routePrefix();

        return view('superadmin.ktp-verifikasi.index', compact('geminiApiKey', 'routePrefix'));
    }

    // ── VERIFY (Multi-KTP + ZIP → Dispatch Batch Jobs) ───────────────────────

    public function verify(Request $request)
    {
        $request->validate([
            'foto_ktp'    => 'required|array|min:1|max:5',
            'foto_ktp.*' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'zip_fotos'   => 'required|file|mimes:zip|max:102400',
        ], [
            'foto_ktp.required'   => 'Minimal 1 foto KTP wajib diunggah.',
            'foto_ktp.array'      => 'Format upload KTP tidak valid.',
            'foto_ktp.max'        => 'Maksimal 5 foto KTP.',
            'foto_ktp.*.required' => 'File KTP tidak boleh kosong.',
            'foto_ktp.*.image'    => 'Setiap file KTP harus berupa gambar.',
            'foto_ktp.*.mimes'    => 'Format KTP harus JPG atau PNG.',
            'foto_ktp.*.max'      => 'Ukuran tiap KTP maksimal 5MB.',
            'zip_fotos.required'  => 'File ZIP foto wajib diunggah.',
            'zip_fotos.mimes'     => 'File harus berformat ZIP.',
            'zip_fotos.max'       => 'Ukuran ZIP maksimal 100MB.',
        ]);

        $apiKey = $this->service->getApiKey();
        if (empty($apiKey)) {
            return back()->with('error', 'Gemini API Key belum dikonfigurasi. Atur di Setting Website → Tab API Keys.');
        }

        // Generate session key di awal
        $sessionKey = Str::random(48);
        $ktpFiles   = $request->file('foto_ktp');
        $ktpCount   = count($ktpFiles);

        // ── 1. Encode semua KTP & simpan ke folder session ──────────────────
        $ktpDataList = []; // [ ['base64'=>..., 'url'=>..., 'file_name'=>...], ... ]

        foreach ($ktpFiles as $idx => $ktpFile) {
            $ext        = $ktpFile->getClientOriginalExtension();
            $fileName   = $ktpFile->getClientOriginalName();
            $storedPath = $ktpFile->storeAs("ktp-verifikasi-sessions/{$sessionKey}", "ktp_{$idx}.{$ext}", 'public');
            $absPath    = storage_path('app/public/' . $storedPath);
            $url        = Storage::url($storedPath);
            $b64        = FaceMatchController::resizeAndEncode($absPath);

            if (! $b64) {
                // Bersihkan semua yang sudah tersimpan
                Storage::disk('public')->deleteDirectory("ktp-verifikasi-sessions/{$sessionKey}");
                return back()->with('error', "Foto KTP ke-" . ($idx + 1) . " ({$fileName}) tidak dapat diproses.");
            }

            $ktpDataList[] = [
                'index'     => $idx,
                'base64'    => $b64,
                'url'       => $url,
                'file_name' => $fileName,
            ];
        }

        // ── 2. Ekstrak ZIP ──────────────────────────────────────────────────
        $zipTmpPath = $request->file('zip_fotos')->store('tmp/ktp-verifikasi-zip', 'public');
        $zipAbsPath = storage_path('app/public/' . $zipTmpPath);
        $extractDir = storage_path('app/public/tmp/ktp-extracted-' . Str::random(12));

        try {
            $zip = new ZipArchive;
            if ($zip->open($zipAbsPath) !== true) {
                @unlink($zipAbsPath);
                Storage::disk('public')->deleteDirectory("ktp-verifikasi-sessions/{$sessionKey}");
                return back()->with('error', 'File ZIP tidak valid atau rusak.');
            }
            @mkdir($extractDir, 0775, true);
            $zip->extractTo($extractDir);
            $zip->close();
        } catch (\Throwable $e) {
            Storage::disk('public')->deleteDirectory("ktp-verifikasi-sessions/{$sessionKey}");
            $this->service->deleteDirectory($extractDir);
            return back()->with('error', 'Gagal mengekstrak ZIP: ' . $e->getMessage());
        } finally {
            @unlink($zipAbsPath);
        }

        $photoPaths = $this->service->collectImagePaths($extractDir);

        if (empty($photoPaths)) {
            $this->service->deleteDirectory($extractDir);
            Storage::disk('public')->deleteDirectory("ktp-verifikasi-sessions/{$sessionKey}");
            return back()->with('error', 'Tidak ada foto (JPG/PNG/WebP) ditemukan dalam file ZIP.');
        }

        // ── 3. Encode foto ZIP & build jobs ─────────────────────────────────
        // Inisialisasi results awal dengan meta KTP (tanpa kandidat)
        $initialResults = [];
        foreach ($ktpDataList as $ktp) {
            $initialResults[$ktp['index']] = [
                'ktp_index'      => $ktp['index'],
                'ktp_file'       => $ktp['file_name'],
                'ktp_url'        => $ktp['url'],
                'ktp_nama'       => null,
                'ktp_nik'        => null,
                'top_candidates' => [],
            ];
        }

        $totalJobs  = 0;
        $jobs       = [];
        $zipPhotoCount = count($photoPaths);

        // ── 3. Encode foto ZIP & build jobs (1 job per foto) ─────────────────
        // Setiap job: baca semua KTP dari disk → bandingkan dengan 1 foto ini
        $jobs = [];

        foreach ($photoPaths as $photoPath) {
            $photoBase64 = FaceMatchController::resizeAndEncode($photoPath);
            if (! $photoBase64) {
                continue;
            }
            $jobs[] = new KtpVerifikasiJob(
                sessionKey:  $sessionKey,
                ktpCount:    $ktpCount,    // job baca KTP dari disk pakai sessionKey
                photoBase64: $photoBase64,
                namaFile:    basename($photoPath),
            );
        }

        $this->service->deleteDirectory($extractDir); // selesai encode, hapus ekstrak

        if (empty($jobs)) {
            Storage::disk('public')->deleteDirectory("ktp-verifikasi-sessions/{$sessionKey}");
            return back()->with('error', 'Semua foto dalam ZIP gagal diproses.');
        }

        // ── 4. Buat session di DB ────────────────────────────────────────────
        $totalJobs = count($jobs); // = jumlah foto ZIP (BUKAN KTP × foto)

        $session = KtpVerifikasiSession::create([
            'session_key'  => $sessionKey,
            'user_id'      => Auth::id(),
            'ktp_count'    => $ktpCount,
            'total_photos' => $totalJobs, // total jobs = ktpCount × zipPhotoCount
            'processed'    => 0,
            'status'       => 'pending',
            'ktp_url'      => $ktpDataList[0]['url'], // referensi cepat KTP pertama
            'results'      => json_encode($initialResults),
        ]);

        // ── 5. Dispatch batch ────────────────────────────────────────────────
        $batch = Bus::batch($jobs)
            ->name("KTP Verifikasi [{$sessionKey}]")
            ->allowFailures()
            ->finally(function () use ($sessionKey) {
                $session = KtpVerifikasiSession::where('session_key', $sessionKey)->first();
                if (! $session) {
                    return;
                }

                // Trim tiap KTP ke top 3 (exclusivity sudah inherent — 1 foto → 1 KTP)
                $results = $session->results ?? [];
                foreach ($results as &$ktpResult) {
                    usort(
                        $ktpResult['top_candidates'],
                        fn ($a, $b) => $b['confidence'] - $a['confidence']
                    );
                    $ktpResult['top_candidates'] = array_slice($ktpResult['top_candidates'], 0, 3);
                }
                unset($ktpResult);
                ksort($results);

                $session->update(['results' => $results, 'status' => 'done']);
            })
            ->dispatch();

        $session->update([
            'batch_id' => $batch->id,
            'status'   => 'processing',
        ]);

        return redirect()->route($this->routePrefix() . '.ktp-verifikasi.progress', $sessionKey);
    }

    // ── PROGRESS (Halaman polling) ───────────────────────────────────────────

    public function progress(string $sessionKey)
    {
        $session = KtpVerifikasiSession::where('session_key', $sessionKey)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $routePrefix = $this->routePrefix();

        return view('superadmin.ktp-verifikasi.progress', compact('session', 'routePrefix'));
    }

    // ── STATUS API (polling) ─────────────────────────────────────────────────

    public function statusApi(string $sessionKey)
    {
        $session = KtpVerifikasiSession::where('session_key', $sessionKey)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($session->status === 'processing' && $session->batch_id) {
            $batch = Bus::findBatch($session->batch_id);
            if ($batch && $batch->finished() && $session->status !== 'done') {
                $results = $session->results ?? [];
                foreach ($results as &$ktpResult) {
                    usort($ktpResult['top_candidates'], fn ($a, $b) => $b['confidence'] - $a['confidence']);
                    $ktpResult['top_candidates'] = array_slice($ktpResult['top_candidates'], 0, 3);
                }
                unset($ktpResult);
                ksort($results);
                $session->update(['results' => $results, 'status' => 'done']);
                $session->refresh();
            }
        }

        return response()->json([
            'status'     => $session->status,
            'processed'  => $session->processed,
            'total'      => $session->total_photos,
            'ktp_count'  => $session->ktp_count,
            'percent'    => $session->getProgressPercent(),
            'done'       => $session->status === 'done',
            'result_url' => $session->status === 'done'
                ? route($this->routePrefix() . '.ktp-verifikasi.result', $sessionKey)
                : null,
        ]);
    }

    // ── RESULT ───────────────────────────────────────────────────────────────

    public function result(string $sessionKey)
    {
        $session = KtpVerifikasiSession::where('session_key', $sessionKey)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $results = $session->results ?? [];
        ksort($results);

        return view('superadmin.ktp-verifikasi.result', [
            'session'     => $session,
            'ktpResults'  => array_values($results), // [{ktp_index, ktp_file, ktp_nama, top_candidates}, ...]
            'totalScanned'=> $session->processed,
            'totalPhotos' => $session->total_photos,
            'ktpCount'    => $session->ktp_count,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    // ── DOWNLOAD ZIP ─────────────────────────────────────────────────────────

    public function download(string $sessionKey)
    {
        $session = KtpVerifikasiSession::where('session_key', $sessionKey)
            ->where('user_id', Auth::id())
            ->where('status', 'done')
            ->firstOrFail();

        $results = $session->results ?? [];
        ksort($results);

        // Buat ZIP di storage sementara
        $tmpZipPath = storage_path('app/tmp/ktp-verifikasi-' . Str::random(8) . '.zip');
        @mkdir(dirname($tmpZipPath), 0775, true);

        $zip = new ZipArchive;
        if ($zip->open($tmpZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        foreach ($results as $ktpResult) {
            $idx      = $ktpResult['ktp_index'];
            $ktpNama  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $ktpResult['ktp_nama'] ?? 'KTP');
            $folderName = "KTP_" . ($idx + 1) . "_" . $ktpNama;

            // Sertakan foto KTP referensi jika masih ada di disk
            if (! empty($ktpResult['ktp_url'])) {
                $diskPath = public_path($ktpResult['ktp_url']);
                if (file_exists($diskPath)) {
                    $ext = pathinfo($diskPath, PATHINFO_EXTENSION);
                    $zip->addFile($diskPath, "{$folderName}/00_KTP_Referensi.{$ext}");
                }
            }

            // Sertakan top 3 foto kandidat
            foreach ($ktpResult['top_candidates'] as $rank => $candidate) {
                $b64Data  = $candidate['foto_base64'] ?? '';
                $b64Strip = preg_replace('/^data:image\/\w+;base64,/', '', $b64Data);
                $imgBytes = base64_decode($b64Strip);

                if (! $imgBytes) {
                    continue;
                }

                $conf     = $candidate['confidence'] ?? 0;
                $rawName  = pathinfo($candidate['nama_file'] ?? "foto_{$rank}", PATHINFO_FILENAME);
                $fileName = sprintf('Rank%d_%dpct_%s.jpg', $rank + 1, $conf, $rawName);
                $zip->addFromString("{$folderName}/{$fileName}", $imgBytes);
            }
        }

        $zip->close();

        $downloadName = 'Hasil_Verifikasi_KTP_' . date('Ymd_His') . '.zip';

        return response()
            ->download($tmpZipPath, $downloadName)
            ->deleteFileAfterSend(true);
    }
}
