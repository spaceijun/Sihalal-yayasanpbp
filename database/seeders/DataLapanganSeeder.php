<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataLapanganSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua ID enumerator yang tersedia
        $enumeratorIds = DB::table('enumerators')->pluck('id')->toArray();

        if (empty($enumeratorIds)) {
            $this->command->error('Data Enumerator tidak ditemukan! Jalankan EnumeratorSeeder terlebih dahulu.');
            return;
        }

        $penerimaUsaha = ['Warung Barokah', 'Kripik Renyah', 'Toko Berkah', 'Sate Madura', 'Bakso Solo'];

        for ($i = 0; $i < 5; $i++) {
            DB::table('data_lapangans')->insert([
                'enumerator_id'   => $enumeratorIds[array_rand($enumeratorIds)],
                'nama_pu'         => $penerimaUsaha[$i],
                'nik'             => '3201' . rand(1000000000, 9999999999),
                'rt'              => '00' . rand(1, 9),
                'rw'              => '00' . rand(1, 5),
                'alamat'          => 'Jl. Contoh Lokasi No. ' . ($i + 1),
                'titik_koordinat' => '-6.' . rand(100000, 900000) . ', 106.' . rand(100000, 900000),
                'foto_ktp'        => 'uploads/ktp/dummy_' . ($i + 1) . '.jpg',
                'foto_rumah'      => 'uploads/rumah/dummy_' . ($i + 1) . '.jpg',
                'foto_pendamping' => 'uploads/pendamping/dummy_' . ($i + 1) . '.jpg',
                'foto_proses'     => 'uploads/proses/dummy_' . ($i + 1) . '.jpg',
                'foto_produk'     => 'uploads/produk/dummy_' . ($i + 1) . '.jpg',
                'status'          => ['PENDING', 'PROGRESS OSS', 'TERBIT SH'][rand(0, 2)],
                'status_pembayaran' => 'PENDING',
                'file_oss'        => null,
                'file_sihalal'    => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
