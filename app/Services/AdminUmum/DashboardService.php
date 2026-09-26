<?php

namespace App\Services\AdminUmum;

use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Total data koordinator (Human Resources).
     */
    public function getTotalDataKoordinator(): int
    {
        return Koordinator::count();
    }

    /**
     * Total data enumerator (pendamping).
     */
    public function getTotalDataEnumerator(): int
    {
        return Enumerator::count();
    }

    /**
     * Total seluruh data lapangan yang masuk.
     */
    public function getTotalDataLapangan(): int
    {
        return DataLapangan::count();
    }

    /**
     * Hitung jumlah data lapangan per status (Pending, Progress OSS, Progress Sihalal,
     * Terbit SH, Revisi, Terverifikasi).
     */
    public function getStatsByStatus(): array
    {
        return [
            'totalDataPending' => DataLapangan::where('status', 'PENDING')->count(),
            'totalDataProgressOSS' => DataLapangan::where('status', 'PROGRESS OSS')->count(),
            'totalDataProgressSihalal' => DataLapangan::where('status', 'PROGRESS SIHALAL')->count(),
            'totalDataTerbitSH' => DataLapangan::where('status', 'TERBIT SH')->count(),
            'totalDataRevisi' => DataLapangan::where('status', 'REVISI')->count(),
            'totalDataTerverifikasi' => DataLapangan::where('status', 'TERVERIFIKASI')->count(),
        ];
    }

    /**
     * Hitung statistik status pembayaran data lapangan.
     */
    public function getPaymentStats(): array
    {
        return [
            'totalPembayaranPending' => DataLapangan::where('status', 'TERBIT SH')->where('status_pembayaran', 'PENDING')->count(),
            'totalPembayaranPengajuan' => DataLapangan::where('status_pembayaran', 'PENGAJUAN')->count(),
            'totalDibayar' => DataLapangan::where('status_pembayaran', 'DIBAYAR')->count(),
        ];
    }

    /**
     * 20 data lapangan terbaru yang masuk hari ini.
     */
    public function getLatestDataToday(): Collection
    {
        return DataLapangan::with('enumerator')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
    }

    /**
     * 20 data lapangan berstatus Terbit SH yang paling baru diperbarui.
     */
    public function getLatestDataUpdate(): Collection
    {
        return DataLapangan::with('enumerator')
            ->where('status', 'Terbit SH')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();
    }

    /**
     * Jumlah data masuk per bulan (untuk chart tren data masuk).
     */
    public function getDataMasukPerBulan(): Collection
    {
        return DataLapangan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();
    }

    /**
     * Jumlah data Terbit SH per bulan (untuk chart tren terbit SH).
     */
    public function getDataTerbitSHPerBulan(): Collection
    {
        return DataLapangan::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan, COUNT(*) as total")
            ->where('status', 'Terbit SH')
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->get();
    }

    /**
     * Kumpulkan seluruh data yang dibutuhkan halaman dashboard Admin Umum.
     */
    public function getDashboardData(): array
    {
        return array_merge(
            [
                'totalDataKoordinator' => $this->getTotalDataKoordinator(),
                'totalDataEnumerator' => $this->getTotalDataEnumerator(),
                'totalDataLapangan' => $this->getTotalDataLapangan(),
                'latestDataToday' => $this->getLatestDataToday(),
                'latestDataUpdate' => $this->getLatestDataUpdate(),
                'dataMasukPerBulan' => $this->getDataMasukPerBulan(),
                'dataTerbitSHPerBulan' => $this->getDataTerbitSHPerBulan(),
            ],
            $this->getStatsByStatus(),
            $this->getPaymentStats()
        );
    }
}
