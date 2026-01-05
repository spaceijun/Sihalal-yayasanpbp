<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataLapanganController extends Controller
{

    public function index(Request $request)
    {
        $query = DataLapangan::with('enumerator')
            ->whereHas('enumerator', function ($q) {
                $q->where('koordinator_id', Auth::user()->koordinator->id);
            });

        // Filter berdasarkan nama PU
        if ($request->filled('nama_pu')) {
            $query->where('nama_pu', 'like', '%' . $request->nama_pu . '%');
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $dataLapangans = $query->latest()->paginate(10)->appends($request->all());

        return view('koordinator.data-lapangan.index', compact('dataLapangans'));
    }

    /**
     * Update the status of a data lapangan
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    /**
     * Validation rules:
     * - status: required, must be either 'PROGRESS SIHALAL', 'TERBIT SH', or 'DITOLAK'
     * - keterangan: optional, string
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            $dataLapangan = DataLapangan::findOrFail($id);

            // Validasi bahwa status saat ini adalah PROGRESS OSS
            if ($dataLapangan->status !== 'PROGRESS OSS' && $dataLapangan->status !== 'DITOLAK') {
                return redirect()->back()->with('error', 'Update status hanya dapat dilakukan dari status PROGRESS OSS atau DITOLAK');
            }

            // Update status ke PROGRESS SIHALAL (fixed, tidak dari request)
            $dataLapangan->status = 'PROGRESS SIHALAL';

            // Simpan keterangan jika ada field di database
            // if ($request->keterangan) {
            //     $dataLapangan->keterangan = $request->keterangan;
            // }

            $dataLapangan->save();

            return redirect()->back()->with('success', 'Status berhasil diupdate ke PROGRESS SIHALAL');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }
    public function show($id): View
    {
        $dataLapangan = DataLapangan::with('enumerator')->find($id);


        return view('koordinator.data-lapangan.show', compact('dataLapangan'));
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

        $fotoPath = public_path('storage/' . $dataLapangan->foto_ktp);

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
