<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->string('provinsi', 100)->nullable()->after('alamat');
            $table->string('kabupaten', 100)->nullable()->after('provinsi');
            $table->string('kecamatan', 100)->nullable()->after('kabupaten');
            $table->string('kelurahan', 100)->nullable()->after('kecamatan');
            $table->string('rt', 3)->nullable()->after('kelurahan');
            $table->string('rw', 3)->nullable()->after('rt');
            $table->string('kode_pos', 5)->nullable()->after('rw');
        });
    }

    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn(['provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'rt', 'rw', 'kode_pos']);
        });
    }
};
