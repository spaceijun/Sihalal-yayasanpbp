<?php

namespace App\Services\Koordinator;

use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;

class DashboardService
{
    private const TARGET_BULANAN = 20;

    public function getStats(Koordinator $koordinator): array
    {
        $totalEnumerator = Enumerator::where('koordinator_id', $koordinator->id)->count();
        $enumeratorAktif = Enumerator::where('koordinator_id', $koordinator->id)->where('status', 'Aktif')->count();

        $enumeratorIds = Enumerator::where('koordinator_id', $koordinator->id)->pluck('id');
        $dataBulanIni = DataLapangan::whereIn('enumerator_id', $enumeratorIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $dataTerbitSH = DataLapangan::whereIn('enumerator_id', $enumeratorIds)
            ->where('status', 'TERBIT SH')
            ->count();

        return compact('totalEnumerator', 'enumeratorAktif', 'dataBulanIni', 'dataTerbitSH');
    }

    public function getEnumeratorKpi(Koordinator $koordinator)
    {
        return Enumerator::where('koordinator_id', $koordinator->id)
            ->withCount([
                'dataLapangans as data_bulan_ini' => fn ($q) => $q
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
                'dataLapangans as terbit_sh_bulan_ini' => fn ($q) => $q
                    ->where('status', 'TERBIT SH')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
            ])
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function ($enumerator) {
                $enumerator->target_bulanan = self::TARGET_BULANAN;
                $enumerator->progress_percent = self::TARGET_BULANAN > 0
                    ? min(100, (int) round(($enumerator->data_bulan_ini / self::TARGET_BULANAN) * 100))
                    : 0;

                return $enumerator;
            });
    }
}
