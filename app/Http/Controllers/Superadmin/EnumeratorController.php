<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Traits\HasRoutePrefix;
use App\Http\Requests\EnumeratorRequest;
use App\Models\DataBank;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use App\Models\User;
use App\Services\Superadmin\EnumeratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use Yajra\DataTables\Facades\DataTables;


class EnumeratorController extends Controller
{
    use HasRoutePrefix;

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $routePrefix = $this->routePrefix();
        return view('superadmin.enumerator.index', compact('routePrefix'));
    }

    /**
     * Return DataTables JSON for enumerator listing.
     */
    public function data(Request $request)
    {
        // Rentang periode aktif (tgl 25 bulan lalu s.d. tgl 25 bulan ini)
        $startDate = now()->day >= 25
            ? now()->startOfDay()->day(25)
            : now()->subMonth()->day(25)->startOfDay();
        $endDate = $startDate->copy()->addMonth();

        $query = Enumerator::with('koordinator', 'bank')
            ->withCount([
                'dataLapangans as data_bulan_ini' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                },
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('no_reg', fn ($e) => '<span class="adm-mono" style="font-size:12px;font-weight:600;color:var(--adm-blue);">KH-'.e($e->no_registrasi).'</span>')
            ->addColumn('nama_cell', function ($e) {
                $inisial = strtoupper(substr($e->nama_lengkap, 0, 2));

                return '<div class="adm-name-cell">
                    <div class="adm-avatar" style="background:var(--adm-blue-lt);color:var(--adm-blue);">'.$inisial.'</div>
                    <strong style="font-size:13px;">'.e($e->nama_lengkap).'</strong>
                </div>';
            })
            ->addColumn('data_bulan', function ($e) {
                $jumlah = $e->data_bulan_ini ?? 0;
                $kurang = $jumlah < 20;
                $bg = $kurang ? 'var(--adm-red-lt,#fff0f0)' : 'var(--adm-green-lt,#f0fff4)';
                $color = $kurang ? 'var(--adm-red,#e03131)' : 'var(--adm-green,#2f9e44)';
                $sub = $kurang
                    ? '<span style="font-size:10px;color:var(--adm-red,#e03131);font-weight:500;white-space:nowrap;">⚠ Kurang '.(20 - $jumlah).' data</span>'
                    : '<span style="font-size:10px;color:var(--adm-green,#2f9e44);font-weight:500;">✓ Tercapai</span>';

                return '<div style="display:flex;flex-direction:column;align-items:center;gap:4px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;min-width:36px;height:28px;border-radius:8px;font-size:13px;font-weight:700;padding:0 10px;background:'.$bg.';color:'.$color.';border:1px solid '.$color.';">'.$jumlah.'</span>
                    '.$sub.'
                </div>';
            })
            ->addColumn('rekening', function ($e) {
                if ($e->bank && $e->no_rekening && $e->nama_rekening) {
                    return '<span style="font-size:12px;color:var(--adm-text-muted);">'.e($e->bank->name).', '.e($e->no_rekening).' an. '.e($e->nama_rekening).'</span>';
                }

                return '<span style="color:var(--adm-text-faint);">—</span>';
            })
            ->addColumn('status_badge', function ($e) {
                return $e->status === 'Aktif'
                    ? '<span class="adm-badge adm-badge-success">Aktif</span>'
                    : '<span class="adm-badge adm-badge-nonaktif">Tidak Aktif</span>';
            })
            ->addColumn('aksi', function ($e) {
                $generateBtn = '';
                if (! $e->user_id) {
                    $generateBtn = '<button type="button" class="adm-btn warning btn-generate-user"
                        data-id="'.$e->id.'" data-nama="'.e($e->nama_lengkap).'" data-hp="'.e($e->telephone).'"
                        title="Generate akun user">
                        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        User
                    </button>';
                }

                return '<div class="adm-actions" style="justify-content:center;flex-wrap:wrap;">
                    '.$generateBtn.'
                    <a class="adm-btn icon-only" href="'.route($this->routePrefix() . '.enumerators.gallery', $e->hashed_id).'" title="Galeri">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </a>
                    <a class="adm-btn primary icon-only" href="'.route($this->routePrefix() . '.enumerators.show', $e->hashed_id).'" title="Detail">
                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a class="adm-btn success icon-only" href="'.route($this->routePrefix() . '.enumerators.edit', $e->hashed_id).'" title="Edit">
                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button type="button" class="adm-btn danger icon-only btn-delete" data-id="'.$e->hashed_id.'" title="Hapus">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                </div>';
            })
            ->rawColumns(['no_reg', 'nama_cell', 'data_bulan', 'rekening', 'status_badge', 'aksi'])
            ->make(true);
    }

    /**
     * Generate akun user untuk enumerator (digunakan oleh tombol di tabel).
     */
    public function generateUser($id)
    {
        $enumerator = Enumerator::findOrFail($id);

        if ($enumerator->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Enumerator ini sudah memiliki akun user.',
            ], 422);
        }

        DB::transaction(function () use ($enumerator) {
            $telephone = $enumerator->telephone;
            $email = $telephone.'@kawulohalal.id';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $enumerator->nama_lengkap,
                    'telephone' => $telephone,
                    'password' => Hash::make('enumkh123'),
                    'role' => 'enumerator',
                ]
            );

            if ($user->wasRecentlyCreated) {
                $user->assignRole('enumerator');
            }

            $enumerator->update(['user_id' => $user->id]);
        });

        return response()->json([
            'success' => true,
            'message' => "User berhasil digenerate dengan email: {$enumerator->telephone}@kawulohalal.id",
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $enumerator = new Enumerator;
        $koordinators = Koordinator::all();

        $routePrefix = $this->routePrefix();

        return view('superadmin.enumerator.create', compact('enumerator', 'koordinators', 'routePrefix'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnumeratorRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('foto_diri')) {
            $image = $request->file('foto_diri');
            $extension = $image->getClientOriginalExtension();
            $imageName = time().'_'.uniqid().'.'.$extension;
            $image->storeAs('foto-diri', $imageName, 'public');
            $validatedData['foto_diri'] = 'foto-diri/'.$imageName;
        }

        DB::transaction(function () use ($validatedData) {
            $lastNo = Enumerator::lockForUpdate()
                ->orderBy('no_registrasi', 'desc')
                ->value('no_registrasi');

            $nextNo = $lastNo ? ((int) $lastNo + 1) : 1;

            if ($nextNo > 999) {
                throw new \Exception('No registrasi sudah penuh');
            }

            $noRegistrasi = str_pad($nextNo, 3, '0', STR_PAD_LEFT);

            Enumerator::create(array_merge(
                $validatedData,
                ['no_registrasi' => $noRegistrasi]
            ));
        });

        return Redirect::route($this->routePrefix() . '.enumerators.index')
            ->with('success', 'Enumerator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load(['koordinator', 'bank', 'aktivasiLogs']);

        $routePrefix = $this->routePrefix();

        return view('superadmin.enumerator.show', compact('enumerator', 'routePrefix'));
    }

    /**
     * Tampilkan galeri foto per enumerator (foto_pendamping & foto_produk)
     */
    public function gallery($hashedId): View
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load(['koordinator', 'dataLapangans']);

        $routePrefix = $this->routePrefix();

        return view('superadmin.enumerator.gallery', compact('enumerator', 'routePrefix'));
    }

    /**
     * Download foto dari data lapangan milik enumerator
     */
    public function downloadFoto($hashedId, $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('dataLapangans');

        $allowed = ['foto_pendamping', 'foto_produk'];

        if (! in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        $data = $enumerator->dataLapangans
            ->whereNotNull($type)
            ->first();

        if (! $data || ! $data->$type) {
            abort(404, 'Foto tidak ditemukan.');
        }

        $path = storage_path('app/public/'.$data->$type);

        if (! file_exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = $type.'_'.$enumerator->no_registrasi.'_'.$data->id.'.'.$extension;

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename);
    }

    /**
     * Download foto per entri data lapangan
     * Route: /enumerators/{id}/download-foto/{dataId}/{type}
     */
    public function downloadFotoByEntry($hashedId, $dataId, $type): StreamedResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        $allowed = ['foto_pendamping', 'foto_produk'];

        if (! in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        $data = $enumerator->dataLapangans()->findOrFail($dataId);

        if (! $data->$type) {
            abort(404, 'Foto tidak ditemukan.');
        }

        $path = storage_path('app/public/'.$data->$type);

        if (! file_exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename = $type.'_'.$enumerator->no_registrasi.'_'.$data->id.'.'.$extension;

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename);
    }

    /**
     * Download semua foto enumerator dalam format ZIP
     * Route: /enumerators/{id}/download-zip?type=all|foto_pendamping|foto_produk
     */
    public function downloadZip(Request $request, $hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('dataLapangans');

        $type = $request->input('type', 'all');
        $allowed = ['all', 'foto_pendamping', 'foto_produk'];

        if (! in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        // Kumpulkan file yang akan dimasukkan ke ZIP
        $files = [];

        if ($type === 'all' || $type === 'foto_pendamping') {
            foreach ($enumerator->dataLapangans->whereNotNull('foto_pendamping') as $data) {
                $path = storage_path('app/public/' . $data->foto_pendamping);
                if (file_exists($path)) {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $namaPu = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data->nama_pu ?? 'noname');
                    $files[] = [
                        'path'     => $path,
                        'filename' => 'pendamping/' . $data->id . '_' . $namaPu . '.' . $ext,
                    ];
                }
            }
        }

        if ($type === 'all' || $type === 'foto_produk') {
            foreach ($enumerator->dataLapangans->whereNotNull('foto_produk') as $data) {
                $path = storage_path('app/public/' . $data->foto_produk);
                if (file_exists($path)) {
                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                    $namaProduk = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data->nama_produk ?? 'noname');
                    $files[] = [
                        'path'     => $path,
                        'filename' => 'produk/' . $data->id . '_' . $namaProduk . '.' . $ext,
                    ];
                }
            }
        }

        if (empty($files)) {
            return redirect()->back()->with('error', 'Tidak ada foto yang tersedia untuk diunduh.');
        }

        // Buat file ZIP sementara
        $tempZip = tempnam(sys_get_temp_dir(), 'gallery_zip_') . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file ZIP.');
        }

        foreach ($files as $file) {
            $zip->addFile($file['path'], $file['filename']);
        }

        $zip->close();

        $typeLabel = match ($type) {
            'foto_pendamping' => 'pendamping',
            'foto_produk'     => 'produk',
            default           => 'semua-foto',
        };

        $zipFilename = 'gallery_' . $typeLabel . '_KH-' . $enumerator->no_registrasi . '_' . now()->format('Ymd_His') . '.zip';

        return response()->download($tempZip, $zipFilename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generate and display Surat Tugas
     */
    public function suratTugas($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('koordinator');

        $routePrefix = $this->routePrefix();

        return view('superadmin.enumerator.partials.surat', compact('enumerator', 'routePrefix'));
    }

    /**
     * Generate ID Card as HTML
     */
    public function idCard($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        $routePrefix = $this->routePrefix();

        return view('superadmin.enumerator.partials.idcard', compact('enumerator', 'routePrefix'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $koordinators = Koordinator::all();
        $banks = DataBank::orderBy('name')->get();

        $routePrefix = $this->routePrefix();

        return view('superadmin.enumerator.edit', compact('enumerator', 'koordinators', 'banks', 'routePrefix'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EnumeratorRequest $request, Enumerator $enumerator): RedirectResponse
    {
        $enumerator->update($request->validated());

        return Redirect::route($this->routePrefix() . '.enumerators.index')
            ->with('success', 'Enumerator updated successfully');
    }

    /**
     * Aktifkan kembali enumerator yang berstatus Tidak Aktif.
     * Membutuhkan upload Surat Pernyataan Pengaktifan Kembali.
     */
    public function aktivasi(Request $request, $hashedId): RedirectResponse
    {
        $request->validate([
            'surat_pernyataan' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'catatan'          => ['nullable', 'string', 'max:500'],
        ], [
            'surat_pernyataan.required' => 'Surat Pernyataan Pengaktifan wajib diupload.',
            'surat_pernyataan.mimes'    => 'Format file harus PDF, JPG, atau PNG.',
            'surat_pernyataan.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        try {
            $service = new EnumeratorService();
            $service->aktivasi($enumerator, $request->file('surat_pernyataan'), $request->catatan);

            return Redirect::back()
                ->with('success', "Enumerator {$enumerator->nama_lengkap} berhasil diaktifkan kembali.");
        } catch (\InvalidArgumentException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete the specified resource.
     */
    public function destroy($hashedId): RedirectResponse
    {
        Enumerator::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route($this->routePrefix() . '.enumerators.index')
            ->with('success', 'Enumerator deleted successfully');
    }

    /**
     * Export daftar enumerator ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $target = 20;
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        // ── Hitung rentang tanggal 25 bulan sebelumnya s.d. 25 bulan ini ──
        $periodeAkhir = \Carbon\Carbon::create($tahun, $bulan, 25)->endOfDay();
        $periodeAwal = $periodeAkhir->copy()->subMonth()->addDay();
        // = 26 bulan lalu s.d 25 bulan ini
        // Agar tepat: 25 (bulan-1) 00:00:00  →  25 (bulan) 23:59:59
        $periodeAwal = \Carbon\Carbon::create($tahun, $bulan, 25)
            ->subMonth()                 // mundur 1 bulan
            ->startOfDay();              // 25 Apr 00:00:00
        $periodeAkhir = \Carbon\Carbon::create($tahun, $bulan, 25)
            ->endOfDay();                // 25 Mei 23:59:59

        $query = Enumerator::with('koordinator')
            ->select('id', 'no_registrasi', 'nama_lengkap', 'status', 'created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhereHas('koordinator', fn ($q2) => $q2->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $semuaEnumerator = $query->orderBy('nama_lengkap', 'asc')->get();

        // ── Hitung data per rentang untuk setiap enumerator ──
        $semuaEnumerator->each(function ($enumerator) use ($periodeAwal, $periodeAkhir) {
            $enumerator->total_data_bulan = DataLapangan::where('enumerator_id', $enumerator->id)
                ->whereBetween('created_at', [$periodeAwal, $periodeAkhir])
                ->count();
        });

        // Filter: Aktif ATAU (Tidak Aktif tapi punya data di periode tsb)
        $enumerators = $semuaEnumerator->filter(function ($enumerator) {
            return $enumerator->status === 'Aktif'
                || $enumerator->total_data_bulan > 0;
        })->values();

        // ── Label periode ──
        $labelAwal = $periodeAwal->locale('id')->isoFormat('D MMMM YYYY');
        $labelAkhir = $periodeAkhir->locale('id')->isoFormat('D MMMM YYYY');
        $periodeLabel = "{$labelAwal} – {$labelAkhir}";

        $exportedAt = now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm');

        // Bulan deadline = bulan yang dipilih (tanggal 25-nya)
        $deadlineLabel = $periodeAkhir->locale('id')->isoFormat('D MMMM YYYY');
        // Nama bulan akhir untuk keterangan tabel (mis. "Mei")
        $namaBulanAkhir = $periodeAkhir->locale('id')->isoFormat('MMMM');

        $pdf = Pdf::loadView(
            'superadmin.enumerator.partials.export-pdf',
            compact(
                'enumerators',
                'exportedAt',
                'target',
                'periodeLabel',
                'deadlineLabel',
                'namaBulanAkhir',
                'bulan',
                'tahun',
                'periodeAwal',
                'periodeAkhir'
            )
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'enable_css_float' => true,
            ]);

        $filename = 'laporan-enumerator-'
            .$tahun
            .str_pad($bulan, 2, '0', STR_PAD_LEFT)
            .'-'.now()->format('His').'.pdf';

        return $pdf->download($filename);
    }
}
