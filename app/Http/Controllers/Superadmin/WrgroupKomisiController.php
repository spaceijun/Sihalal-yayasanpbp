<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WrgroupKomisiPembayaranRequest;
use App\Models\Superadmin\WrgroupKomisiPembayaran;
use App\Services\Integrasi\Wrgroup\WrgroupKomisiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WrgroupKomisiController extends Controller
{
    public function __construct(protected WrgroupKomisiService $service) {}

    public function index(): View
    {
        $daftar = $this->service->daftarPeriode();

        return view('superadmin.wrgroup.komisi', [
            'periodes' => $daftar['data'],
            'errorPeriode' => $daftar['error'],
            'riwayat' => WrgroupKomisiPembayaran::with('pengirim')->latest()->get(),
        ]);
    }

    public function ajukan(WrgroupKomisiPembayaranRequest $request, string $referensi): RedirectResponse
    {
        $row = $this->service->ajukanPembayaran(
            $referensi,
            $request->validated('periode'),
            $request->validated(),
            $request->file('bukti_setoran'),
            $request->user(),
        );

        if (! $row->berhasil) {
            return back()->with('error', 'Gagal mengirim ke WRGROUP: '.($row->response_message ?: 'tidak ada respons.').' Gunakan tombol "Coba Lagi" di riwayat.');
        }

        return back()->with('success', 'Bukti setoran berhasil dikirim ke WRGROUP, menunggu verifikasi.');
    }

    public function ulangi(WrgroupKomisiPembayaran $pembayaran): RedirectResponse
    {
        $row = $this->service->ulangi($pembayaran);

        return back()->with($row->berhasil ? 'success' : 'error', $row->berhasil
            ? 'Berhasil dikirim ulang ke WRGROUP.'
            : 'Gagal dikirim ulang: '.($row->response_message ?: 'tidak ada respons.'));
    }
}
