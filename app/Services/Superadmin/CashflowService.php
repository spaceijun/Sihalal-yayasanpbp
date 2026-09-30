<?php

namespace App\Services\Superadmin;

use App\Models\Cashflow;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class CashflowService
{
    /**
     * Pendapatan bersih (Pemasukan − Pengeluaran) pada satu rentang tanggal — dasar komisi/CSR yang
     * dilaporkan ke WRGROUP (lihat WrgroupEventService::reportPendapatanBersih()). Tipe "Kas" sengaja
     * dikecualikan: itu penyesuaian posisi kas internal, bukan pemasukan/pengeluaran sungguhan — sama
     * seperti perhitungan netCashflow di halaman Laporan Arus Kas. Setoran komisi WRGROUP juga
     * dikecualikan: komisi dihitung dari pendapatan bersih SEBELUM komisi itu sendiri, jadi komisi
     * yang sudah dibayar tidak boleh memotong dasar komisi periode berikutnya.
     */
    public function netPeriode(CarbonInterface $mulai, CarbonInterface $selesai): float
    {
        $jumlah = Cashflow::whereBetween('tanggal', [$mulai->toDateString(), $selesai->toDateString()])
            ->whereIn('tipe', ['Pemasukan', 'Pengeluaran'])
            ->where(fn ($q) => $q->whereNull('sumber')->orWhere('sumber', '!=', Cashflow::SUMBER_KOMISI_WRGROUP))
            ->selectRaw("SUM(CASE WHEN tipe = 'Pemasukan' THEN jumlah ELSE -jumlah END) AS net")
            ->value('net');

        return (float) ($jumlah ?? 0);
    }

    public function store(array $data): Cashflow
    {
        return DB::transaction(function () use ($data) {
            return Cashflow::create($data);
        });
    }

    public function update(Cashflow $cashflow, array $data): Cashflow
    {
        return DB::transaction(function () use ($cashflow, $data) {
            $cashflow->update($data);

            return $cashflow->fresh();
        });
    }

    public function delete(Cashflow $cashflow): void
    {
        DB::transaction(function () use ($cashflow) {
            $cashflow->delete();
        });
    }
}
