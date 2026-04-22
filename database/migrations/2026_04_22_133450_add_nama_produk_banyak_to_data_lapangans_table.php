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
            $table->string('nama_produk_2')->nullable()->after('nama_produk');
            $table->string('nama_produk_3')->nullable()->after('nama_produk_2');
            $table->string('nama_produk_4')->nullable()->after('nama_produk_3');
            $table->string('nama_produk_5')->nullable()->after('nama_produk_4');
            $table->string('foto_produk_2')->nullable()->after('foto_produk');
            $table->string('foto_produk_3')->nullable()->after('foto_produk_2');
            $table->string('foto_produk_4')->nullable()->after('foto_produk_3');
            $table->string('foto_produk_5')->nullable()->after('foto_produk_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('nama_produk_2');
            $table->dropColumn('nama_produk_3');
            $table->dropColumn('nama_produk_4');
            $table->dropColumn('nama_produk_5');
            $table->dropColumn('foto_produk_2');
            $table->dropColumn('foto_produk_3');
            $table->dropColumn('foto_produk_4');
            $table->dropColumn('foto_produk_5');
        });
    }
};
