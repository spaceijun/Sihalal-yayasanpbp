<?php

namespace App\Http\Controllers\Superadmin;

use App\Exports\DataLapangansExport;
use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DataLapanganRequest;
use App\Models\DataEntryProgress;
use App\Models\Enumerator;
use App\Models\Verifikator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use App\Services\Superadmin\DataLapanganService;
use App\Services\Superadmin\StatusService;
use App\Services\Superadmin\FileService;
use App\Services\Superadmin\ImageService;
use App\Services\Superadmin\ImageDownloadService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\PdfService;
use Illuminate\Support\Facades\Log;

class DataLapanganController extends Controller
{
    public function __construct(
        private DataLapanganService $dataLapanganService,
        private StatusService $statusService,
        private FileService $fileService,
        private ImageService $imageService,
        private ImageDownloadService $imageDownloadService,
        private NotificationService $notificationService,
        private PdfService $pdfService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [
            'nama_pu' => $request->nama_pu,
            'enumerator_id' => $request->enumerator_id,
            'status' => $request->status,
        ];

        $dataLapangans = $this->dataLapanganService->getFilteredData($filters, 20);
        $i = ($dataLapangans->currentPage() - 1) * $dataLapangans->perPage();

        return view('superadmin.data-lapangan.index', compact('dataLapangans', 'i'));
    }

    /**
     * Show Data Revisi
     */
    public function dataRevisi(Request $request): View
    {
        $dataLapangans = $this->dataLapanganService->getDataRevisi();

        return view('superadmin.data-lapangan.partials.data-revisi', compact('dataLapangans'))
            ->with('i', ($request->input('page', 1) - 1) * $dataLapangans->perPage());
    }

