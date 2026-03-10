<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use App\Services\Koordinator\DataLapanganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataLapanganController extends Controller
{
    protected $dataLapanganService;
    public function __construct(DataLapanganService $dataLapanganService)
    {
        $this->dataLapanganService = $dataLapanganService;
    }

    public function index(Request $request): View
    {
        $filters = [
            'nama_pu' => $request->nama_pu,
            'enumerator_id' => $request->enumerator_id,
            'status' => $request->status,
            'koordinator_id' => Auth::id(),
        ];

        $dataLapangans = $this->dataLapanganService->getFilteredData($filters, 20);
        $i = ($dataLapangans->currentPage() - 1) * $dataLapangans->perPage();

        return view('koordinator.data-lapangan.index', compact('i', 'dataLapangans'));
    }
    /**
     * Update the status of a data lapangan
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            // Validasi bahwa status saat ini adalah PROGRESS OSS
            if ($dataLapangan->status !== 'PROGRESS OSS' && $dataLapangan->status !== 'DITOLAK') {
                return redirect()->back()->with('error', 'Update status hanya dapat dilakukan dari status PROGRESS OSS atau DITOLAK');
            }

            // Update status ke PROGRESS SIHALAL (fixed, tidak dari request)
            $dataLapangan->status = 'PROGRESS SIHALAL';
            $dataLapangan->save();

            return redirect()->back()->with('success', 'Status berhasil diupdate ke PROGRESS SIHALAL');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }
    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);
        $dataLapangan->load(['enumerator', 'spotchecks']);

        // Ambil data entry berdasarkan entry_type
        $dataEntryOSS = DataEntryProgress::with('dataEntry')
            ->where('data_lapangan_id', $dataLapangan->id)
            ->whereHas('dataEntry', fn($q) => $q->where('entry_type', 'OSS'))
            ->orderBy('actioned_at', 'asc')
            ->first();

        $dataEntrySihalal = DataEntryProgress::with('dataEntry')
            ->where('data_lapangan_id', $dataLapangan->id)
            ->whereHas('dataEntry', fn($q) => $q->where('entry_type', 'SIHALAL'))
            ->orderBy('actioned_at', 'asc')
            ->first();

        return view('koordinator.data-lapangan.show', compact(
            'dataLapangan',
            'dataEntryOSS',
            'dataEntrySihalal'
        ));
    }
    /**
     * Download foto KTP yang tersimpan di storage.
     *
     * @param int $id ID data lapangan yang foto KTP-nya akan diunduh
     * @return Response
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function downloadFotoKTP($id)
    {
        $dataLapangan = DataLapangan::findOrFail($id);

        if (!$dataLapangan->foto_ktp) {
            return back()->with('error', 'Foto KTP tidak tersedia');
        }

        // Alternatif yang lebih reliable:
        $fotoPath = storage_path('app/public/' . $dataLapangan->foto_ktp);

        // Atau deteksi otomatis:
        if (file_exists(public_path('storage/' . $dataLapangan->foto_ktp))) {
            $fotoPath = public_path('storage/' . $dataLapangan->foto_ktp);
        } elseif (file_exists(storage_path('app/public/' . $dataLapangan->foto_ktp))) {
            $fotoPath = storage_path('app/public/' . $dataLapangan->foto_ktp);
        } else {
            return response()->json(['error' => 'File foto KTP tidak ditemukan'], 404);
        }
        if (!file_exists($fotoPath)) {
            return back()->with('error', 'File foto KTP tidak ditemukan');
        }

        // Deteksi tipe gambar
        $imageInfo = getimagesize($fotoPath);
        $mimeType = $imageInfo['mime'];

        // Load gambar sesuai tipe
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($fotoPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($fotoPath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($fotoPath);
                break;
            default:
                return back()->with('error', 'Format gambar tidak didukung');
        }

        // Compress dan simpan temporary
        $tempPath = storage_path('app/temp_ktp_' . $id . '.jpg');
        $quality = 85;

        imagejpeg($image, $tempPath, $quality);

        // Cek ukuran dan compress lebih jika perlu
        while (filesize($tempPath) > 2 * 1024 * 1024 && $quality > 50) {
            $quality -= 5;
            imagejpeg($image, $tempPath, $quality);
        }

        imagedestroy($image);

        $fileName = 'KTP_' . $dataLapangan->nama_pu . '.jpg';
        $fileName = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $fileName);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }



    /**
     * Check if a nik exists in the database
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkNik(Request $request)
    {
        $nik = $request->nik;

        $exists = DataLapangan::where('nik', $nik)->first();

        return response()->json([
            'exists' => $exists ? true : false,
            'nama_pu' => $exists ? $exists->nama_pu : null
        ]);
    }
}
