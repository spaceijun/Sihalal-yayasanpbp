<?php

namespace App\Services\Superadmin;

use App\Models\DataEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DataEntryService
{
    public function store(array $data): DataEntry
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'password' => bcrypt($data['password']),
                'role' => 'data_entry',
            ]);
            $user->assignRole('data_entry');

            $dataEntry = DataEntry::create([
                'user_id' => $user->id,
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'alamat' => $data['alamat'],
                'status' => $data['status'],
                'entry_type' => $data['entry_type'],
            ]);

            if (! empty($data['koordinator_ids'])) {
                $dataEntry->koordinators()->sync($data['koordinator_ids']);
            }

            return $dataEntry;
        });
    }

    public function update(DataEntry $dataEntry, array $data): DataEntry
    {
        return DB::transaction(function () use ($dataEntry, $data) {
            $dataEntry->update([
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'alamat' => $data['alamat'],
                'status' => $data['status'],
                'entry_type' => $data['entry_type'],
            ]);

            // Sync koordinator (otomatis handle tambah/hapus)
            $dataEntry->koordinators()->sync($data['koordinator_ids'] ?? []);

            // Update password jika diisi
            if (isset($data['password']) && $data['password'] !== '' && $dataEntry->user) {
                $dataEntry->user->update([
                    'password' => bcrypt($data['password']),
                ]);
            }

            return $dataEntry->fresh();
        });
    }

    public function delete(DataEntry $dataEntry): void
    {
        DB::transaction(function () use ($dataEntry) {
            // Detach koordinator dulu sebelum delete
            $dataEntry->koordinators()->detach();
            $dataEntry->delete();
        });
    }
}
