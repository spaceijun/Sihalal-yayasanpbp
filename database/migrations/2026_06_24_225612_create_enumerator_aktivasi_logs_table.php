<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enumerator_aktivasi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enumerator_id')->constrained('enumerators')->cascadeOnDelete();
            $table->string('diaktifkan_oleh')->nullable(); // nama admin
            $table->string('surat_pernyataan')->nullable(); // path file
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_aktivasi')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enumerator_aktivasi_logs');
    }
};
