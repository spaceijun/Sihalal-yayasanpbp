<?php

namespace App\Services\Superadmin;

use App\Models\Pengumuman;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengumumanService
{
    /**
     * Generate nomor pengumuman otomatis (format: YPBP-KH/{bulan}/{tahun}/{urut}).
     */
    public function generateNomor(): string
    {
        $bulan = now()->format('m');
        $tahun = now()->format('Y');
        $prefix = "YPBP-KH/{$bulan}/{$tahun}/";

        // Selalu ambil nomor urut dari total seluruh data
        $count = Pengumuman::count();
        $nextNumber = $count + 1;

        // Cek jika nomor sudah dipakai (hindari duplikat)
        $nomor = $prefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        while (Pengumuman::where('nomor', $nomor)->exists()) {
            $nextNumber++;
            $nomor = $prefix.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $nomor;
    }

    public function store(array $data, ?UploadedFile $foto = null): Pengumuman
    {
        return DB::transaction(function () use ($data, $foto) {
            $data['nomor'] = $this->generateNomor();

            if ($foto) {
                $fotoName = time().'_'.$foto->getClientOriginalName();
                $foto->storeAs('pengumuman', $fotoName, 'public');
                $data['foto'] = 'pengumuman/'.$fotoName;
            }

            return Pengumuman::create($data);
        });
    }

    public function update(Pengumuman $pengumuman, array $data, ?UploadedFile $foto = null): Pengumuman
    {
        return DB::transaction(function () use ($pengumuman, $data, $foto) {
            if ($foto) {
                // Hapus foto lama jika ada
                if ($pengumuman->foto) {
                    Storage::disk('public')->delete($pengumuman->foto);
                }

                $fotoName = time().'_'.$foto->getClientOriginalName();
                $foto->storeAs('pengumuman', $fotoName, 'public');
                $data['foto'] = 'pengumuman/'.$fotoName;
            } else {
                // Pertahankan foto lama jika tidak ada upload baru
                unset($data['foto']);
            }

            $pengumuman->update($data);

            return $pengumuman->fresh();
        });
    }

    public function delete(Pengumuman $pengumuman): void
    {
        DB::transaction(function () use ($pengumuman) {
            $pengumuman->delete();
        });
    }
}
