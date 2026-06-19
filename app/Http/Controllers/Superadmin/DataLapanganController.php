<?php

namespace App\Http\Controllers\Superadmin;

use App\Exports\DataLapangansExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\DataLapanganRequest;
use App\Models\DataEntryProgress;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Verifikator;
use App\Services\Superadmin\DataLapanganService;
use App\Services\Superadmin\FileService;
use App\Services\Superadmin\ImageDownloadService;
use App\Services\Superadmin\ImageService;
use App\Services\Superadmin\NotificationService;
use App\Services\Superadmin\PdfService;
use App\Services\Superadmin\StatusService;
use App\Traits\HasRoutePrefix;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DataLapanganController extends Controller
{
    use HasRoutePrefix;

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
    public function index(): View
    {
        // Payment stats for the summary cards
        $cutoff = Carbon::create(2026, 5, 1);
        $allData = DataLapangan::select('id', 'status_pembayaran', 'created_at')
            ->where('status', 'TERBIT SH')
            ->get();

        $paymentStats = [
            'pending_count' => 0, 'pending_total' => 0,
            'pengajuan_count' => 0, 'pengajuan_total' => 0,
            'dibayar_count' => 0, 'dibayar_total' => 0,
        ];
        foreach ($allData as $item) {
            $tagihan = Carbon::parse($item->created_at)->lt($cutoff) ? 50000 : 60000;
            $key = strtolower($item->status_pembayaran);
            if (isset($paymentStats["{$key}_count"])) {
                $paymentStats["{$key}_count"]++;
                $paymentStats["{$key}_total"] += $tagihan;
            }
        }

        $routePrefix = $this->routePrefix();

        return view('superadmin.data-lapangan.index', compact('paymentStats', 'routePrefix'));
    }

    /**
     * Return Yajra DataTables JSON for data-lapangan listing.
     */
    public function data(Request $request)
    {
        $cutoff = Carbon::create(2026, 5, 1);

        // LEFT JOIN enumerators so Yajra can search/sort on enumerator name
        $query = DataLapangan::query()
            ->select('data_lapangans.*', 'enumerators.nama_lengkap as enumerator_nama')
            ->leftJoin('enumerators', 'enumerators.id', '=', 'data_lapangans.enumerator_id');

        // Custom filters sent as extra AJAX params from the view
        if ($request->filled('status_filter')) {
            $query->where('data_lapangans.status', $request->status_filter);
        }
        if ($request->filled('payment_filter')) {
            $query->where('data_lapangans.status_pembayaran', $request->payment_filter);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            // Per-column filterColumn so global search works on each searchable column
            ->filterColumn('nama_pu', fn ($q, $k) => $q->where('data_lapangans.nama_pu', 'like', "%{$k}%"))
            ->filterColumn('no_registrasi', fn ($q, $k) => $q->where('data_lapangans.no_registrasi', 'like', "%{$k}%"))
            ->filterColumn('nik', fn ($q, $k) => $q->where('data_lapangans.nik', 'like', "%{$k}%"))
            ->filterColumn('enumerator_nama', fn ($q, $k) => $q->where('enumerators.nama_lengkap', 'like', "%{$k}%"))
            ->addColumn('tanggal', fn ($dl) => $dl->created_at ? $dl->created_at->format('d/m/Y') : '-')
            ->addColumn('pendamping_cell', function ($dl) {
                $nama = e($dl->enumerator_nama ?? '-');

                return '<span style="font-size:12.5px;">'.$nama.'</span>';
            })
            ->addColumn('status_badge', function ($dl) {
                $map = [
                    'Pending' => '#F59E0B:#FEF3C7',
                    'Terverifikasi' => '#2563EB:#DBEAFE',
                    'Progress OSS' => '#7C3AED:#EDE9FE',
                    'Progress SIHALAL' => '#0891B2:#CFFAFE',
                    'Terbit SH' => '#16A34A:#DCFCE7',
                    'Ditolak' => '#DC2626:#FEE2E2',
                    'Revisi' => '#D97706:#FEF3C7',
                ];
                $status = $dl->status ?? 'Pending';
                [$color, $bg] = explode(':', $map[$status] ?? '#6B7280:#F3F4F6');

                return '<span class="adm-badge" style="background:'.$bg.';color:'.$color.';border:1px solid '.$color.'33;">'.e($status).'</span>';
            })
            ->addColumn('payment_badge', function ($dl) {
                $map = [
                    'PENDING' => '#D97706:#FEF3C7',
                    'PENGAJUAN' => '#2563EB:#DBEAFE',
                    'DIBAYAR' => '#16A34A:#DCFCE7',
                ];
                $sp = strtoupper($dl->status_pembayaran ?? 'PENDING');
                [$color, $bg] = explode(':', $map[$sp] ?? '#6B7280:#F3F4F6');

                return '<span class="adm-badge" style="background:'.$bg.';color:'.$color.';border:1px solid '.$color.'33;">'.e($sp).'</span>';
            })
            ->addColumn('tagihan_cell', function ($dl) use ($cutoff) {
                if ($dl->status !== 'TERBIT SH') {
                    return '-';
                }
                $tagihan = Carbon::parse($dl->created_at)->lt($cutoff) ? 50000 : 60000;

                return 'Rp '.number_format($tagihan, 0, ',', '.');
            })
            ->addColumn('locked_icon', function ($dl) {
                if ($dl->is_being_edited && $dl->edit_expires_at && now()->lt($dl->edit_expires_at)) {
                    return '<button class="adm-btn warning icon-only btn-force-unlock" data-id="'.e($dl->hashed_id).'" title="Terkunci — klik untuk paksa buka">
                        <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                    </button>';
                }

                return '';
            })
            ->addColumn('aksi', function ($dl) {
                $showUrl = route($this->routePrefix().'.data-lapangans.show', $dl->hashed_id);
                $deleteUrl = route($this->routePrefix().'.data-lapangans.destroy', $dl->hashed_id);
                $toggleUrl = route($this->routePrefix().'.data-lapangans.toggle-unlock', $dl->hashed_id);
                $unlocked = $dl->is_unlocked_for_data_entry;
                $unlockClass = $unlocked ? 'adm-btn success' : 'adm-btn warning';
                $unlockTitle = $unlocked ? 'Kunci dari Data Entry' : 'Buka untuk Data Entry';
                $unlockIcon = $unlocked
                    ? '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>'
                    : '<svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>';

                return '<div class="adm-actions" style="justify-content:center;gap:4px;">
                    <a class="adm-btn primary icon-only" href="'.$showUrl.'" title="Lihat">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <button class="'.$unlockClass.' icon-only btn-toggle-unlock" data-id="'.e($dl->hashed_id).'" data-url="'.e($toggleUrl).'" title="'.$unlockTitle.'">
                        '.$unlockIcon.'
                    </button>
                    <form action="'.$deleteUrl.'" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin hapus data ini?\')">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="adm-btn danger icon-only" title="Hapus">
                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>';
            })
            ->addColumn('checkbox', function ($dl) {
                if ($dl->status_pembayaran === 'PENGAJUAN') {
                    return '<input type="checkbox" class="row-checkbox adm-checkbox" value="'.e($dl->hashed_id).'">';
                }

                return '';
            })
            ->rawColumns(['pendamping_cell', 'status_badge', 'payment_badge', 'locked_icon', 'aksi', 'checkbox'])
            ->make(true);
    }

    /**
     * Toggle visibility of a DataLapangan record for the data_entry role.
     */
    public function toggleUnlockForDataEntry(string $id): JsonResponse
    {
        $dataLapangan = DataLapangan::findByHashedId($id);

        if (! $dataLapangan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $dataLapangan->update([
            'is_unlocked_for_data_entry' => ! $dataLapangan->is_unlocked_for_data_entry,
        ]);

        $unlocked = $dataLapangan->is_unlocked_for_data_entry;

        return response()->json([
            'success' => true,
            'unlocked' => $unlocked,
            'message' => $unlocked ? 'Data dibuka untuk Data Entry.' : 'Data dikunci dari Data Entry.',
        ]);
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
     * Export Data Revisi ke PDF
     */
    public function exportRevisiPdf(): Response
    {
        $dataLapangans = $this->dataLapanganService->getDataRevisiAll();

        // Group berdasarkan enumerator
        $grouped = $dataLapangans->groupBy(fn ($item) => $item->enumerator->nama_lengkap ?? 'Tidak Diketahui');

        $exportedAt = now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm');
        $tahun = now()->year;
        $bulan = now()->month;

        $pdf = Pdf::loadView('superadmin.data-lapangan.partials.data-revisi-pdf', compact(
            'grouped',
            'dataLapangans',
            'exportedAt',
            'tahun',
            'bulan',
        ))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'enable_css_float' => true,
            ]);

        $filename = 'data-revisi-'
            .$tahun
            .str_pad($bulan, 2, '0', STR_PAD_LEFT)
            .'-'.now()->format('His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Kirim notifikasi revisi untuk satu data — pakai hashedId
     */
    public function sendRevisiNotification($hashedId): JsonResponse
    {
        try {
            $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
            $dataLapangan->load('enumerator');
            $result = $this->notificationService->sendRevisiNotification($dataLapangan);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
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
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
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

        $fileName = 'data-lapangan-'.date('Y-m-d-His').'.xlsx';

        return Excel::download(new DataLapangansExport($filters), $fileName);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dataLapangan = new DataLapangan;
        $enumerators = Enumerator::orderBy('nama_lengkap')->get();

        return view('publik.form', compact('dataLapangan', 'enumerators'));
    }

    /**
     * Upload a file based on the given file type (OSS / SIHALAL)
     */
    public function uploadFile(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
            'file_type' => 'required|in:oss,sihalal',
        ]);

        $fileType = $request->file_type;
        $uploadResult = $this->fileService->uploadFile($dataLapangan, $request->file('file'), $fileType);

        // Update status
        $newStatus = $this->statusService->determineStatusByFileType($fileType);
        $dataLapangan->status = $newStatus;
        $dataLapangan->save();

        $statusMessage = "Status diubah menjadi {$newStatus}";
        $message = 'File '.strtoupper($fileType).' berhasil diupload. '.$statusMessage;

        // Handle notifications
        if ($fileType === 'oss' && $uploadResult['is_first_upload']) {
            $notificationSent = $this->notificationService->sendOSSNotification($dataLapangan);
            $message .= $notificationSent
                ? ' Notifikasi WhatsApp telah dikirim ke koordinator.'
                : ' Namun notifikasi WhatsApp gagal dikirim ke koordinator.';
        }

        if ($fileType === 'sihalal' && $uploadResult['is_first_upload']) {
            $notificationSent = $this->notificationService->sendSihalalUploadNotification($dataLapangan);
            $message .= $notificationSent
                ? ' Link sertifikat halal telah dikirim ke PU.'
                : ' Namun notifikasi WhatsApp gagal dikirim.';

            return redirect()->back()->with($notificationSent ? 'success' : 'warning', $message);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Download foto KTP yang tersimpan di storage.
     */
    public function downloadFotoKTP($hashedId)
    {
        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
        $fotoPath = $this->imageService->getFotoKTPPath($dataLapangan);

        if (! $fotoPath) {
            return back()->with('error', 'File foto KTP tidak ditemukan');
        }

        try {
            $tempPath = $this->imageService->compressKTPImage($fotoPath, $hashedId);
            $fileName = $this->imageService->generateSafeFilename($dataLapangan->nama_pu);

            return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download foto Pendamping
     */
    public function downloadFotoPendamping($hashedId)
    {
        try {
            $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
            $downloadData = $this->imageDownloadService->downloadFotoPendamping($dataLapangan);

            return response()->download($downloadData['path'], $downloadData['filename'])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Download foto Produk (produk utama / foto_produk)
     */
    public function downloadFotoProduk($hashedId)
    {
        try {
            $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
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

        if (! $deleted) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        // Update status when file is deleted
        $statusResult = $this->statusService->determineStatusAfterDeletion($fileType, $dataLapangan);
        $dataLapangan->status = $statusResult['status'];
        $dataLapangan->save();

        return redirect()->back()->with(
            'success',
            'File '.strtoupper($fileType).' berhasil dihapus. '.$statusResult['message']
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
    public function updateKeterangan(Request $request, $hashedId): RedirectResponse
    {
        $request->validate([
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $dataLapanganObj = DataLapangan::findByHashedIdOrFail($hashedId);
        $this->dataLapanganService->updateKeterangan($dataLapanganObj->id, $request->keterangan);

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Clear/hapus keterangan revisi (AJAX endpoint)
     */
    public function clearKeterangan(Request $request, $hashedId): \Illuminate\Http\JsonResponse
    {
        $dataLapanganObj = DataLapangan::findByHashedIdOrFail($hashedId);
        $this->dataLapanganService->updateKeterangan($dataLapanganObj->id, null);

        return response()->json(['success' => true, 'message' => 'Keterangan berhasil dihapus']);
    }

    public function updateEmail(Request $request, $hashedId): RedirectResponse
    {
        $request->validate([
            'email_prefix' => 'required|string|max:100|regex:/^[a-zA-Z0-9._+-]+$/',
            'verifikator_id' => 'nullable|exists:verifikators,id',
            'tanggal_verifikasi' => 'nullable|date',
        ]);

        // Gabungkan prefix + domain
        $email = $request->email_prefix.'@kawulohalal.id';

        $dlObjEmail = DataLapangan::findByHashedIdOrFail($hashedId);

        // Simpan email lengkap ke DB (tanpa password)
        $this->dataLapanganService->updateEmail(
            $dlObjEmail->id,
            $email,
            $request->verifikator_id,
            $request->tanggal_verifikasi
        );

        return redirect()->back()->with('success', 'Email '.$email.' berhasil disimpan dan data diverifikasi.');
    }

    /**
     * Admin Umum mengajukan pembayaran ke Superadmin (PENDING → PENGAJUAN).
     */
    public function ajukanPembayaran($hashedId): RedirectResponse
    {
        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);

        if ($dataLapangan->status !== 'TERBIT SH') {
            return redirect()->back()->with('error', 'Hanya data berstatus TERBIT SH yang dapat diajukan.');
        }

        if ($dataLapangan->status_pembayaran !== 'PENDING') {
            return redirect()->back()->with('warning', 'Status pembayaran sudah '.$dataLapangan->status_pembayaran.'.');
        }

        $dataLapangan->update(['status_pembayaran' => 'PENGAJUAN']);

        return redirect()->back()->with('success', 'Pengajuan pembayaran berhasil dikirim ke Superadmin.');
    }

    /**
     * Admin Umum — Blast ajukan semua data PENDING → PENGAJUAN.
     */
    public function bulkAjukanPembayaran(Request $request): JsonResponse
    {
        $data = DataLapangan::where('status', 'TERBIT SH')
            ->where('status_pembayaran', 'PENDING')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data PENDING yang dapat diajukan.',
            ], 422);
        }

        $updated = $data->each(fn ($d) => $d->update(['status_pembayaran' => 'PENGAJUAN']));

        return response()->json([
            'success' => true,
            'message' => $data->count() . ' data berhasil diajukan ke Superadmin.',
            'updated' => $data->count(),
        ]);
    }

    /**
     * Superadmin — Ambil daftar data yang sedang PENGAJUAN untuk ditampilkan di modal approval.
     */
    public function getPengajuanData(Request $request): JsonResponse
    {
        $cutoff = \Carbon\Carbon::create(2026, 5, 1);

        $items = DataLapangan::whereIn('status_pembayaran', ['PENDING', 'PENGAJUAN'])
            ->whereRaw('UPPER(status) = ?', ['TERBIT SH'])
            ->with('enumerator')
            ->orderBy('status_pembayaran', 'asc') // PENDING first, then PENGAJUAN
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($dl) use ($cutoff) {
                $nominal = \Carbon\Carbon::parse($dl->created_at)->lt($cutoff) ? 50000 : 60000;
                return [
                    'hashed_id'      => $dl->hashed_id,
                    'no_registrasi'  => $dl->no_registrasi,
                    'nama_pu'        => $dl->nama_pu,
                    'nik'            => $dl->nik,
                    'pendamping'     => $dl->enumerator->nama_lengkap ?? '-',
                    'nominal'        => $nominal,
                    'nominal_fmt'    => 'Rp ' . number_format($nominal, 0, ',', '.'),
                    'status_pembayaran' => $dl->status_pembayaran,
                ];
            });

        $total = $items->sum('nominal');

        return response()->json([
            'success' => true,
            'data'    => $items,
            'total'   => $total,
            'total_fmt' => 'Rp ' . number_format($total, 0, ',', '.'),
            'count'   => $items->count(),
        ]);
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $prefix = $request->query('prefix');

        if (empty($prefix)) {
            return response()->json(['exists' => false]);
        }

        $email = $prefix.'@kawulohalal.id';
        $exists = DataLapangan::where('email', $email)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Upload gambar secara sekuensial (AJAX).
     */
    public function uploadFileSequintal(Request $request, $type): JsonResponse
    {
        if (! $this->fileService->isAllowedType($type)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe file tidak valid',
            ], 400);
        }

        $request->validate([
            $type => 'required|image|mimes:jpeg,jpg,png|max:10240',
        ]);

        try {
            if (! $request->hasFile($type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan',
                ], 400);
            }

            $path = $this->fileService->uploadImageSequential($request->file($type), $type);

            return response()->json([
                'success' => true,
                'path' => $path,
                'message' => ucwords(str_replace('_', ' ', $type)).' berhasil diupload',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: '.$e->getMessage(),
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
                ->with('error', 'Gagal menyimpan data: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update status pembayaran SATU data lapangan menjadi DIBAYAR.
     */
    public function updateStatusPayment(Request $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $this->processPembayaran($dataLapangan);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diubah menjadi DIBAYAR');
    }

    /**
     * Bulk update status pembayaran — ids[] berisi hashed_id, di-decode dulu.
     */
    public function bulkUpdateStatusPayment(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|string',
        ]);

        // Decode semua hashed_id ke real ID
        $realIds = collect($request->ids)
            ->map(fn ($hashedId) => DataLapangan::findByHashedId($hashedId)?->id)
            ->filter()
            ->values()
            ->all();

        if (empty($realIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid yang ditemukan.',
            ], 422);
        }

        $dataLapangans = DataLapangan::whereIn('id', $realIds)
            ->where('status', 'TERBIT SH')
            ->where('status_pembayaran', 'PENGAJUAN')
            ->get();

        if ($dataLapangans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang memenuhi syarat untuk diubah.',
            ], 422);
        }

        $updated = 0;
        $errors = [];

        foreach ($dataLapangans as $dataLapangan) {
            try {
                $this->processPembayaran($dataLapangan);
                $updated++;
            } catch (\Exception $e) {
                $errors[] = "({$dataLapangan->nama_pu}): {$e->getMessage()}";
                Log::error("bulkUpdateStatusPayment error on ID {$dataLapangan->id}: {$e->getMessage()}");
            }
        }

        $message = "{$updated} data berhasil diubah menjadi DIBAYAR";
        if (! empty($errors)) {
            $message .= '. Beberapa data gagal: '.implode('; ', $errors);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }

    /**
     * Logika inti: update status_pembayaran ke DIBAYAR pada satu instance model.
     */
    private function processPembayaran(DataLapangan $dataLapangan): void
    {
        $dataLapangan->update([
            'status_pembayaran' => 'DIBAYAR',
        ]);

        $this->notificationService->sendPembayaranEnumeratorNotification($dataLapangan);
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
        $dataLapangan->load(['enumerator', 'spotchecks']);

        $verifikators = Verifikator::orderBy('nama_lengkap')->get();

        $dataEntryOSS = DataEntryProgress::with('dataEntry')
            ->where('data_lapangan_id', $dataLapangan->id)
            ->whereHas('dataEntry', fn ($q) => $q->where('entry_type', 'OSS'))
            ->orderBy('actioned_at', 'asc')
            ->first();

        $dataEntrySihalal = DataEntryProgress::with('dataEntry')
            ->where('data_lapangan_id', $dataLapangan->id)
            ->whereHas('dataEntry', fn ($q) => $q->where('entry_type', 'SIHALAL'))
            ->orderBy('actioned_at', 'asc')
            ->first();

        $routePrefix = $this->routePrefix();

        return view('superadmin.data-lapangan.show', compact(
            'dataLapangan',
            'dataEntryOSS',
            'dataEntrySihalal',
            'verifikators', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource — pakai hashedId.
     */
    public function edit($hashedId): View
    {
        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.data-lapangan.edit', compact('dataLapangan', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DataLapanganRequest $request, DataLapangan $dataLapangan): RedirectResponse
    {
        $this->dataLapanganService->update($dataLapangan, $request->validated());

        return Redirect::route($this->routePrefix().'.data-lapangans.index')
            ->with('success', 'DataLapangan updated successfully');
    }

    /**
     * Download foto rumah as PDF — pakai hashedId.
     */
    public function downloadFotoRumahPdf($hashedId)
    {
        try {
            $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
            $pdf = $this->pdfService->generateFotoRumahPdf($dataLapangan);
            $filename = $this->pdfService->generatePdfFilename('Foto_Rumah', $dataLapangan->nama_pu);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage — pakai hashedId.
     */
    public function destroy($hashedId): RedirectResponse
    {
        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
        $this->dataLapanganService->delete($dataLapangan->id);

        return Redirect::route($this->routePrefix().'.data-lapangans.index')
            ->with('success', 'DataLapangan deleted successfully');
    }

    /**
     * Update email sihalal of a data lapangan.
     */
    public function updateEmailSihalal(Request $request, $hashedId): RedirectResponse
    {
        $request->validate([
            'email_sihalal' => 'required|email|max:255',
        ]);

        $dataLapangan = DataLapangan::findByHashedIdOrFail($hashedId);
        $dataLapangan->update(['email_sihalal' => $request->email_sihalal]);

        return redirect()->back()->with('success', 'Email Sihalal berhasil diperbarui');
    }
}
