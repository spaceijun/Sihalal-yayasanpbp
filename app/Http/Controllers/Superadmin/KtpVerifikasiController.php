<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\Superadmin\KtpVerifikasiService;
use App\Traits\HasRoutePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KtpVerifikasiController extends Controller
{
    use HasRoutePrefix;

    public function __construct(
        private KtpVerifikasiService $service
    ) {}

    /**
     * Halaman utama — form upload KTP
     */
    public function index()
    {
        $geminiApiKey = $this->service->getApiKey();
        $routePrefix = $this->routePrefix();

        return view('superadmin.ktp-verifikasi.index', compact('geminiApiKey', 'routePrefix'));
    }

    /**
     * Proses verifikasi — upload KTP → scan semua foto_pendamping → tampilkan top 3
     */
    public function verify(Request $request)
    {
        $request->validate([
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ], [
            'foto_ktp.required' => 'Foto KTP wajib diunggah.',
            'foto_ktp.image' => 'File harus berupa gambar.',
            'foto_ktp.mimes' => 'Format gambar harus JPG atau PNG.',
            'foto_ktp.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $apiKey = $this->service->getApiKey();

        if (empty($apiKey)) {
            return back()->with('error', 'GEMINI_API_KEY belum dikonfigurasi. Silakan atur di Setting Website → Tab API Keys.');
        }

        // Simpan foto KTP sementara
        $tmpPath = $request->file('foto_ktp')->store('tmp/ktp-verifikasi', 'public');
        $ktpPath = storage_path('app/public/'.$tmpPath);
        $ktpUrl = Storage::url($tmpPath);

        try {
            $result = $this->service->verifikasiKtp($ktpPath, $apiKey);
        } finally {
            // Hapus file sementara
            @unlink($ktpPath);
        }

        $routePrefix = $this->routePrefix();

        return view('superadmin.ktp-verifikasi.result', [
            'results' => $result['results'],
            'totalScanned' => $result['total_scanned'],
            'ktpInfo' => $result['ktp_info'],
            'ktpUrl' => $ktpUrl,
            'routePrefix' => $routePrefix,
        ]);
    }
}
