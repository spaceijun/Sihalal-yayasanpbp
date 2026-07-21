<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ktp_verifikasi_sessions', function (Blueprint $table) {
            $table->unsignedTinyInteger('ktp_count')->default(1)->after('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('ktp_verifikasi_sessions', function (Blueprint $table) {
            $table->dropColumn('ktp_count');
        });
    }
};
