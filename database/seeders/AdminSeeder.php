<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'koordinator']);
        Role::create(['name' => 'enumerator']);

        // Superadmin
        $superadmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
            'telephone' => '085876550051',
            'email_verified_at' => now(),
            'password' => bcrypt('superadmin'),
            'role' => 'superadmin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $superadmin->assignRole('superadmin');

        //     // Buat user admin desa
        //     $adminDesa = User::create([
        //         'name' => 'Users',
        //         'email' => 'users@gmail.com',
        //         'telephone' => '085876550051',
        //         'email_verified_at' => now(),
        //         'password' => bcrypt('password'),
        //         'role' => 'User',
        //         'created_at' => now(),
        //         'updated_at' => now(),

        //     ]);
        //     $adminDesa->assignRole('users');
    }
}
