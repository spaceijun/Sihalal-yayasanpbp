<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('data_lapangan_id')
                ->nullable()
                ->constrained('data_lapangans')
                ->nullOnDelete()
                ->after('user_id');
        });

        // Ubah enum status ke Open / Proses / Closed
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('Open','Proses','Closed') NOT NULL DEFAULT 'Open'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open'");

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('data_lapangan_id');
        });
    }
};
