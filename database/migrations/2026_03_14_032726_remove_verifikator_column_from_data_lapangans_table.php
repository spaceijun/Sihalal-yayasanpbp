<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropForeign(['verifikator_id']); // drop dulu sebentar
            $table->dropColumn('verifikator');       // hapus kolom lama
            // Tambahkan kembali constraint jika perlu tidak nullable
            $table->foreign('verifikator_id')->references('id')->on('verifikators');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            //
        });
    }
};
