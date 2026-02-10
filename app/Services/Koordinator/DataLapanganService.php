<?php

namespace App\Services\Koordinator;

use App\Models\DataLapangan;

class DataLapanganService
{
    public function getFilteredData($filters, $perPage)
    {
        $query = DataLapangan::query();

        // Filter berdasarkan koordinator (melalui enumerator)
        if (!empty($filters['koordinator_id'])) {
            $query->whereHas('enumerator', function ($q) use ($filters) {
                $q->where('koordinator_id', $filters['koordinator_id']);
            });
        }

        if (!empty($filters['nama_pu'])) {
            $query->where('nama_pu', 'like', '%' . $filters['nama_pu'] . '%');
        }

        if (!empty($filters['enumerator_id'])) {
            $query->where('enumerator_id', $filters['enumerator_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }
}
