<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Definisikan default requirements untuk masing-masing posisi agar sesuai form sebelumnya
        $requirementsPendamping = [
            ['label' => 'Nama Lengkap', 'type' => 'text', 'field_key' => 'nama_lengkap', 'required' => true],
            ['label' => 'NIK', 'type' => 'text', 'field_key' => 'nik', 'required' => true],
            ['label' => 'Jenis Kelamin', 'type' => 'select', 'field_key' => 'jenis_kelamin', 'required' => true, 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Nomor Telepon', 'type' => 'text', 'field_key' => 'telephone', 'required' => true],
            ['label' => 'Alamat Lengkap', 'type' => 'textarea', 'field_key' => 'alamat_lengkap', 'required' => true],
            ['label' => 'Pendidikan Terakhir', 'type' => 'text', 'field_key' => 'pendidikan_terakhir', 'required' => true],
            ['label' => 'Pengalaman Kerja / Organisasi', 'type' => 'textarea', 'field_key' => 'pengalaman', 'required' => true],
            ['label' => 'Rekomendasi', 'type' => 'text', 'field_key' => 'rekomendasi', 'required' => false],
            ['label' => 'Foto Diri (3x4)', 'type' => 'file', 'field_key' => 'foto_diri', 'required' => true, 'accept' => 'image/*'],
            ['label' => 'Foto KTP', 'type' => 'file', 'field_key' => 'foto_ktp', 'required' => true, 'accept' => 'image/*'],
            ['label' => 'Foto Ijazah (PDF/Image)', 'type' => 'file', 'field_key' => 'foto_ijasah', 'required' => true, 'accept' => 'image/*,application/pdf'],
            ['label' => 'Pakta Integritas (PDF/Image)', 'type' => 'file', 'field_key' => 'pakta_integritas', 'required' => true, 'accept' => 'image/*,application/pdf'],
        ];

        $requirementsDataEntry = [
            ['label' => 'Nama Lengkap', 'type' => 'text', 'field_key' => 'nama_lengkap', 'required' => true],
            ['label' => 'NIK', 'type' => 'text', 'field_key' => 'nik', 'required' => true],
            ['label' => 'Jenis Kelamin', 'type' => 'select', 'field_key' => 'jenis_kelamin', 'required' => true, 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Nomor Telepon', 'type' => 'text', 'field_key' => 'telephone', 'required' => true],
            ['label' => 'Tipe Entry', 'type' => 'select', 'field_key' => 'type_entry', 'required' => true, 'options' => ['OSS', 'SIHALAL']],
            ['label' => 'Alamat Lengkap', 'type' => 'textarea', 'field_key' => 'alamat_lengkap', 'required' => true],
            ['label' => 'Pendidikan Terakhir', 'type' => 'text', 'field_key' => 'pendidikan_terakhir', 'required' => true],
            ['label' => 'Pengalaman Kerja / Organisasi', 'type' => 'textarea', 'field_key' => 'pengalaman', 'required' => true],
            ['label' => 'Rekomendasi', 'type' => 'text', 'field_key' => 'rekomendasi', 'required' => false],
            ['label' => 'Foto Diri (3x4)', 'type' => 'file', 'field_key' => 'foto_diri', 'required' => true, 'accept' => 'image/*'],
            ['label' => 'Foto KTP', 'type' => 'file', 'field_key' => 'foto_ktp', 'required' => true, 'accept' => 'image/*'],
            ['label' => 'Foto Ijazah (PDF/Image)', 'type' => 'file', 'field_key' => 'foto_ijasah', 'required' => true, 'accept' => 'image/*,application/pdf'],
            ['label' => 'Pakta Integritas (PDF/Image)', 'type' => 'file', 'field_key' => 'pakta_integritas', 'required' => true, 'accept' => 'image/*,application/pdf'],
        ];

        $requirementsAdminUmum = [
            ['label' => 'Nama Lengkap', 'type' => 'text', 'field_key' => 'nama_lengkap', 'required' => true],
            ['label' => 'NIK', 'type' => 'text', 'field_key' => 'nik', 'required' => true],
            ['label' => 'Jenis Kelamin', 'type' => 'select', 'field_key' => 'jenis_kelamin', 'required' => true, 'options' => ['Laki-laki', 'Perempuan']],
            ['label' => 'Nomor Telepon', 'type' => 'text', 'field_key' => 'telephone', 'required' => true],
            ['label' => 'Alamat Lengkap', 'type' => 'textarea', 'field_key' => 'alamat_lengkap', 'required' => true],
            ['label' => 'Pendidikan Terakhir', 'type' => 'text', 'field_key' => 'pendidikan_terakhir', 'required' => true],
            ['label' => 'Pengalaman Kerja / Organisasi', 'type' => 'textarea', 'field_key' => 'pengalaman', 'required' => true],
            ['label' => 'Rekomendasi', 'type' => 'text', 'field_key' => 'rekomendasi', 'required' => false],
            ['label' => 'Foto Diri (3x4)', 'type' => 'file', 'field_key' => 'foto_diri', 'required' => true, 'accept' => 'image/*'],
            ['label' => 'Foto KTP', 'type' => 'file', 'field_key' => 'foto_ktp', 'required' => true, 'accept' => 'image/*'],
            ['label' => 'Foto Ijazah (PDF/Image)', 'type' => 'file', 'field_key' => 'foto_ijasah', 'required' => true, 'accept' => 'image/*,application/pdf'],
            ['label' => 'Pakta Integritas (PDF/Image)', 'type' => 'file', 'field_key' => 'pakta_integritas', 'required' => true, 'accept' => 'image/*,application/pdf'],
        ];

        // 2. Buat default recruitment posts
        $postsData = [
            [
                'nama_loker' => 'Pendaftaran Pendamping Lapangan',
                'posisi' => 'PENDAMPING',
                'slug' => 'pendaftaran-pendamping-lapangan',
                'is_active' => true,
                'requirements' => json_encode($requirementsPendamping),
                'deskripsi' => 'Lowongan pendaftaran untuk Pendamping Lapangan Sihalal Yayasan PBP.',
                'jobdesk' => 'Melakukan pendampingan sertifikasi halal bagi pelaku usaha.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loker' => 'Pendaftaran Data Entry',
                'posisi' => 'DATA ENTRY',
                'slug' => 'pendaftaran-data-entry',
                'is_active' => true,
                'requirements' => json_encode($requirementsDataEntry),
                'deskripsi' => 'Lowongan pendaftaran untuk Data Entry Sihalal Yayasan PBP.',
                'jobdesk' => 'Melakukan penginputan data sertifikasi halal ke sistem.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loker' => 'Pendaftaran Admin Umum',
                'posisi' => 'ADMIN UMUM',
                'slug' => 'pendaftaran-admin-umum',
                'is_active' => true,
                'requirements' => json_encode($requirementsAdminUmum),
                'deskripsi' => 'Lowongan pendaftaran untuk Admin Umum Sihalal Yayasan PBP.',
                'jobdesk' => 'Mengelola administrasi rekrutmen dan operasional kantor.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($postsData as $postData) {
            // Cek jika posisi ini sudah ada di recruitment_posts untuk menghindari duplikasi
            $exists = DB::table('recruitment_posts')
                ->where('posisi', $postData['posisi'])
                ->first();

            if (!$exists) {
                $postId = DB::table('recruitment_posts')->insertGetId($postData);
            } else {
                $postId = $exists->id;
            }

            // 3. Update data recruitment lama yang masih null agar terhubung ke lowongan yang sesuai
            DB::table('recruitments')
                ->whereNull('recruitment_post_id')
                ->where('recruit_type', $postData['posisi'])
                ->update(['recruitment_post_id' => $postId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan recruitment_post_id ke null
        DB::table('recruitments')->update(['recruitment_post_id' => null]);

        // Hapus default posts
        DB::table('recruitment_posts')
            ->whereIn('slug', [
                'pendaftaran-pendamping-lapangan',
                'pendaftaran-data-entry',
                'pendaftaran-admin-umum'
            ])
            ->delete();
    }
};
