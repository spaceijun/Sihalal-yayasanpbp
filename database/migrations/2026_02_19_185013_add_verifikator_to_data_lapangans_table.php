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
            $table->string('verifikator')->nullable()->after('status');
            $table->dateTime('tanggal_verifikasi')->nullable()->after('verifikator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('verifikator');
            $table->dropColumn('tanggal_verifikasi');
        });
    }
};
