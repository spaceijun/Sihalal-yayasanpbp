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
        Schema::create('enumerators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koordinator_id')->constrained('koordinators')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('telephone');
            $table->string('foto_diri')->nullable();
            $table->string('no_registrasi')->unique();
            $table->text('alamat');
            $table->enum('status', ['Aktif', 'Tidak Aktif']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enumerators');
    }
};
