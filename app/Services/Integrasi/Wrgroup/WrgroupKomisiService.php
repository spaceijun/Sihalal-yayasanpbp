<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Models\Superadmin\WrgroupKomisiPembayaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Sisi pilar dari pembayaran komisi: dana dibayarkan langsung dari sistem ini, lalu buktinya
 * dilaporkan ke WRGROUP untuk diverifikasi (WRGROUP tidak pernah mencatat uang bergerak di sini).
 * Sinkron (bukan lewat wrgroup_outbox) — aksi manual staf sekali klik dengan lampiran file, bukan
 * event otomatis frekuensi tinggi (lihat .agent/workflows/wrgroup-integrasi.md).
 */
class WrgroupKomisiService
{
    public function __construct(protected WrgroupClient $client) {}

    /**
     * @return array{ok: bool, data: array<int, array<string, mixed>>, error: ?string}
     */
    public function daftarPeriode(): array
    {
        $hasil = $this->client->get('/komisi/periode');

        if ($hasil['status'] !== 200) {
            return ['ok' => false, 'data' => [], 'error' => $hasil['error'] ?? ($hasil['body']['message'] ?? 'Gagal mengambil daftar periode komisi.')];
        }

        return ['ok' => true, 'data' => $hasil['body']['data'] ?? [], 'error' => null];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function ajukanPembayaran(string $komisiReference, string $periode, array $data, UploadedFile $bukti, User $pengirim): WrgroupKomisiPembayaran
    {
        $path = $bukti->store('wrgroup/komisi-bukti-setoran', 'public');

        $row = WrgroupKomisiPembayaran::create([
            'event_id' => (string) Str::uuid(),
            'komisi_reference' => $komisiReference,
            'periode' => $periode,
            'jumlah' => $data['jumlah'],
            'tanggal_transaksi_bank' => $data['tanggal_transaksi_bank'],
            'referensi_bank' => $data['referensi_bank'] ?? null,
            'catatan' => $data['catatan'] ?? null,
            'bukti_setoran' => $path,
            'dikirim_oleh' => $pengirim->id,
        ]);

        return $this->kirim($row);
    }

    public function ulangi(WrgroupKomisiPembayaran $row): WrgroupKomisiPembayaran
    {
        return $this->kirim($row);
    }

    protected function kirim(WrgroupKomisiPembayaran $row): WrgroupKomisiPembayaran
    {
        $hasil = $this->client->postMultipart(
            "/komisi/periode/{$row->komisi_reference}/pembayaran",
            [
                'event_id' => $row->event_id,
                'jumlah' => (string) $row->jumlah,
                'tanggal_transaksi_bank' => $row->tanggal_transaksi_bank->toDateString(),
                'referensi_bank' => $row->referensi_bank,
                'catatan' => $row->catatan,
            ],
            [[
                'name' => 'bukti_setoran',
                'contents' => fopen(storage_path('app/public/'.$row->bukti_setoran), 'r'),
                'filename' => basename($row->bukti_setoran),
            ]],
        );

        $berhasil = in_array($hasil['status'], [200, 409], true);

        $row->update([
            'berhasil' => $berhasil,
            'http_status' => $hasil['status'] ?: null,
            'response_message' => $hasil['error'] ?? ($hasil['body']['message'] ?? ($hasil['body']['status'] ?? null)),
        ]);

        return $row->fresh();
    }
}
