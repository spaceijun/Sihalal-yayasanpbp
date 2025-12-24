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
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koordinator_id')->nullable()->constrained('koordinators')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('telephone');
            $table->string('alamat_lengkap');
            $table->string('pengalaman');
            $table->string('rekomendasi')->nullable();
            $table->string('pendidikan_terakhir');
            $table->string('foto_diri');
            $table->string('foto_ktp');
            $table->enum('status', ['Melamar', 'Diterima', 'Ditolak'])->default('Melamar');
            $table->string('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
