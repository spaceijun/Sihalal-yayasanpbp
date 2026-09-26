<?php

namespace App\Services\Superadmin;

use App\Models\ResepMakanan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ResepMakananService
{
    public function store(array $data, ?UploadedFile $foto = null): ResepMakanan
    {
        return DB::transaction(function () use ($data, $foto) {
            if ($foto) {
                $fotoName = time().'_'.$foto->getClientOriginalName();
                $foto->storeAs('resep-makanan', $fotoName, 'public');
                $data['foto'] = 'resep-makanan/'.$fotoName;
            }

            return ResepMakanan::create($data);
        });
    }

    public function update(ResepMakanan $resepMakanan, array $data, ?UploadedFile $foto = null): ResepMakanan
    {
        return DB::transaction(function () use ($resepMakanan, $data, $foto) {
            if ($foto) {
                $fotoName = time().'_'.$foto->getClientOriginalName();
                $foto->storeAs('resep-makanan', $fotoName, 'public');
                $data['foto'] = 'resep-makanan/'.$fotoName;
            }

            $resepMakanan->update($data);

            return $resepMakanan->fresh();
        });
    }

    public function delete(ResepMakanan $resepMakanan): void
    {
        DB::transaction(function () use ($resepMakanan) {
            $resepMakanan->delete();
        });
    }
}
