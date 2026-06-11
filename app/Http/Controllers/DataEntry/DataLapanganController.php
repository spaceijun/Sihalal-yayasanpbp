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
use Yajra\DataTables\Facades\DataTables;

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
     * Ambil DataEntry milik user yang sedang login.
     * Throw exception jika tidak ditemukan agar tidak perlu cek berulang.
     */
    private function getAuthDataEntry(): DataEntry
    {
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();

        if (! $dataEntry) {
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
        $latestProgress = DataEntryProgress::where('data_lapangan_id', $dataLapanganId)
            ->where('action', 'created')
            ->latest('id')
            ->first();

        return $latestProgress?->status === 'PENDING';
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
            'user_id' => Auth::id(),
            'data_entry_id' => $dataEntry->id,
            'data_lapangan_id' => $dataLapangan->id,
            'action' => 'created',
            'status' => 'PENDING',
            'old_data' => [
                'status' => $dataLapangan->status,
            ],
            'new_data' => [
                'file_type' => $fileType,
                'status' => $newStatus,
                'file_name' => $fileName,
            ],
            'actioned_at' => now(),
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
    public function index(): View
    {
        return view('data-entry.data-lapangan.index');
    }

    /**
     * Return Yajra DataTables JSON for data-lapangan listing (data_entry role).
     *
     * Filtering rules (mirroring original API logic):
     *   - entry_type = OSS     → tampilkan status = 'Terverifikasi'   (belum diambil siapapun)
     *   - entry_type = SIHALAL → tampilkan status = 'Progress OSS'    (belum diambil siapapun)
     *   - selain itu           → tidak tampilkan apapun
     *
     * Juga filter berdasarkan koordinator yang di-assign ke user data_entry ini.
     */
    public function data(Request $request)
    {
        // Ambil DataEntry milik user login beserta koordinator yang di-assign
        $dataEntry = DataEntry::where('user_id', Auth::id())
            ->with('koordinators')
            ->first();

        // Jika tidak ada DataEntry atau entry_type tidak dikenali → kembalikan data kosong
        if (!$dataEntry || !in_array($dataEntry->entry_type, ['OSS', 'SIHALAL'])) {
            return DataTables::of(DataLapangan::query()->whereRaw('1 = 0'))
                ->make(true);
        }

        // Tentukan status yang boleh dilihat berdasarkan entry_type
        $targetStatus = $dataEntry->entry_type === 'SIHALAL'
            ? 'PROGRESS OSS'
            : 'TERVERIFIKASI';

        $koordinatorIds = $dataEntry->koordinators->pluck('id');

        $currentUserId = Auth::id();

        $entryType = $dataEntry->entry_type;

        $query = DataLapangan::query()
            ->select('data_lapangans.*', 'enumerators.nama_lengkap as enumerator_nama')
            ->leftJoin('enumerators', 'enumerators.id', '=', 'data_lapangans.enumerator_id')
            ->where('data_lapangans.status', $targetStatus)
            // Tidak ada progress aktif (PENDING/DITERIMA) dari entry_type yang sama
            // Filter harus spesifik per entry_type agar OSS DITERIMA tidak menghalangi SIHALAL
            ->whereDoesntHave('dataEntryProgress', fn ($q) =>
                $q->whereIn('status', ['PENDING', 'DITERIMA'])
                  ->whereHas('dataEntry', fn ($q2) => $q2->where('entry_type', $entryType))
            )
            // Tidak sedang dikunci user lain
            ->where(function ($q) use ($currentUserId) {
                $q->where('data_lapangans.is_being_edited', false)
                  ->orWhereNull('data_lapangans.edit_expires_at')
                  ->orWhere('data_lapangans.edit_expires_at', '<', now())
                  ->orWhere('data_lapangans.edited_by', $currentUserId);
            });

        // Filter berdasarkan koordinator yang di-assign ke user data_entry ini
        if ($koordinatorIds->isNotEmpty()) {
            $query->whereHas('enumerator', fn ($q) =>
                $q->whereIn('koordinator_id', $koordinatorIds));
        }

        // Custom filter: rentang tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('data_lapangans.created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('data_lapangans.created_at', '<=', $request->tanggal_sampai);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->filterColumn('nama_pu',         fn ($q, $k) => $q->where('data_lapangans.nama_pu',    'like', "%{$k}%"))
            ->filterColumn('enumerator_nama',  fn ($q, $k) => $q->where('enumerators.nama_lengkap', 'like', "%{$k}%"))
            ->addColumn('pendamping_cell', fn ($dl) => e($dl->enumerator_nama ?? '-'))
            ->addColumn('nama_produk_cell', function ($dl) {
                $produk = collect([
                    $dl->nama_produk, $dl->nama_produk_2, $dl->nama_produk_3,
                    $dl->nama_produk_4, $dl->nama_produk_5,
                ])->filter()->implode(', ');

                return e($produk ?: '-');
            })
            ->addColumn('status_badge', function ($dl) {
                $map = [
                    'PENDING'          => 'adm-badge-pending',
                    'TERVERIFIKASI'    => 'adm-badge-info',
                    'PROGRESS OSS'     => 'adm-badge-oss',
                    'PROGRESS SIHALAL' => 'adm-badge-sihalal',
                    'TERBIT SH'        => 'adm-badge-terbit',
                    'DITOLAK'          => 'adm-badge-ditolak',
                    'REVISI'           => 'adm-badge-revisi',
                ];
                $cls = $map[$dl->status] ?? 'adm-badge-pending';
                return '<span class="adm-badge '.$cls.'"><span class="dot"></span>'.e($dl->status).'</span>';
            })
            ->addColumn('old_email_sihalal_cell', function ($dl) {
                if ($dl->old_email_sihalal) {
                    return '<span style="font-size:11.5px;color:#DC2626;" title="Email sebelumnya yang ditolak/kedaluwarsa">
                        <i class="las la-history"></i> '.e($dl->old_email_sihalal).'
                    </span>';
                }
                return '<span style="color:#9CA3AF;font-size:11.5px;">—</span>';
            })
            ->addColumn('aksi', function ($dl) use ($dataEntry) {
                $showUrl    = route('data-entry.data-lapangan.show', $dl->hashed_id);
                $isSihalal  = $dataEntry->entry_type === 'SIHALAL';

                // Jika SIHALAL dan email_sihalal sudah terisi → sedang direview admin
                if ($isSihalal && !empty($dl->email_sihalal)) {
                    return '<button class="btn btn-sm btn-secondary" disabled title="'.e($dl->email_sihalal).'">
                        <i class="las la-clock"></i> Sedang Direview Admin
                    </button>';
                }

                return '<a href="'.$showUrl.'" class="btn btn-sm btn-info btn-show-data" data-id="'.e($dl->hashed_id).'">
                    <i class="las la-eye"></i> Show
                </a>';
            })
            ->rawColumns(['status_badge', 'aksi', 'old_email_sihalal_cell'])
            ->make(true);
    }

    /**
     * Acquire an editing lock on a DataLapangan record (web route, session-auth).
     * Uses hashed ID — same format as what the JS views send.
     */
    public function lockData(string $hashedId): \Illuminate\Http\JsonResponse
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        if (! $dataLapangan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Reject if locked by someone else and not yet expired
        if (
            $dataLapangan->is_being_edited &&
            $dataLapangan->edited_by !== Auth::id() &&
            $dataLapangan->edit_expires_at?->isFuture()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data sedang dikerjakan oleh pengguna lain.',
            ], 423);
        }

        $dataLapangan->update([
            'is_being_edited' => true,
            'edited_by' => Auth::id(),
            'edit_expires_at' => now()->addMinutes(50),
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil dikunci.']);
    }

    /**
     * Release the editing lock (web route, session-auth).
     */
    public function unlockData(string $hashedId): \Illuminate\Http\JsonResponse
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        if ($dataLapangan) {
            DataLapangan::where('id', $dataLapangan->id)
                ->where('edited_by', Auth::id())
                ->update([
                    'is_being_edited' => false,
                    'edited_by' => null,
                    'edit_expires_at' => null,
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Data dilepas.']);
    }

    /**
     * Unlock via sendBeacon (browser close/navigate away).
     */
    public function unlockBeacon(string $hashedId): \Illuminate\Http\JsonResponse
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);

        if ($dataLapangan) {
            DataLapangan::where('id', $dataLapangan->id)
                ->where('edited_by', Auth::id())
                ->update([
                    'is_being_edited' => false,
                    'edited_by' => null,
                    'edit_expires_at' => null,
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedId($hashedId);
        $dataEntry = DataEntry::where('user_id', Auth::id())->first();
        $entryType = $dataEntry?->entry_type;

        $latestProgress = DataEntryProgress::where('data_lapangan_id', $dataLapangan->id)
            ->where('action', 'created')
            ->latest('id')
            ->first();

        $hasPendingProgress = $latestProgress?->status === 'PENDING';
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
            'file' => 'required|mimes:pdf|max:5120',
            'file_type' => 'required|in:oss,sihalal',
        ]);

        $dataEntry = $this->getAuthDataEntry();

        // Cegah double submit — tolak jika masih ada PENDING
        if ($this->hasPendingProgress($dataEntry->id, $dataLapangan->id)) {
            return redirect()
                ->route('data-entry.data-lapangan.show', $dataLapangan->hashed_id)
                ->with('error', 'Masih ada progress yang menunggu review superadmin. Silakan tunggu sebelum upload ulang.');
        }

        $fileType = $request->file_type;
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

        $message = 'File '.strtoupper($fileType).' berhasil diupload. Menunggu review superadmin.';

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
            $dataEntry = $this->getAuthDataEntry();

            // Cegah double submit — tolak jika masih ada PENDING
            if ($this->hasPendingProgress($dataEntry->id, $dataLapangan->id)) {
                return redirect()->back()->with('error', 'Permintaan sebelumnya masih menunggu review superadmin.');
            }

            if (! in_array($dataLapangan->status, ['PROGRESS OSS', 'DITOLAK'])) {
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
            return redirect()->back()->with('error', 'Gagal mengupdate status: '.$e->getMessage());
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

        if (! $progressRevisi) {
            return redirect()->back()->with('error', 'Tidak ada data revisi yang perlu diupdate.');
        }

        // Update record yang sama — REVISI kembali ke PENDING
        // Keterangan update disimpan di new_data, bukan kolom terpisah
        $progressRevisi->update([
            'status' => 'PENDING',
            'new_data' => array_merge($progressRevisi->new_data ?? [], [
                'keterangan_update' => $request->keterangan_update,
            ]),
            'actioned_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Resubmit berhasil. Menunggu review dari superadmin.');
    }
}
