<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\KtpVerifikasiService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class KtpVerifikasiController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private KtpVerifikasiService $service
    ) {}

    /**
     * Halaman utama — form upload KTP + ZIP
     */
    public function index()
    {
        $geminiApiKey = $this->service->getApiKey();
        $routePrefix  = $this->routePrefix();

        return view('superadmin.ktp-verifikasi.index', compact('geminiApiKey', 'routePrefix'));
    }

    /**
     * Proses verifikasi — upload KTP + ZIP → ekstrak → scan semua foto → top 3
     */
    public function verify(Request $request)
    {
        $request->validate([
            'foto_ktp'  => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'zip_fotos' => 'required|file|mimes:zip|max:51200', // maks 50MB
        ], [
            'foto_ktp.required'  => 'Foto KTP wajib diunggah.',
            'foto_ktp.image'     => 'KTP harus berupa gambar.',
            'foto_ktp.mimes'     => 'Format KTP harus JPG atau PNG.',
            'foto_ktp.max'       => 'Ukuran KTP maksimal 5MB.',
            'zip_fotos.required' => 'File ZIP foto pendamping wajib diunggah.',
            'zip_fotos.mimes'    => 'File harus berformat ZIP.',
            'zip_fotos.max'      => 'Ukuran ZIP maksimal 50MB.',
        ]);

        $apiKey = $this->service->getApiKey();
        if (empty($apiKey)) {
            return back()->with('error', 'Gemini API Key belum dikonfigurasi. Atur di Setting Website → Tab API Keys.');
        }

        // Simpan foto KTP sementara
        $ktpTmpPath = $request->file('foto_ktp')->store('tmp/ktp-verifikasi', 'public');
        $ktpAbsPath = storage_path('app/public/' . $ktpTmpPath);
        $ktpUrl     = Storage::url($ktpTmpPath);

        // Simpan ZIP dan tentukan direktori ekstrak
        $zipTmpPath  = $request->file('zip_fotos')->store('tmp/ktp-verifikasi-zip', 'public');
        $zipAbsPath  = storage_path('app/public/' . $zipTmpPath);
        $extractDir  = storage_path('app/public/tmp/ktp-extracted-' . uniqid());

        $extractedCount = 0;
        try {
            // Ekstrak ZIP
            $zip = new ZipArchive;
            if ($zip->open($zipAbsPath) !== true) {
                return back()->with('error', 'File ZIP tidak valid atau rusak.');
            }
            @mkdir($extractDir, 0775, true);
            $zip->extractTo($extractDir);
            $zip->close();

            // Hitung foto yang berhasil diekstrak
            $extractedCount = count($this->service->collectImagePaths($extractDir));

            // Jalankan verifikasi
            $result = $this->service->verifikasiKtpDariZip($ktpAbsPath, $extractDir, $apiKey);

        } finally {
            // Bersihkan semua file sementara
            @unlink($ktpAbsPath);
            @unlink($zipAbsPath);
            $this->service->deleteDirectory($extractDir);
        }

        return view('superadmin.ktp-verifikasi.result', [
            'results'        => $result['results'],
            'totalScanned'   => $result['total_scanned'],
            'ktpInfo'        => $result['ktp_info'],
            'ktpUrl'         => $ktpUrl,
            'extractedCount' => $extractedCount,
            'routePrefix'    => $this->routePrefix(),
        ]);
    }
}
