<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kembalikan status enum ke nilai asli (lowercase)
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open'");

        // Hapus kolom data_lapangan_id yang ditambahkan sebelumnya
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('data_lapangan_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('data_lapangan_id')
                ->nullable()
                ->constrained('data_lapangans')
                ->nullOnDelete()
                ->after('user_id');
        });

        DB::statement("ALTER TABLE tickets MODIFY status ENUM('Open','Proses','Closed') NOT NULL DEFAULT 'Open'");
    }
};
