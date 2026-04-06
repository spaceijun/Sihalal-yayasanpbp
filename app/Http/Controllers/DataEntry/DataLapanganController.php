<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use App\Services\Superadmin\FileService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DataLapanganController extends Controller
{
    protected $fileService;
    protected $statusService;
    protected $notificationService;

    public function __construct(
        FileService $fileService,
        StatusService $statusService,
        NotificationService $notificationService
    ) {
        $this->fileService         = $fileService;
        $this->statusService       = $statusService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show the verified data entry list
     */
    public function index(Request $request): View
    {
        $enumerators = DataLapangan::where('status', 'TERVERIFIKASI')->paginate();

        return view('data-entry.data-lapangan.index', compact('enumerators'))
            ->with('i', ($request->input('page', 1) - 1) * $enumerators->perPage());
    }

    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        $entryType = $dataEntry?->entry_type;

        // Ambil progress terbaru milik data lapangan ini untuk user yang login
        $latestProgress = $dataEntry
            ? DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('data_lapangan_id', $dataLapangan->id)
            ->latest()
            ->first()
            : null;

        return view('data-entry.data-lapangan.show', compact('dataLapangan', 'entryType', 'latestProgress'));
    }

    /**
     * Track progress data entry ketika upload file / update status.
     * Status default PENDING — menunggu review superadmin.
     */
    private function trackDataEntryProgress(
        DataLapangan $dataLapangan,
        string $fileType,
        string $newStatus,
        string $fileName
    ): DataEntryProgress {
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        $progress = DataEntryProgress::create([
            'user_id'          => Auth::id(),
            'data_entry_id'    => $dataEntry->id,
            'data_lapangan_id' => $dataLapangan->id,
            'action'           => 'created',
            'status'           => 'PENDING',
            'old_data'         => [
                'status' => $dataLapangan->status,
            ],
            'new_data'         => [
                'file_type' => $fileType,
                'status'    => $newStatus,
                'file_name' => $fileName,
            ],
            'actioned_at'      => now(),
        ]);

        return $progress;
    }

    /**
     * Upload file OSS (entry_type = OSS)
     */
    public function uploadFile(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'file'      => 'required|mimes:pdf|max:5120',
            'file_type' => 'required|in:oss,sihalal',
        ]);

        $fileType     = $request->file_type;
        $uploadedFile = $request->file('file');

        $uploadResult = $this->fileService->uploadFile($dataLapangan, $uploadedFile, $fileType);

        // Update status tanpa trigger Observer agar tidak double counting
        $newStatus = $this->statusService->determineStatusByFileType($fileType);
        DataLapangan::withoutEvents(function () use ($dataLapangan, $newStatus) {
            $dataLapangan->status = $newStatus;
            $dataLapangan->save();
        });

        // Track progress — status PENDING, belum masuk penagihan
        $this->trackDataEntryProgress(
            $dataLapangan,
            $fileType,
            $newStatus,
            $uploadedFile->getClientOriginalName()
        );

        $statusMessage = "Status diubah menjadi {$newStatus}";
        $message       = 'File ' . strtoupper($fileType) . ' berhasil diupload. ' . $statusMessage;

        // Handle notifications
        if ($fileType === 'oss' && $uploadResult['is_first_upload']) {
            $notificationSent = $this->notificationService->sendOSSNotification($dataLapangan);
            $message .= $notificationSent
                ? ' Notifikasi WhatsApp telah dikirim ke koordinator.'
                : ' Namun notifikasi WhatsApp gagal dikirim ke koordinator.';
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

        return redirect()->route('data-entry.data-lapangan.index')->with('success', $message);
    }

    /**
     * Update status ke PROGRESS SIHALAL (entry_type = SIHALAL)
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        try {
            $dataLapangan = DataLapangan::findOrFail($id);
            if (!in_array($dataLapangan->status, ['PROGRESS OSS', 'DITOLAK'])) {
                return redirect()->back()->with('error', 'Update status hanya dapat dilakukan dari status PROGRESS OSS atau DITOLAK');
            }

            $newStatus = 'PROGRESS SIHALAL';

            // Track dulu SEBELUM status berubah — agar old_data['status'] = status lama
            $this->trackDataEntryProgress(
                $dataLapangan,
                'status_update',
                $newStatus,
                'N/A'
            );

            // Baru update status
            $dataLapangan->status = $newStatus;
            $dataLapangan->save();

            return redirect()->back()->with('success', 'Status berhasil diupdate ke PROGRESS SIHALAL');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }
    /**
     * Resubmit setelah revisi — data entry mengupdate keterangan atau file ulang
     * Dipanggil dari tombol revisi di halaman show
     */
    public function resubmit(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'keterangan_update' => 'required|string|max:1000',
        ]);

        $dataEntry = DataEntry::where('user_id', Auth::id())->first();
        if (!$dataEntry) {
            return redirect()->back()->with('error', 'Data entry tidak ditemukan.');
        }

        // Ambil progress terakhir yang berstatus REVISI untuk data lapangan ini
        $progressRevisi = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('data_lapangan_id', $dataLapangan->id)
            ->where('status', 'REVISI')
            ->latest()
            ->first();

        if (!$progressRevisi) {
            return redirect()->back()->with('error', 'Tidak ada data revisi yang perlu diupdate.');
        }

        // Update progress yang REVISI menjadi PENDING kembali dengan keterangan update
        $progressRevisi->update([
            'status'             => 'PENDING',
            'keterangan_update'  => $request->keterangan_update,
            'actioned_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Resubmit berhasil. Menunggu review dari superadmin.');
    }
}
