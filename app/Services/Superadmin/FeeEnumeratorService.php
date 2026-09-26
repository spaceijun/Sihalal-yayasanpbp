<?php

namespace App\Services\Superadmin;

use App\Models\Superadmin\FeeEnumerator;
use Illuminate\Support\Facades\DB;

class FeeEnumeratorService
{
    public function store(array $data): FeeEnumerator
    {
        return DB::transaction(function () use ($data) {
            $data['is_aktif'] = $data['is_aktif'] ?? true;

            if ($data['is_aktif']) {
                $this->deactivateConflicting($data['skala'], $data['provinsi'] ?? null, $data['tipe_fee']);
            }

            return FeeEnumerator::create($data);
        });
    }

    public function update(FeeEnumerator $fee, array $data): FeeEnumerator
    {
        return DB::transaction(function () use ($fee, $data) {
            $data['is_aktif'] = $data['is_aktif'] ?? false;

            if ($data['is_aktif']) {
                $this->deactivateConflicting($data['skala'], $data['provinsi'] ?? null, $data['tipe_fee'], excludeId: $fee->id);
            }

            $fee->update($data);

            return $fee->fresh();
        });
    }

    public function toggleAktif(FeeEnumerator $fee): FeeEnumerator
    {
        return DB::transaction(function () use ($fee) {
            $newState = ! $fee->is_aktif;

            if ($newState) {
                $this->deactivateConflicting($fee->skala, $fee->provinsi, $fee->tipe_fee, excludeId: $fee->id);
            }

            $fee->update(['is_aktif' => $newState]);

            return $fee->fresh();
        });
    }

    public function delete(FeeEnumerator $fee): void
    {
        DB::transaction(function () use ($fee) {
            if ($fee->skala === 'Global' && $fee->is_aktif) {
                $otherActiveGlobal = FeeEnumerator::aktif()->global()->where('id', '!=', $fee->id)->exists();
                if (! $otherActiveGlobal) {
                    throw new \Exception('Tidak dapat menghapus fee Global aktif satu-satunya. Aktifkan fee Global lain terlebih dahulu, atau nonaktifkan fee ini dulu.');
                }
            }

            $fee->delete();
        });
    }

    /**
     * Resolve the active fee for a koordinator: Provinsi-scoped override takes
     * priority over the Global fallback.
     */
    public function resolveForKoordinator($koordinator): ?FeeEnumerator
    {
        if ($koordinator->provinsi_kerja) {
            $feeProvinsi = FeeEnumerator::aktif()->provinsi($koordinator->provinsi_kerja)->latest()->first();
            if ($feeProvinsi) {
                return $feeProvinsi;
            }
        }

        return FeeEnumerator::aktif()->global()->latest()->first();
    }

    /**
     * Deactivate any other active fee record sharing the same resolution key
     * (skala [+ provinsi] + tipe_fee), so only one record can win resolution.
     */
    private function deactivateConflicting(string $skala, ?string $provinsi, string $tipeFee, ?int $excludeId = null): void
    {
        $query = FeeEnumerator::aktif()->where('skala', $skala)->where('tipe_fee', $tipeFee);

        if ($skala === 'Provinsi') {
            $query->where('provinsi', $provinsi);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_aktif' => false]);
    }
}
