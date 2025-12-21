<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnumeratorSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID koordinator yang tersedia
        $koordinatorIds = DB::table('koordinators')->pluck('id')->toArray();

        // Cek jika data koordinator kosong untuk menghindari error
        if (empty($koordinatorIds)) {
            $this->command->info('Data Koordinator kosong! Sila jalankan KoordinatorSeeder terlebih dahulu.');
            return;
        }

        $data = [
            ['nama' => 'Andi Wijaya', 'alamat' => 'Jl. Merpati No. 12'],
            ['nama' => 'Bambang Pamungkas', 'alamat' => 'Jl. Garuda No. 45'],
            ['nama' => 'Candra Kirana', 'alamat' => 'Jl. Melati No. 09'],
            ['nama' => 'Dina Mariana', 'alamat' => 'Jl. Mawar No. 21'],
            ['nama' => 'Eko Prasetyo', 'alamat' => 'Jl. Anggrek No. 33'],
        ];

        foreach ($data as $item) {
            DB::table('enumerators')->insert([
                // Mengambil ID koordinator secara acak dari data yang ada
                'koordinator_id' => $koordinatorIds[array_rand($koordinatorIds)],
                'nama_lengkap' => $item['nama'],
                'telephone' => '0857' . rand(10000000, 99999999),
                'alamat' => $item['alamat'],
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
