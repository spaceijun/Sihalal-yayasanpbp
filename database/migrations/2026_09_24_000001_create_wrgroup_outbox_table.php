<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Outbox integrasi WRGROUP Super Apps: setiap event yang harus dikirim dicatat
 * dulu di sini (event_id UUID v4 tetap untuk semua percobaan), lalu dikirim oleh
 * job / scheduler dengan retry + exponential backoff.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wrgroup_outbox', function (Blueprint $table) {
            $table->id();

            $table->string('event_id', 100)->unique()
                ->comment('UUID v4 — sama untuk semua percobaan kirim ulang');
            $table->string('endpoint', 20)->comment('invoice | refund | payment | nihil');
            $table->string('transaction_id', 100)
                ->comment('ID dokumen di sistem pilar; tetap sama untuk semua versi');
            $table->unsignedInteger('version')->default(1);
            $table->string('type', 60)->nullable();
            $table->string('period', 10)->nullable()->comment('Triwulan YYYY-Qn dari tanggal transaksi');

            $table->string('source_type', 40)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();

            $table->json('payload')->comment('Body request persis seperti yang dikirim');
            $table->string('payload_hash', 64)->comment('SHA-256 isi data — deteksi versi tanpa perubahan');

            $table->string('status', 20)->default('pending')->comment('pending | sent | rejected | failed');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('locked_at')->nullable()->comment('Klaim pengirim (cegah kirim ganda paralel)');

            $table->unsignedSmallInteger('last_http_status')->nullable();
            $table->json('last_response')->nullable();
            $table->text('last_error')->nullable();
            $table->string('environment', 20)->nullable()->comment('production | testing (dari respons WRGROUP)');
            $table->boolean('persisted')->nullable();

            $table->timestamp('event_time')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['endpoint', 'transaction_id', 'version'], 'wrgroup_outbox_doc_unique');
            $table->index(['status', 'next_attempt_at']);
            $table->index(['endpoint', 'period']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wrgroup_outbox');
    }
};
