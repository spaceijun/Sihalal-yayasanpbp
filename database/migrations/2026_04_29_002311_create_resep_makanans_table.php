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
        Schema::create('resep_makanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->enum('kategori', ['makanan', 'minuman']);
            $table->string('foto');
            $table->text('bahan_makanan');
            $table->text('proses_pembuatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_makanans');
    }
};
