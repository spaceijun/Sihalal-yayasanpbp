<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom pengajuan_lewat (PTSP HALAL / HALALMAX) ke tabel data_lapangans.
     * Data yang sudah ada di production diisi dengan 'PTSP HALAL' sebagai default.
     */
    public function up(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->string('pengajuan_lewat')->nullable()->after('email_sihalal');
        });

        // Backfill data production: semua record yang sudah ada diisi 'PTSP HALAL'
        DB::table('data_lapangans')->whereNull('pengajuan_lewat')->update([
            'pengajuan_lewat' => 'PTSP HALAL',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('pengajuan_lewat');
        });
    }
};
