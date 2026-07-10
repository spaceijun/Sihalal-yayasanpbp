<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_entry_penarikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('data_entry_id')->constrained('data_entrys')->onDelete('cascade');
            // Bisa menarik sebagian atau semua penagihan yang sudah dibuat (Menunggu/belum ditarik)
            $table->bigInteger('nominal'); // Nominal yang diminta ditarik
            $table->string('catatan_de')->nullable(); // Catatan dari data entry
            $table->string('catatan_admin')->nullable(); // Catatan dari superadmin saat approve/tolak
            $table->enum('status', [
                'Menunggu',  // Baru diajukan, menunggu review superadmin
                'Diproses',  // Sedang diproses superadmin
                'Disetujui', // Disetujui superadmin → bayar
                'Ditolak',   // Ditolak superadmin
            ])->default('Menunggu');
            $table->string('receipt_path')->nullable(); // Path receipt PDF setelah disetujui
            $table->timestamp('tanggal_pengajuan');
            $table->timestamp('tanggal_diproses')->nullable();
            $table->timestamps();
        });

        // Pivot: penarikan ↔ penagihan (satu penarikan bisa mencakup beberapa penagihan)
        Schema::create('data_entry_penarikan_penagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penarikan_id')
                ->constrained('data_entry_penarikan')
                ->onDelete('cascade');
            $table->foreignId('penagihan_id')
                ->constrained('data_entry_penagihans')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_entry_penarikan_penagihan');
        Schema::dropIfExists('data_entry_penarikan');
    }
};
