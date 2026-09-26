<?php

namespace App\Services\Superadmin;

use App\Models\Superadmin\Koordinator;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KoordinatorService
{
    public function store(array $data, ?UploadedFile $fotoKtp = null, ?UploadedFile $fotoFormal = null): Koordinator
    {
        return DB::transaction(function () use ($data, $fotoKtp, $fotoFormal) {
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'password' => bcrypt($data['password']),
                'role' => 'koordinator',
            ]);

            $user->assignRole('koordinator');

            $payload = $this->fieldPayload($data);
            $payload['user_id'] = $user->id;

            if ($fotoKtp) {
                $payload['foto_ktp'] = $fotoKtp->store('koordinator/foto-ktp', 'public');
            }

            if ($fotoFormal) {
                $payload['foto_formal'] = $fotoFormal->store('koordinator/foto-formal', 'public');
            }

            return Koordinator::create($payload);
        });
    }

    public function update(Koordinator $koordinator, array $data, ?UploadedFile $fotoKtp = null, ?UploadedFile $fotoFormal = null): Koordinator
    {
        return DB::transaction(function () use ($koordinator, $data, $fotoKtp, $fotoFormal) {
            $payload = $this->fieldPayload($data);

            if ($fotoKtp) {
                if ($koordinator->foto_ktp) {
                    Storage::disk('public')->delete($koordinator->foto_ktp);
                }
                $payload['foto_ktp'] = $fotoKtp->store('koordinator/foto-ktp', 'public');
            }

            if ($fotoFormal) {
                if ($koordinator->foto_formal) {
                    Storage::disk('public')->delete($koordinator->foto_formal);
                }
                $payload['foto_formal'] = $fotoFormal->store('koordinator/foto-formal', 'public');
            }

            $koordinator->update($payload);

            if (! empty($data['password']) && $koordinator->user) {
                $koordinator->user->update(['password' => bcrypt($data['password'])]);
            }

            return $koordinator->fresh();
        });
    }

    public function delete(Koordinator $koordinator): void
    {
        DB::transaction(function () use ($koordinator) {
            if ($koordinator->foto_ktp) {
                Storage::disk('public')->delete($koordinator->foto_ktp);
            }
            if ($koordinator->foto_formal) {
                Storage::disk('public')->delete($koordinator->foto_formal);
            }

            $koordinator->delete();
        });
    }

    /**
     * Non-file fields shared between store() and update(), keyed for mass assignment.
     */
    private function fieldPayload(array $data): array
    {
        return [
            'nama_lengkap' => $data['nama_lengkap'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'provinsi_ktp' => $data['provinsi_ktp'],
            'kabupaten_ktp' => $data['kabupaten_ktp'],
            'kecamatan_ktp' => $data['kecamatan_ktp'],
            'desa_ktp' => $data['desa_ktp'],
            'rt_ktp' => $data['rt_ktp'],
            'rw_ktp' => $data['rw_ktp'],
            'alamat_lengkap_ktp' => $data['alamat_lengkap_ktp'],
            'tipe_wilayah_kerja' => $data['tipe_wilayah_kerja'],
            'provinsi_kerja' => $data['provinsi_kerja'],
            'kabupaten_kerja' => $data['tipe_wilayah_kerja'] === 'Kabupaten' ? ($data['kabupaten_kerja'] ?? null) : null,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'status' => $data['status'],
        ];
    }
}
