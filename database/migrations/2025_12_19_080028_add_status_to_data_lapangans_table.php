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
            $table->enum('status', ['PENDING', 'PROGRESS OSS', 'PROGRESS SIHALAL', 'TERBIT SH'])->default('PENDING')->after('foto_produk');
            $table->string('file_oss')->nullable()->after('status');
            $table->string('file_sihalal')->nullable()->after('file_oss');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn(['status', 'file_oss', 'file_sihalal']);
        });
    }
};
