<?php

namespace App\Services;

use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryPenagihanDetail;
use App\Models\DataEntryProgress;

class DataEntryPenagihanService
{
    const TARIF_PER_PAKET = 100000;
    const DATA_PER_PAKET  = 15;

    /**
     * Cek apakah data entry sudah memenuhi syarat penagihan baru
     * dipanggil setiap kali ada upload file berhasil
     */
    public function cekDanBuatTagihan(DataEntry $dataEntry): ?DataEntryPenagihan
    {
        // Ambil progress yang belum masuk tagihan manapun
        $progressBelumDitagih = DataEntryProgress::where('data_entry_id', $dataEntry->id)
            ->where('action', 'created')
            ->whereDoesntHave('penagihanDetails') // sesuaikan nama relasi
            ->get();

        $totalBelumDitagih = $progressBelumDitagih->count();

        // Belum mencapai 15 data, tidak perlu buat tagihan
        if ($totalBelumDitagih < self::DATA_PER_PAKET) {
            return null;
        }

        // Hitung berapa paket yang bisa ditagihkan
        $jumlahPaket = (int) floor($totalBelumDitagih / self::DATA_PER_PAKET);
        $jumlahData  = $jumlahPaket * self::DATA_PER_PAKET;
        $nominal     = $jumlahPaket * self::TARIF_PER_PAKET;

        // Ambil hanya data yang akan dimasukkan tagihan
        $progressUntukTagihan = $progressBelumDitagih->take($jumlahData);

        // Buat tagihan
        $penagihan = DataEntryPenagihan::create([
            'user_id'          => $dataEntry->user_id,
            'data_entry_id'    => $dataEntry->id,
            'jumlah_data'      => $jumlahData,
            'jumlah_paket'     => $jumlahPaket,
            'nominal'          => $nominal,
            'status'           => 'Menunggu',
            'tanggal_tagihan'  => now(),
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
