<?php

namespace App\Services\Koordinator;

use App\Models\DataLapangan;
use App\Models\Superadmin\Koordinator;
use Illuminate\Support\Facades\DB;

class KoordinatorDataLapanganService
{
    /**
     * Verifikasi lapangan oleh koordinator. Koordinator TIDAK mengubah data aktual
     * (nama PU, NIK, foto, dll) — hanya menandai status verifikasi + catatan.
     */
    public function verifikasi(DataLapangan $dataLapangan, Koordinator $koordinator, array $data): DataLapangan
    {
        if ($dataLapangan->enumerator->koordinator_id !== $koordinator->id) {
            throw new \Exception('Data lapangan ini bukan di bawah koordinasi Anda.');
        }

        return DB::transaction(function () use ($dataLapangan, $koordinator, $data) {
            $dataLapangan->update([
                'verifikasi_koordinator' => $data['verifikasi_koordinator'],
                'catatan_koordinator' => $data['catatan_koordinator'] ?? null,
                'verified_at_koordinator' => now(),
                'verified_by_koordinator' => $koordinator->id,
            ]);

            return $dataLapangan->fresh();
        });
    }
}
