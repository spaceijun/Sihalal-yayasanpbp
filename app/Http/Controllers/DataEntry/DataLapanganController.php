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
     * Ambil DataEntry milik user yang sedang login.
     * Throw exception jika tidak ditemukan agar tidak perlu cek berulang.
     */
    private function getAuthDataEntry(): DataEntry
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        if (!$dataEntry) {
            abort(403, 'Data entry tidak ditemukan untuk akun ini.');
        }

        return $dataEntry;
    }

    /**
     * Cek apakah ada progress berstatus PENDING untuk data lapangan ini.
     * Digunakan sebelum membuat record baru agar tidak terjadi double submit.
     */
    private function hasPendingProgress(int $dataEntryId, int $dataLapanganId): bool
    {
        return DataEntryProgress::where('data_entry_id', $dataEntryId)
            ->where('data_lapangan_id', $dataLapanganId)
            ->where('status', 'PENDING')
            ->exists();
    }

    /**
     * Track progress data entry ketika upload file / update status.
     * Status default PENDING — menunggu review superadmin.
     * Status pada table data_lapangans TIDAK berubah di sini.
     * Perubahan status data_lapangans dilakukan oleh superadmin saat menerima progress.
     */
    private function trackDataEntryProgress(
        DataLapangan $dataLapangan,
        DataEntry $dataEntry,
        string $fileType,
        string $newStatus,
        string $fileName
    ): DataEntryProgress {
        return DataEntryProgress::create([
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
    }

    /**
     * Tampilkan data lapangan berstatus TERVERIFIKASI yang BELUM diambil siapapun.
     * Data dianggap "sudah diambil" jika sudah ada record di data_entry_progress
     * untuk data_lapangan_id tersebut.
     *
     * Pastikan model DataLapangan memiliki relasi:
     *   public function dataEntryProgress()
     *   {
     *       return $this->hasMany(DataEntryProgress::class, 'data_lapangan_id');
     *   }
     */
    public function index(Request $request): View
    {
        $enumerators = DataLapangan::where('status', 'TERVERIFIKASI')
            ->whereDoesntHave('dataEntryProgress')
            ->paginate();

        return view('data-entry.data-lapangan.index', compact('enumerators'))
            ->with('i', ($request->input('page', 1) - 1) * $enumerators->perPage());
    }

    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);
        $dataEntry    = DataEntry::where('user_id', Auth::id())->first();
        $entryType    = $dataEntry?->entry_type;

        $latestProgress = $dataEntry
            ? DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('data_lapangan_id', $dataLapangan->id)
            ->latest()
            ->first()
            : null;

        // Cek apakah ada progress PENDING — cukup cek dari status progress,
        // tidak perlu cek new_data->status agar lebih akurat dan tidak meleset.
        $hasPendingProgress = $dataEntry
            ? $this->hasPendingProgress($dataEntry->id, $dataLapangan->id)
            : false;

        return view('data-entry.data-lapangan.show', compact(
            'dataLapangan',
            'entryType',
            'latestProgress',
            'hasPendingProgress'
        ));
    }

    /**
     * Upload file OSS atau SIHALAL (entry_type = OSS / SIHALAL).
     *
     * Cek PENDING terlebih dahulu agar tidak terjadi double submit.
     * Status pada table data_lapangans TIDAK langsung berubah saat upload.
     * Status baru berubah ketika superadmin menerima (DITERIMA) progress ini.
     * Setelah upload, redirect ke halaman show data lapangan yang sama
     * agar data entry bisa memantau status tanpa terlempar ke halaman lain.
     */
    public function uploadFile(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'file'      => 'required|mimes:pdf|max:5120',
            'file_type' => 'required|in:oss,sihalal',
        ]);

        $dataEntry = $this->getAuthDataEntry();

        // Cegah double submit — tolak jika masih ada PENDING
        if ($this->hasPendingProgress($dataEntry->id, $dataLapangan->id)) {
            return redirect()
                ->route('data-entry.data-lapangan.show', $dataLapangan->hashed_id)
                ->with('error', 'Masih ada progress yang menunggu review superadmin. Silakan tunggu sebelum upload ulang.');
        }

        $fileType     = $request->file_type;
        $uploadedFile = $request->file('file');
        $uploadResult = $this->fileService->uploadFile($dataLapangan, $uploadedFile, $fileType);

        // Tentukan status target untuk disimpan di new_data progress,
        // tapi JANGAN update status data_lapangans sekarang.
        // Status akan diupdate oleh superadmin saat menerima progress.
        $newStatus = $this->statusService->determineStatusByFileType($fileType);

        // Track progress — status PENDING, belum masuk penagihan.
        $this->trackDataEntryProgress(
            $dataLapangan,
            $dataEntry,
            $fileType,
            $newStatus,
            $uploadedFile->getClientOriginalName()
        );

        $message = 'File ' . strtoupper($fileType) . ' berhasil diupload. Menunggu review superadmin.';

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
            } else {
                $message .= ' Namun notifikasi WhatsApp gagal dikirim.';
                return redirect()
                    ->route('data-entry.data-lapangan.show', $dataLapangan->hashed_id)
                    ->with('warning', $message);
            }
        }

        // Redirect eksplisit ke halaman show data lapangan yang sama,
        // bukan redirect()->back() yang bisa terlempar ke halaman progress
        // tergantung dari mana user datang.
        return redirect()
            ->route('data-entry.data-lapangan.show', $dataLapangan->hashed_id)
            ->with('success', $message);
    }

    /**
     * Kirim permintaan update status ke PROGRESS SIHALAL (entry_type = SIHALAL).
     *
     * Cek PENDING terlebih dahulu agar tidak terjadi double submit.
     * Status pada table data_lapangans TIDAK langsung berubah.
     * Status baru berubah ketika superadmin menerima (DITERIMA) progress ini.
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        try {
            $request->validate([
                'email_sihalal' => ['required', 'email', 'max:255'],
            ]);

            $dataLapangan = DataLapangan::findOrFail($id);
            $dataEntry    = $this->getAuthDataEntry();

            // Cegah double submit — tolak jika masih ada PENDING
            if ($this->hasPendingProgress($dataEntry->id, $dataLapangan->id)) {
                return redirect()->back()->with('error', 'Permintaan sebelumnya masih menunggu review superadmin.');
            }

            if (!in_array($dataLapangan->status, ['PROGRESS OSS', 'DITOLAK'])) {
                return redirect()->back()->with('error', 'Update status hanya dapat dilakukan dari status PROGRESS OSS atau DITOLAK.');
            }

            $newStatus = 'PROGRESS SIHALAL';

            // Simpan email_sihalal ke database
            $dataLapangan->email_sihalal = $request->email_sihalal;
            $dataLapangan->save();

            $this->trackDataEntryProgress(
                $dataLapangan,
                $dataEntry,
                'status_update',
                $newStatus,
                'N/A'
            );

            return redirect()->back()->with('success', 'Permintaan update ke PROGRESS SIHALAL telah dikirim. Menunggu review superadmin.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate status: ' . $e->getMessage());
        }
    }

    /**
     * Resubmit setelah revisi — data entry mengupdate keterangan atau file ulang.
     * Dipanggil dari tombol revisi di halaman show.
     *
     * Update record yang sama (REVISI → PENDING) agar riwayat tetap pada satu record.
     * Keterangan update disimpan di new_data agar tidak bentrok dengan keterangan_revisi
     * yang diisi oleh superadmin.
     */
    public function resubmit(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'keterangan_update' => 'required|string|max:1000',
        ]);

        $dataEntry = $this->getAuthDataEntry();

        // Ambil progress terakhir yang berstatus REVISI untuk data lapangan ini
        $progressRevisi = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('data_lapangan_id', $dataLapangan->id)
            ->where('status', 'REVISI')
            ->latest()
            ->first();

        if (!$progressRevisi) {
            return redirect()->back()->with('error', 'Tidak ada data revisi yang perlu diupdate.');
        }

        // Update record yang sama — REVISI kembali ke PENDING
        // Keterangan update disimpan di new_data, bukan kolom terpisah
        $progressRevisi->update([
            'status'      => 'PENDING',
            'new_data'    => array_merge($progressRevisi->new_data ?? [], [
                'keterangan_update' => $request->keterangan_update,
            ]),
            'actioned_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Resubmit berhasil. Menunggu review dari superadmin.');
    }
}
