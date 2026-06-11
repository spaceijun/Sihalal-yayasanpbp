<?php

namespace App\Services\Superadmin;

use App\Models\DataEntryProgress;
use App\Models\Verifikator;
use App\Models\VerifikatorPayment;
use Illuminate\Support\Facades\DB;

class VerifikatorProgressPaymentService
{
    /**
     * Buat pembayaran untuk semua progress DITERIMA yang belum dibayar
     * milik satu verifikator.
     */
    public function bayarVerifikator(
        Verifikator $verifikator,
        string $periodeDari,
        string $periodeSampai
    ): VerifikatorPayment {
        return DB::transaction(function () use ($verifikator, $periodeDari, $periodeSampai) {

            // Ambil semua progress DITERIMA, belum dibayar, milik verifikator ini
            $progresses = DataEntryProgress::where('verifikator_id', $verifikator->id)
                ->where('status', 'DITERIMA')
                ->whereNull('payment_id')
                ->where('action', 'created')
                ->get();

            if ($progresses->isEmpty()) {
                throw new \RuntimeException('Tidak ada data progress yang belum dibayar untuk verifikator ini.');
            }

            $jumlahData = $progresses->count();
            $totalNominal = $jumlahData * $verifikator->rate_per_data;

            // Buat record payment
            $payment = VerifikatorPayment::create([
                'verifikator_id' => $verifikator->id,
                'jumlah_data' => $jumlahData,
                'total_nominal' => $totalNominal,
                'periode_dari' => $periodeDari,
                'periode_sampai' => $periodeSampai,
                'paid_at' => now(),
            ]);

            // Tandai semua progress sudah dibayar
            DataEntryProgress::whereIn('id', $progresses->pluck('id'))
                ->update(['payment_id' => $payment->id]);

            return $payment;
        });
    }

    /**
     * Ringkasan tagihan per verifikator (belum dibayar).
     */
    public function getRingkasanTagihan(): \Illuminate\Support\Collection
    {
        return Verifikator::withCount([
            // progress DITERIMA & belum dibayar
            'dataLapangans as jumlah_belum_dibayar' => function ($q) {
                // fallback ke dataLapangans jika relasi progress belum ada
            },
        ])
            ->get()
            ->map(function ($v) {
                $jumlah = DataEntryProgress::where('verifikator_id', $v->id)
                    ->where('status', 'DITERIMA')
                    ->whereNull('payment_id')
                    ->where('action', 'created')
                    ->count();

                return [
                    'verifikator' => $v,
                    'jumlah_data' => $jumlah,
                    'total_nominal' => $jumlah * $v->rate_per_data,
                ];
            })
            ->filter(fn ($item) => $item['jumlah_data'] > 0);
    }
}
