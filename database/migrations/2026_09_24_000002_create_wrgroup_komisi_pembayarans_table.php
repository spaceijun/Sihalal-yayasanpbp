<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wrgroup_komisi_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('event_id', 100)->unique()->comment('Kunci idempotensi yang dikirim ke WRGROUP');
            $table->string('komisi_reference')->comment('hashed_id KomisiPeriode di WRGROUP');
            $table->string('periode', 20);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_transaksi_bank');
            $table->string('referensi_bank')->nullable();
            $table->text('catatan')->nullable();
            $table->string('bukti_setoran')->comment('Path file lokal, disk public');
            $table->boolean('berhasil')->nullable()->comment('null = belum pernah dikirim');
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('response_message')->nullable();
            $table->foreignId('dikirim_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wrgroup_komisi_pembayarans');
    }
};