    /**
     * Kirim notifikasi revisi untuk satu data
     */
    public function sendRevisiNotification($id): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::with('enumerator')->findOrFail($id);
            $result = $this->notificationService->sendRevisiNotification($dataLapangan);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kirim notifikasi revisi untuk semua data
     */
    public function sendAllRevisiNotifications(): JsonResponse
    {
        try {
            $result = $this->notificationService->sendAllRevisiNotifications();
            return response()->json($result, $result['success'] ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export data lapangan ke Excel
     */
    public function export(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'tanggal_dari' => $request->input('tanggal_dari'),
            'tanggal_sampai' => $request->input('tanggal_sampai'),
        ];

        $fileName = 'data-lapangan-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(new DataLapangansExport($filters), $fileName);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dataLapangan = new DataLapangan();
        $enumerators = Enumerator::orderBy('nama_lengkap')->get();

        return view('publik.form', compact('dataLapangan', 'enumerators'));
    }

    /**
     * Upload a file based on the given file type
     */
    public function uploadFile(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
            'file_type' => 'required|in:oss,sihalal'
        ]);

        $fileType = $request->file_type;
        $uploadResult = $this->fileService->uploadFile($dataLapangan, $request->file('file'), $fileType);

        // Update status
        $newStatus = $this->statusService->determineStatusByFileType($fileType);
        $dataLapangan->status = $newStatus;
        $dataLapangan->save();

        $statusMessage = "Status diubah menjadi {$newStatus}";
        $message = 'File ' . strtoupper($fileType) . ' berhasil diupload. ' . $statusMessage;

        // Handle notifications
        if ($fileType === 'oss' && $uploadResult['is_first_upload']) {
            $notificationSent = $this->notificationService->sendOSSNotification($dataLapangan);
            if ($notificationSent) {
                $message .= ' Notifikasi WhatsApp telah dikirim ke koordinator.';
            } else {
                $message .= ' Namun notifikasi WhatsApp gagal dikirim ke koordinator.';
            }
        }

        if ($fileType === 'sihalal' && $uploadResult['is_first_upload']) {
            $notificationSent = $this->notificationService->sendSihalalUploadNotification($dataLapangan);
            if ($notificationSent) {
                $message .= ' Link sertifikat halal telah dikirim ke PU.';
                return redirect()->back()->with('success', $message);
            } else {
                $message .= ' Namun notifikasi WhatsApp gagal dikirim.';
                return redirect()->back()->with('warning', $message);
            }
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Download foto KTP yang tersimpan di storage.
     */
    public function downloadFotoKTP($id)
    {
        $dataLapangan = DataLapangan::findOrFail($id);

        $fotoPath = $this->imageService->getFotoKTPPath($dataLapangan);

        if (!$fotoPath) {
            return back()->with('error', 'File foto KTP tidak ditemukan');
        }

        try {
            $tempPath = $this->imageService->compressKTPImage($fotoPath, $id);
            $fileName = $this->imageService->generateSafeFilename($dataLapangan->nama_pu);

            return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download foto Pendamping
     */
    public function downloadFotoPendamping($id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $downloadData = $this->imageDownloadService->downloadFotoPendamping($dataLapangan);

            return response()->download($downloadData['path'], $downloadData['filename'])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download foto Produk
     */
    public function downloadFotoProduk($id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $downloadData = $this->imageDownloadService->downloadFotoProduk($dataLapangan);

            return response()->download($downloadData['path'], $downloadData['filename'])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a file based on the given file type
     */
    public function deleteFile(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $fileType = $request->file_type;
        $deleted = $this->fileService->deleteFile($dataLapangan, $fileType);

        if (!$deleted) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        // Update status when file is deleted
        $statusResult = $this->statusService->determineStatusAfterDeletion($fileType, $dataLapangan);
        $dataLapangan->status = $statusResult['status'];
        $dataLapangan->save();

        return redirect()->back()->with(
            'success',
            'File ' . strtoupper($fileType) . ' berhasil dihapus. ' . $statusResult['message']
        );
    }

    /**
     * Update the status of a data lapangan.
     */
    public function updateStatus(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:PENDING,REVISI,PROGRESS OSS,PROGRESS SIHALAL,TERBIT SH,DITOLAK',
        ]);

        $result = $this->statusService->updateStatus($dataLapangan, $request->status);

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Update the keterangan of a data lapangan.
     */
    public function updateKeterangan(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $this->dataLapanganService->updateKeterangan($id, $request->keterangan);

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    public function updateEmail(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'email'              => 'required|email|max:255',
            'verifikator_id'     => 'nullable|exists:verifikators,id',
            'tanggal_verifikasi' => 'nullable|date',
        ]);

        $this->dataLapanganService->updateEmail(
            $id,
            $request->email,
            $request->verifikator_id,
            $request->tanggal_verifikasi
        );

        return redirect()->back()->with('success', 'Email berhasil diperbarui');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function uploadFileSequintal(Request $request, $type): JsonResponse
    {
        if (!$this->fileService->isAllowedType($type)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe file tidak valid'
            ], 400);
        }

        $request->validate([
            $type => 'required|image|mimes:jpeg,jpg,png|max:10240'
        ]);

        try {
            if (!$request->hasFile($type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 400);
            }

            $path = $this->fileService->uploadImageSequential($request->file($type), $type);

            return response()->json([
                'success' => true,
                'path' => $path,
                'message' => ucwords(str_replace('_', ' ', $type)) . ' berhasil diupload'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DataLapanganRequest $request): RedirectResponse
    {
        try {
            $this->dataLapanganService->create($request->validated());

            return redirect()->route('formulir.halal')
                ->with('success', 'Data lapangan berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update status pembayaran SATU data lapangan menjadi DIBAYAR.
     *
     * Cashflow dan notifikasi WA terpicu otomatis via DataLapangan::booted().
     *
     * PATCH /superadmin/data-lapangans/{dataLapangan}/update-status-payment
     */
    public function updateStatusPayment(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $this->processPembayaran($dataLapangan);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diubah menjadi DIBAYAR');
    }

    /**
     * Bulk update status pembayaran sejumlah data lapangan menjadi DIBAYAR.
     *
     * Menggunakan loop per instance (bukan whereIn()->update()) agar
     * model event booted() terpicu pada setiap record — sehingga cashflow
     * dan notifikasi WA berjalan identik dengan updateStatusPayment().
     *
     * POST /superadmin/data-lapangans/bulk-payment
     */
    public function bulkUpdateStatusPayment(Request $request): JsonResponse
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:data_lapangans,id',
        ]);

        // Ambil hanya record yang benar-benar eligible
        // (guard di sini sebagai lapisan keamanan tambahan,
        //  meskipun checkbox di view sudah difilter)
        $dataLapangans = DataLapangan::whereIn('id', $request->ids)
            ->where('status', 'TERBIT SH')
            ->where('status_pembayaran', 'PENDING')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang memenuhi syarat untuk diubah.',
            ], 422);
        }

        $updated = 0;
        $errors  = [];

        foreach ($dataLapangans as $dataLapangan) {
            try {
                $this->processPembayaran($dataLapangan);
                $updated++;
            } catch (\Exception $e) {
                // Catat error per-record agar record lain tetap diproses
                $errors[] = "ID {$dataLapangan->id} ({$dataLapangan->nama_pu}): {$e->getMessage()}";
                Log::error("bulkUpdateStatusPayment error on ID {$dataLapangan->id}: {$e->getMessage()}");
            }
        }

        $message = "{$updated} data berhasil diubah menjadi DIBAYAR";
        if (!empty($errors)) {
            $message .= '. Beberapa data gagal: ' . implode('; ', $errors);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'updated' => $updated,
            'errors'  => $errors,
        ]);
    }

    /**
     * Logika inti: update status_pembayaran ke DIBAYAR pada satu instance model.
     *
     * Cukup satu baris — semua side-effect (cashflow + WA) sudah
     * didelegasikan ke DataLapangan::booted() → static::updated().
     *
     * Dipanggil oleh: updateStatusPayment() dan bulkUpdateStatusPayment().
     */
    private function processPembayaran(DataLapangan $dataLapangan): void
    {
        $dataLapangan->update([
            'status_pembayaran' => 'DIBAYAR',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
        $dataLapangan->load(['enumerator', 'spotchecks']);

        $verifikators = Verifikator::orderBy('nama_lengkap')->get(); // tambah ini

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

        return view('superadmin.data-lapangan.show', compact(
            'dataLapangan',
            'dataEntryOSS',
            'dataEntrySihalal',
            'verifikators'
        ));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $dataLapangan = DataLapangan::find($id);

        return view('superadmin.data-lapangan.edit', compact('dataLapangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DataLapanganRequest $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $this->dataLapanganService->update($dataLapangan, $request->validated());

        return Redirect::route('superadmin.data-lapangans.index')
            ->with('success', 'DataLapangan updated successfully');
    }

    /**
     * Download foto rumah as PDF
     */
    public function downloadFotoRumahPdf($id)
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            $pdf = $this->pdfService->generateFotoRumahPdf($dataLapangan);
            $filename = $this->pdfService->generatePdfFilename('Foto_Rumah', $dataLapangan->nama_pu);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $this->dataLapanganService->delete($id);

        return Redirect::route('superadmin.data-lapangans.index')
            ->with('success', 'DataLapangan deleted successfully');
    }
}
