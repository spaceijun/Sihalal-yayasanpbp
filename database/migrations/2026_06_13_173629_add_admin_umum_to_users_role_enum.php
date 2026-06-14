<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan nilai 'admin_umum' ke kolom ENUM role di tabel users.
     */
    public function up(): void
    {
        // Modifikasi ENUM untuk menambahkan admin_umum
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('superadmin', 'koordinator', 'data_entry', 'enumerator', 'admin_umum') NOT NULL DEFAULT 'data_entry'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus admin_umum dari ENUM (rollback)
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('superadmin', 'koordinator', 'data_entry', 'enumerator') NOT NULL DEFAULT 'data_entry'");
    }
};
