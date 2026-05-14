<?php

namespace App\Services;

use App\Models\DataEntry;
use App\Models\DataEntryPenagihan;
use App\Models\DataEntryPenagihanDetail;
use App\Models\DataEntryProgress;
use Illuminate\Support\Facades\DB;

class DataEntryPenagihanService
{
    const DATA_PER_PAKET = 15;

    private static function tarifOssPerPaket(): int
    {
        return (int) config('services.oss.tarif_per_paket_oss');
    }

    private static function tarifSiHalalPerPaket(): int
    {
        return (int) config('services.sihalal.tarif_per_paket_sihalal');
    }

    private function getTarifPerPaket(DataEntry $dataEntry): int
    {
        return $dataEntry->entry_type === 'SIHALAL'
            ? self::tarifSiHalalPerPaket()
            : self::tarifOssPerPaket();
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
        return DB::transaction(function () use ($dataEntry) {
            // Jika masih ada PENDING, tahan dulu.
            $adaPending = DataEntryProgress::where('data_entry_id', $dataEntry->id)
                ->where('action', 'created')
                ->where('status', 'PENDING')
                ->exists();

            if ($adaPending) {
                return null;
            }

            // Ambil progress DITERIMA yang belum masuk tagihan aktif,
            // termasuk yang berasal dari penagihan Ditolak.
            $progressDiterima = DataEntryProgress::where('data_entry_id', $dataEntry->id)
                ->where('action', 'created')
                ->where('status', 'DITERIMA')
                ->where(function ($query) {
                    $query->whereDoesntHave('penagihanDetails')
                        ->orWhereHas('penagihanDetails', function ($q) {
                            $q->whereHas('penagihan', function ($q2) {
                                $q2->where('status', 'Ditolak');
                            });
                        });
                })
                ->lockForUpdate() // Cegah race condition
                ->get();

            $totalDiterima = $progressDiterima->count();

            if ($totalDiterima < self::DATA_PER_PAKET) {
                return null;
            }

            $tarifPerPaket = $this->getTarifPerPaket($dataEntry);
            $jumlahPaket   = (int) floor($totalDiterima / self::DATA_PER_PAKET);
            $jumlahData    = $jumlahPaket * self::DATA_PER_PAKET;
            $nominal       = $jumlahPaket * $tarifPerPaket;

            $progressUntukTagihan = $progressDiterima->take($jumlahData);

            $penagihan = DataEntryPenagihan::create([
                'user_id'         => $dataEntry->user_id,
                'data_entry_id'   => $dataEntry->id,
                'jumlah_data'     => $jumlahData,
                'jumlah_paket'    => $jumlahPaket,
                'nominal'         => $nominal,
                'status'          => 'Menunggu',
                'tanggal_tagihan' => now(),
            ]);

            // Gunakan insert massal — lebih efisien dari looping create()
            $details = $progressUntukTagihan->map(fn($progress) => [
                'penagihan_id'           => $penagihan->id,
                'data_entry_progress_id' => $progress->id,
                'created_at'             => now(),
                'updated_at'             => now(),
            ])->toArray();

            DataEntryPenagihanDetail::insert($details);

            return $penagihan;
        });
    }
}
