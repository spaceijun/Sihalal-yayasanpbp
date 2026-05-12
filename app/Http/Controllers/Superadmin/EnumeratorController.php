<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Enumerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EnumeratorRequest;
use App\Models\DataBank;
use App\Models\DataLapangan;
use App\Models\Superadmin\Koordinator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnumeratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $enumerators = Enumerator::with('koordinator', 'bank')->paginate();

        return view('superadmin.enumerator.index', compact('enumerators'))
            ->with('i', ($request->input('page', 1) - 1) * $enumerators->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $enumerator = new Enumerator();
        $koordinators = Koordinator::all();

        return view('superadmin.enumerator.create', compact('enumerator', 'koordinators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnumeratorRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('foto_diri')) {
            $image     = $request->file('foto_diri');
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            $image->storeAs('foto-diri', $imageName, 'public');
            $validatedData['foto_diri'] = 'foto-diri/' . $imageName;
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

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load(['koordinator', 'bank']);

        return view('superadmin.enumerator.show', compact('enumerator'));
    }

    /**
     * Tampilkan galeri foto per enumerator (foto_pendamping & foto_produk)
     */
    public function gallery($hashedId): View
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load(['koordinator', 'dataLapangans']);

        return view('superadmin.enumerator.gallery', compact('enumerator'));
    }

    /**
     * Download foto dari data lapangan milik enumerator
     */
    public function downloadFoto($hashedId, $type): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('dataLapangans');

        $allowed = ['foto_pendamping', 'foto_produk'];

        if (!in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        $data = $enumerator->dataLapangans
            ->whereNotNull($type)
            ->first();

        if (!$data || !$data->$type) {
            abort(404, 'Foto tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $data->$type);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = $type . '_' . $enumerator->no_registrasi . '_' . $data->id . '.' . $extension;

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

        if (!in_array($type, $allowed)) {
            abort(403, 'Tipe foto tidak diizinkan.');
        }

        $data = $enumerator->dataLapangans()->findOrFail($dataId);

        if (!$data->$type) {
            abort(404, 'Foto tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $data->$type);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = $type . '_' . $enumerator->no_registrasi . '_' . $data->id . '.' . $extension;

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename);
    }

    /**
     * Generate and display Surat Tugas
     */
    public function suratTugas($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);
        $enumerator->load('koordinator');

        return view('superadmin.enumerator.partials.surat', compact('enumerator'));
    }

    /**
     * Generate ID Card as HTML
     */
    public function idCard($hashedId)
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        return view('superadmin.enumerator.partials.idcard', compact('enumerator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($hashedId): View
    {
        $enumerator   = Enumerator::findByHashedIdOrFail($hashedId);
        $koordinators = Koordinator::all();
        $banks        = DataBank::orderBy('name')->get();
        return view('superadmin.enumerator.edit', compact('enumerator', 'koordinators', 'banks'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(EnumeratorRequest $request, Enumerator $enumerator): RedirectResponse
    {
        $enumerator->update($request->validated());

        return Redirect::route('superadmin.enumerators.index')
            ->with('success', 'Enumerator updated successfully');
    }

    /**
     * Aktifkan kembali enumerator yang berstatus Tidak Aktif.
     */
    public function aktivasi($hashedId): RedirectResponse
    {
        $enumerator = Enumerator::findByHashedIdOrFail($hashedId);

        if ($enumerator->status === 'Tidak Aktif') {
            $enumerator->update(['status' => 'Aktif']);

            return Redirect::back()
                ->with('success', "Enumerator {$enumerator->nama_lengkap} berhasil diaktifkan kembali.");
        }

        return Redirect::back()
            ->with('error', 'Enumerator sudah berstatus Aktif.');
    }

    /**
     * Delete the specified resource.
     */
    public function destroy($hashedId): RedirectResponse
    {
        Enumerator::findByHashedIdOrFail($hashedId)->delete();

        return Redirect::route('superadmin.enumerators.index')
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
        $periodeAkhir   = \Carbon\Carbon::create($tahun, $bulan, 25)->endOfDay();
        $periodeAwal    = $periodeAkhir->copy()->subMonth()->addDay();
        // = 26 bulan lalu s.d 25 bulan ini
        // Agar tepat: 25 (bulan-1) 00:00:00  →  25 (bulan) 23:59:59
        $periodeAwal    = \Carbon\Carbon::create($tahun, $bulan, 25)
            ->subMonth()                 // mundur 1 bulan
            ->startOfDay();              // 25 Apr 00:00:00
        $periodeAkhir   = \Carbon\Carbon::create($tahun, $bulan, 25)
            ->endOfDay();                // 25 Mei 23:59:59

        $query = Enumerator::with('koordinator')
            ->select('id', 'no_registrasi', 'nama_lengkap', 'status', 'created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhereHas('koordinator', fn($q2) => $q2->where('nama_lengkap', 'like', "%{$search}%"));
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
        $labelAwal    = $periodeAwal->locale('id')->isoFormat('D MMMM YYYY');
        $labelAkhir   = $periodeAkhir->locale('id')->isoFormat('D MMMM YYYY');
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
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'dpi'                  => 96,
                'enable_css_float'     => true,
            ]);

        $filename = 'laporan-enumerator-'
            . $tahun
            . str_pad($bulan, 2, '0', STR_PAD_LEFT)
            . '-' . now()->format('His') . '.pdf';

        return $pdf->download($filename);
    }
}
