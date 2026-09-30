<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Models\Superadmin\WrgroupSurat;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Sisi pilar dari modul Pembuat Surat WRGROUP: Kawulo Halal memilih Jenis Surat yang dibuka WRGROUP,
 * mengisi formulirnya, dan mengajukan lewat API (cakupan kredensial "surat"). Template, kop dua-logo,
 * penomoran, dan PDF tetap 100% dirender & dikuasai WRGROUP — service ini hanya jembatan API + arsip
 * lokal (lihat .agent/workflows/wrgroup-integrasi.md). Sinkron seperti WrgroupKomisiService, bukan
 * lewat wrgroup_outbox: pengajuan surat bukan event finansial frekuensi tinggi.
 */
class WrgroupSuratService
{
    public function __construct(protected WrgroupClient $client) {}

    /**
     * @return array{ok: bool, data: array<int, array<string, mixed>>, error: ?string}
     */
    public function jenisTersedia(): array
    {
        $hasil = $this->client->get('/surat/jenis');

        if ($hasil['status'] !== 200) {
            return ['ok' => false, 'data' => [], 'error' => $hasil['error'] ?? ($hasil['body']['message'] ?? 'Gagal mengambil daftar jenis surat dari WRGROUP.')];
        }

        return ['ok' => true, 'data' => $hasil['body']['data'] ?? [], 'error' => null];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function ajukan(array $data, User $pengaju): WrgroupSurat
    {
        $row = WrgroupSurat::create([
            'surat_jenis_kode' => $data['surat_jenis_kode'],
            'jenis_nama' => $data['jenis_nama'] ?? $data['surat_jenis_kode'],
            'perihal' => $data['perihal'],
            'data_variabel' => $data['data_variabel'] ?? [],
            'diajukan_oleh' => $pengaju->id,
        ]);

        $hasil = $this->client->post('/surat', [
            'surat_jenis_kode' => $data['surat_jenis_kode'],
            'perihal' => $data['perihal'],
            'pengaju_nama' => $pengaju->name,
            'pengaju_kontak' => $pengaju->email,
            'data_variabel' => $data['data_variabel'] ?? [],
        ]);

        $berhasil = $hasil['status'] === 200;

        $row->update([
            'wrgroup_hashed_id' => $berhasil ? ($hasil['body']['reference'] ?? null) : null,
            'status' => $berhasil ? ($hasil['body']['status_surat'] ?? 'diajukan') : null,
            'berhasil_kirim' => $berhasil,
            'http_status' => $hasil['status'] ?: null,
            'response_message' => $hasil['error'] ?? ($hasil['body']['message'] ?? null),
        ]);

        return $row->fresh();
    }

    /**
     * Kirim ulang surat yang gagal terkirim sebelumnya (data sama, baris sama — bukan pengajuan baru).
     */
    public function ulangi(WrgroupSurat $row): WrgroupSurat
    {
        $hasil = $this->client->post('/surat', [
            'surat_jenis_kode' => $row->surat_jenis_kode,
            'perihal' => $row->perihal,
            'pengaju_nama' => $row->pengaju->name,
            'pengaju_kontak' => $row->pengaju->email,
            'data_variabel' => $row->data_variabel ?? [],
        ]);

        $berhasil = $hasil['status'] === 200;

        $row->update([
            'wrgroup_hashed_id' => $berhasil ? ($hasil['body']['reference'] ?? null) : null,
            'status' => $berhasil ? ($hasil['body']['status_surat'] ?? 'diajukan') : null,
            'berhasil_kirim' => $berhasil,
            'http_status' => $hasil['status'] ?: null,
            'response_message' => $hasil['error'] ?? ($hasil['body']['message'] ?? null),
        ]);

        return $row->fresh();
    }

    /**
     * Ambil status terbaru dari WRGROUP utk semua surat yang berhasil terkirim dan belum berstatus
     * final (terbit/dibatalkan), lalu unduh PDF sekali saat pertama kali terbit. Dipanggil terjadwal
     * (wrgroup:surat-sync) — no-op murni bila WRGROUP_ENABLED=false (dicek pemanggil/command).
     */
    public function sinkronStatus(): int
    {
        $baris = WrgroupSurat::where('berhasil_kirim', true)
            ->whereNotNull('wrgroup_hashed_id')
            ->whereNotIn('status', ['terbit', 'dibatalkan'])
            ->get();

        $diperbarui = 0;

        foreach ($baris as $row) {
            $hasil = $this->client->get('/surat/'.$row->wrgroup_hashed_id);
            if ($hasil['status'] !== 200) {
                continue;
            }

            $data = $hasil['body']['data'] ?? [];
            $row->update([
                'status' => $data['status'] ?? $row->status,
                'nomor' => $data['nomor'] ?? $row->nomor,
                'diterbitkan_at' => $data['diterbitkan_at'] ?? $row->diterbitkan_at,
                'catatan_terakhir' => $data['catatan_terakhir'] ?? $row->catatan_terakhir,
            ]);
            $diperbarui++;

            if (($data['pdf_tersedia'] ?? false) && ! $row->pdf_path) {
                $this->unduhPdf($row);
            }
        }

        return $diperbarui;
    }

    protected function unduhPdf(WrgroupSurat $row): void
    {
        $hasil = $this->client->getBinary('/surat/'.$row->wrgroup_hashed_id.'/pdf');
        if ($hasil['status'] !== 200 || $hasil['body'] === null) {
            return;
        }

        $path = 'wrgroup/surat-pdf/'.$row->wrgroup_hashed_id.'.pdf';
        Storage::disk('public')->put($path, $hasil['body']);
        $row->update(['pdf_path' => $path]);
    }
}
