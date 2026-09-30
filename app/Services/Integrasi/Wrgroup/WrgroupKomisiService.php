<?php

namespace App\Services\Integrasi\Wrgroup;

use App\Models\Cashflow;
use App\Models\Superadmin\WrgroupKomisiPembayaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

    /**
     * Ambil status verifikasi setoran dari WRGROUP (GET /komisi/periode, dicocokkan lewat event_id)
     * dan bukukan setoran yang TERVERIFIKASI sebagai Pengeluaran di Arus Kas — sekali per setoran,
     * tanggal = tanggal_transaksi_bank (sama dengan pendapatan yang dibukukan WRGROUP). Setoran ditolak
     * tidak dibukukan. Dipanggil terjadwal (wrgroup:komisi-sync).
     *
     * @return array{ok: bool, diperbarui: int, dibukukan: int, error: ?string}
     */
    public function sinkronVerifikasi(): array
    {
        $belumFinal = WrgroupKomisiPembayaran::where('berhasil', true)
            ->where(fn ($q) => $q->whereNull('status_verifikasi')->orWhereNotIn('status_verifikasi', WrgroupKomisiPembayaran::STATUS_FINAL))
            ->get();

        // Setoran terverifikasi yang belum sempat dibukukan (mis. proses sebelumnya terhenti) ikut diproses.
        $belumDibukukan = WrgroupKomisiPembayaran::where('status_verifikasi', 'terverifikasi')->whereNull('dibukukan_at')->get();

        if ($belumFinal->isEmpty() && $belumDibukukan->isEmpty()) {
            return ['ok' => true, 'diperbarui' => 0, 'dibukukan' => 0, 'error' => null];
        }

        $diperbarui = 0;
        $dibukukan = 0;

        if ($belumFinal->isNotEmpty()) {
            $daftar = $this->daftarPeriode();
            if (! $daftar['ok']) {
                return ['ok' => false, 'diperbarui' => 0, 'dibukukan' => 0, 'error' => $daftar['error']];
            }

            $remote = collect($daftar['data'])
                ->flatMap(fn (array $periode) => $periode['pembayaran'] ?? [])
                ->filter(fn (array $bayar) => filled($bayar['event_id'] ?? null))
                ->keyBy('event_id');

            foreach ($belumFinal as $row) {
                $bayar = $remote->get($row->event_id);
                if (! $bayar || ($bayar['status'] ?? null) === $row->status_verifikasi) {
                    continue;
                }

                $row->update([
                    'status_verifikasi' => $bayar['status'],
                    'diverifikasi_at' => $bayar['diverifikasi_at'] ?? null,
                    'catatan_verifikasi' => $bayar['catatan'] ?? null,
                ]);
                $diperbarui++;

                if ($row->status_verifikasi === 'terverifikasi') {
                    $belumDibukukan->push($row);
                }
            }
        }

        foreach ($belumDibukukan->unique('id') as $row) {
            if ($this->bukukan($row)) {
                $dibukukan++;
            }
        }

        return ['ok' => true, 'diperbarui' => $diperbarui, 'dibukukan' => $dibukukan, 'error' => null];
    }

    /**
     * Idempoten: baris dikunci & dibaca ulang di dalam transaksi, dan dibukukan_at yang sudah terisi
     * tidak pernah dibukukan ulang — walau entri Arus Kasnya kemudian dihapus manual oleh staf.
     */
    protected function bukukan(WrgroupKomisiPembayaran $row): bool
    {
        return DB::transaction(function () use ($row) {
            $terkunci = WrgroupKomisiPembayaran::whereKey($row->id)->lockForUpdate()->first();
            if (! $terkunci || $terkunci->dibukukan_at !== null || $terkunci->status_verifikasi !== 'terverifikasi') {
                return false;
            }

            $cashflow = Cashflow::create([
                'tipe' => 'Pengeluaran',
                'sumber' => Cashflow::SUMBER_KOMISI_WRGROUP,
                'jumlah' => $terkunci->jumlah,
                'tanggal' => $terkunci->tanggal_transaksi_bank->toDateString(),
                'keterangan' => "Setoran komisi WRGROUP periode {$terkunci->periode}"
                    .($terkunci->referensi_bank ? " (ref. {$terkunci->referensi_bank})" : '')
                    .' — terverifikasi WRGROUP',
            ]);

            $terkunci->update(['cashflow_id' => $cashflow->id, 'dibukukan_at' => now()]);

            return true;
        });
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
