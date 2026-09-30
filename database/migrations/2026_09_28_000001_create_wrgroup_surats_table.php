<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wrgroup_surats', function (Blueprint $table) {
            $table->id();
            $table->string('wrgroup_hashed_id')->nullable()->unique()
                ->comment('hashed_id SuratDokumen di sisi WRGROUP — null bila pengiriman gagal');
            $table->string('surat_jenis_kode');
            $table->string('jenis_nama');
            $table->string('perihal');
            $table->json('data_variabel')->nullable();
            $table->string('status')->nullable()
                ->comment('diajukan/diperiksa/disetujui/terbit/dibatalkan — disinkronkan dari WRGROUP; null bila gagal kirim');
            $table->string('nomor')->nullable();
            $table->timestamp('diterbitkan_at')->nullable();
            $table->text('catatan_terakhir')->nullable();
            $table->string('pdf_path')->nullable()->comment('salinan lokal PDF, diunduh sekali saat pertama kali terbit');
            $table->boolean('berhasil_kirim')->default(false);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('response_message')->nullable();
            $table->foreignId('diajukan_oleh')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wrgroup_surats');
    }
};
