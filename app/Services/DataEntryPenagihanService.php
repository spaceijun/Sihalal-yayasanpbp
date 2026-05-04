<?php

namespace App\Services;

use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryPenagihanDetail;
use App\Models\DataEntryProgress;

class DataEntryPenagihanService
{
    const DATA_PER_PAKET          = 15;
    const TARIF_OSS_PER_PAKET     = 150000;
    const TARIF_SIHALAL_PER_PAKET = 200000;

    /**
     * Kembalikan tarif per paket berdasarkan entry_type data entry.
     */
    private function getTarifPerPaket(DataEntry $dataEntry): int
    {
        return $dataEntry->entry_type === 'SIHALAL'
            ? self::TARIF_SIHALAL_PER_PAKET
            : self::TARIF_OSS_PER_PAKET;
    }

    /**
     * Dipanggil oleh superadmin ketika meng-approve (DITERIMA) satu progress.
     * Setelah progress di-update ke DITERIMA, cek apakah sudah cukup untuk paket baru.
     *
     * Aturan:
     * - Penagihan HANYA dibuat jika tidak ada data PENDING yang masih menunggu review.
     * - Progress dari penagihan yang Ditolak bisa ditagih ulang.
     */
    public function cekDanBuatTagihan(DataEntry $dataEntry): ?DataEntryPenagihan
    {
        // Cek apakah masih ada data PENDING milik data entry ini.
        // Jika ada, tahan dulu — tunggu semua review selesai agar tidak ada
        // penagihan parsial yang dibuat sebelum data pending ikut diterima/ditolak.
        $adaPending = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->where('status', 'PENDING')
            ->exists();

        if ($adaPending) {
            return null;
        }

        // Ambil progress yang sudah DITERIMA tapi belum masuk tagihan aktif.
        // Progress dari penagihan Ditolak diikutkan kembali agar bisa ditagih ulang.
        $progressDiterima = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->where('status', 'DITERIMA')
            ->where(function ($query) {
                $query->whereDoesntHave('penagihanDetails')
                    ->orWhereHas('penagihanDetails', function ($q) {
                        // Jika detail terkait penagihan yang Ditolak,
                        // progress boleh masuk ke tagihan baru
                        $q->whereHas('penagihan', function ($q2) {
                            $q2->where('status', 'Ditolak');
                        });
                    });
            })
            ->get();

        $totalDiterima = $progressDiterima->count();

        // Belum mencapai 15 data, tidak perlu buat tagihan
        if ($totalDiterima < self::DATA_PER_PAKET) {
            return null;
        }

        // Hitung berapa paket yang bisa ditagihkan
        $tarifPerPaket = $this->getTarifPerPaket($dataEntry);
        $jumlahPaket   = (int) floor($totalDiterima / self::DATA_PER_PAKET);
        $jumlahData    = $jumlahPaket * self::DATA_PER_PAKET;
        $nominal       = $jumlahPaket * $tarifPerPaket;

        // Ambil hanya data yang akan dimasukkan tagihan
        $progressUntukTagihan = $progressDiterima->take($jumlahData);

        // Buat tagihan
        $penagihan = DataEntryPenagihan::create([
            'user_id'         => $dataEntry->user_id,
            'data_entry_id'   => $dataEntry->id,
            'jumlah_data'     => $jumlahData,
            'jumlah_paket'    => $jumlahPaket,
            'nominal'         => $nominal,
            'status'          => 'Menunggu',
            'tanggal_tagihan' => now(),
        ]);

        // Simpan detail tagihan
        foreach ($progressUntukTagihan as $progress) {
            DataEntryPenagihanDetail::create([
                'penagihan_id'           => $penagihan->id,
                'data_entry_progress_id' => $progress->id,
            ]);
        }

        return $penagihan;
    }
}
