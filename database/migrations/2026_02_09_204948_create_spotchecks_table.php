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
        Schema::create('spotchecks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enumerator_id')->constrained('enumerators')->onDelete('cascade');
            $table->foreignId('data_lapangan_id')->constrained('data_lapangans')->onDelete('cascade');
            $table->string('nama_spotcheck')->nullable();
            $table->date('tanggal_spotcheck')->nullable();
            $table->string('foto_pu')->nullable();
            $table->string('hasil_spotcheck')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spotchecks');
    }
};
