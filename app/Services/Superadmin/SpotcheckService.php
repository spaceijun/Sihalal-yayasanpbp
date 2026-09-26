<?php

namespace App\Services\Superadmin;

use App\Models\Spotcheck;
use Illuminate\Support\Facades\DB;

class SpotcheckService
{
    public function store(array $data): Spotcheck
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['foto_pu']) && $data['foto_pu'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['foto_pu'];
                $fileName = time().'_spotcheck_'.$file->getClientOriginalName();
                $file->storeAs('spotcheck', $fileName, 'public');
                $data['foto_pu'] = 'spotcheck/'.$fileName;
            }

            return Spotcheck::create($data);
        });
    }

    public function update(Spotcheck $spotcheck, array $data): Spotcheck
    {
        return DB::transaction(function () use ($spotcheck, $data) {
            $spotcheck->update($data);

            return $spotcheck->fresh();
        });
    }

    public function delete(Spotcheck $spotcheck): void
    {
        DB::transaction(function () use ($spotcheck) {
            $spotcheck->delete();
        });
    }
}
