<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUmumRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Jalankan dengan: php artisan db:seed --class=AdminUmumRoleSeeder
     * 
     * Untuk assign role ke user tertentu:
     *   $user = \App\Models\User::find($id);
     *   $user->assignRole('admin_umum');
     *   $user->update(['role' => 'admin_umum']);
     */
    public function run(): void
    {
        // Buat role admin_umum jika belum ada
        Role::firstOrCreate([
            'name'       => 'admin_umum',
            'guard_name' => 'web',
        ]);

        $this->command->info('✅ Role admin_umum berhasil dibuat.');
    }
}
