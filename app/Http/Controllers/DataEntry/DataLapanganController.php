<?php

namespace App\Http\Controllers\DataEntry;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use App\Services\DataEntryPenagihanService;
use App\Services\Superadmin\FileService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\StatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Intervention\Image\Colors\Rgb\Channels\Red;

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
        $this->fileService = $fileService;
        $this->statusService = $statusService;
        $this->notificationService = $notificationService;
    }
    /**
     * Show the verified data entry list
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
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

        $entryType = DataEntry::where('user_id', Auth::id())
            ->value('entry_type');

        return view('data-entry.data-lapangan.show', compact('dataLapangan', 'entryType'));
    }

    /**
     * Track progress data entry ketika upload file
     */
    private function trackDataEntryProgress(DataLapangan $dataLapangan, string $fileType, string $newStatus, string $fileName): void
    {
        if (!Auth::check() || Auth::user()->role !== 'data_entry') return;

        $dataEntry = DataEntry::where('user_id', Auth::id())->first();
        if (!$dataEntry) return;

        DataEntryProgress::create([
            'user_id'          => Auth::id(),
            'data_entry_id'    => $dataEntry->id,
            'data_lapangan_id' => $dataLapangan->id,
            'action'           => 'created',
            'old_data'         => null,
            'new_data'         => [
                'file_type' => $fileType,
                'status'    => $newStatus,
                'file_name' => $fileName,
            ],
            'actioned_at'      => now(),
        ]);

        $penagihan = app(DataEntryPenagihanService::class)->cekDanBuatTagihan($dataEntry);

        // Jika tagihan berhasil dibuat, kirim notifikasi ke superadmin
        if ($penagihan) {
            // Opsional: kirim notifikasi WhatsApp / email ke superadmin
            // $this->notificationService->sendPenagihanNotification($penagihan);
        }
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

        $fileType    = $request->file_type;
        $uploadedFile = $request->file('file');
        $uploadResult = $this->fileService->uploadFile($dataLapangan, $uploadedFile, $fileType);

        // Update status tanpa trigger Observer agar tidak double counting
        $newStatus = $this->statusService->determineStatusByFileType($fileType);
        DataLapangan::withoutEvents(function () use ($dataLapangan, $newStatus) {
            $dataLapangan->status = $newStatus;
            $dataLapangan->save();
        });

        // ✅ Track progress data entry
        $this->trackDataEntryProgress(
            $dataLapangan,
            $fileType,
            $newStatus,
            $uploadedFile->getClientOriginalName()
        );

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
}
