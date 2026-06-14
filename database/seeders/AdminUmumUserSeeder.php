<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUmumUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Jalankan dengan: php artisan db:seed --class=AdminUmumUserSeeder
     */
    public function run(): void
    {
        // 1. Pastikan role admin_umum sudah ada
        $role = Role::firstOrCreate([
            'name'       => 'admin_umum',
            'guard_name' => 'web',
        ]);

        // 2. Buat atau update user admin umum
        $user = User::updateOrCreate(
            ['email' => 'adminumum@sihalal.id'],
            [
                'name'      => 'Admin Umum',
                'email'     => 'adminumum@sihalal.id',
                'telephone' => '08123456789',
                'password'  => Hash::make('password123'),
                'role'      => 'admin_umum',
            ]
        );

        // 3. Assign role Spatie (sync agar tidak duplikat)
        $user->syncRoles([$role->name]);

        $this->command->info('✅ User Admin Umum berhasil dibuat.');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Name',     $user->name],
                ['Email',    $user->email],
                ['Password', 'password123'],
                ['Role',     'admin_umum'],
            ]
        );
    }
}
