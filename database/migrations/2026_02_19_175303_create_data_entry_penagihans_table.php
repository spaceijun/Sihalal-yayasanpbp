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
        Schema::create('data_entry_penagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('data_entry_id')->constrained('data_entrys')->onDelete('cascade');
            $table->integer('jumlah_data');
            $table->integer('jumlah_paket');
            $table->bigInteger('nominal');
            $table->enum('status', [
                'Menunggu',
                'Diproses',
                'Dibayar',
                'Ditolak',
            ])->default('Menunggu');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_tagihan');
            $table->timestamp('tanggal_dibayar')->nullable();
            $table->timestamps();
        });

        Schema::create('data_entry_penagihan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penagihan_id')->constrained('data_entry_penagihans')->onDelete('cascade');
            $table->foreignId('data_entry_progress_id')->constrained('data_entry_progress')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_entry_penagihans');
        Schema::dropIfExists('data_entry_penagihan_details');
    }
};
