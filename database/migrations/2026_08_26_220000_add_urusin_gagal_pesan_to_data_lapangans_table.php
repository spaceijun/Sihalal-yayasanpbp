<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan pesan kegagalan submit terakhir (NIB/Halal) supaya Admin Umum/Superadmin bisa
     * melihat alasan "gagal_kirim" langsung di panel detail, tanpa perlu membuka
     * storage/logs/laravel.log secara manual — gap yang ditemukan saat test sandbox pertama.
     */
    public function up(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->text('urusin_gagal_pesan')->nullable()->after('urusin_last_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('urusin_gagal_pesan');
        });
    }
};
