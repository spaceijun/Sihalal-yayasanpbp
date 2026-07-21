<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ktp_verifikasi_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_key', 64)->unique()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('batch_id', 64)->nullable();           // Laravel Bus Batch ID
            $table->unsignedSmallInteger('total_photos')->default(0);
            $table->unsignedSmallInteger('processed')->default(0);
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->string('ktp_nama', 255)->nullable();
            $table->string('ktp_nik', 20)->nullable();
            $table->string('ktp_url', 500)->nullable();           // URL foto KTP sementara
            $table->json('results')->nullable();                  // top 3 hasil setelah selesai
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ktp_verifikasi_sessions');
    }
};
