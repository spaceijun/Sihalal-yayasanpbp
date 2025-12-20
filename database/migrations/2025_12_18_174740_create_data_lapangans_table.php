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
        Schema::create('data_lapangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enumerator_id')->constrained('enumerators')->onDelete('cascade');
            $table->string('nama_pu');
            $table->string('nik')->unique();
            $table->string('rt');
            $table->string('rw');
            $table->string('alamat');
            $table->string('titik_koordinat');
            $table->string('foto_ktp');
            $table->string('foto_rumah');
            $table->string('foto_pendamping');
            $table->string('foto_proses');
            $table->string('foto_produk');
            $table->enum('status', ['PENDING', 'PROGRESS OSS', 'PROGRESS SIHALAL', 'TERBIT SH'])->default('PENDING');
            $table->enum('status_pembayaran', ['PENDING', 'PENGAJUAN ', 'DIBAYAR'])->default('PENDING');
            $table->string('file_oss')->nullable();
            $table->string('file_sihalal')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_lapangans');
    }
};
