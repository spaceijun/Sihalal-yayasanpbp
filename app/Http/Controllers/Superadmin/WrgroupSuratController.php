<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WrgroupSuratRequest;
use App\Models\Superadmin\WrgroupSurat;
use App\Services\Integrasi\Wrgroup\WrgroupSuratService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Buat & arsip surat resmi lewat modul Pembuat Surat WRGROUP — lihat .agent/workflows/wrgroup-integrasi.md.
 */
class WrgroupSuratController extends Controller
{
    public function __construct(protected WrgroupSuratService $service) {}

    public function index(): View
    {
        return view('superadmin.wrgroup-surat.index', [
            'daftar' => WrgroupSurat::with('pengaju')->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $jenis = $this->service->jenisTersedia();

        return view('superadmin.wrgroup-surat.create', [
            'jenisList' => $jenis['data'],
            'errorJenis' => $jenis['error'],
        ]);
    }

    public function store(WrgroupSuratRequest $request): RedirectResponse
    {
        $row = $this->service->ajukan($request->validated(), $request->user());

        if (! $row->berhasil_kirim) {
            return back()->withInput()->with('error', 'Gagal mengajukan surat ke WRGROUP: '.($row->response_message ?: 'tidak ada respons.'));
        }

        return redirect()->route('superadmin.wrgroup-surat.show', $row->hashed_id)
            ->with('success', 'Surat berhasil diajukan ke WRGROUP, menunggu pemeriksaan.');
    }

    public function show(WrgroupSurat $surat): View
    {
        return view('superadmin.wrgroup-surat.show', ['surat' => $surat]);
    }

    public function ulangi(WrgroupSurat $surat): RedirectResponse
    {
        $row = $this->service->ulangi($surat);

        return back()->with($row->berhasil_kirim ? 'success' : 'error', $row->berhasil_kirim
            ? 'Berhasil diajukan ulang ke WRGROUP.'
            : 'Gagal diajukan ulang: '.($row->response_message ?: 'tidak ada respons.'));
    }

    public function pdf(WrgroupSurat $surat): Response
    {
        abort_unless($surat->pdf_path && Storage::disk('public')->exists($surat->pdf_path), 404, 'PDF belum tersedia — surat belum terbit atau belum tersinkron.');

        return response(Storage::disk('public')->get($surat->pdf_path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($surat->pdf_path).'"',
        ]);
    }
}
