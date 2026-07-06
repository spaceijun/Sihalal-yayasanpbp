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
        Schema::create('analisis_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->index(); // Nama produk (e.g., "PEYEK BU INAH")
            $table->string('kemasan')->nullable(); // Jenis kemasan
            $table->json('bahan')->nullable(); // Array of bahan objects
            $table->json('proses')->nullable(); // Array of proses objects
            $table->text('catatan_halal')->nullable(); // Catatan halal
            $table->string('status_halal')->default('PERLU_VERIFIKASI'); // AMAN, PERLU_VERIFIKASI, BERISIKO
            $table->string('sertifikasi')->nullable(); // Info sertifikasi
            $table->string('google_search_url')->nullable(); // URL Google Search
            $table->string('image_path')->nullable(); // Path ke foto produk (jika ada)
            $table->json('raw_analysis')->nullable(); // Raw data dari Google/Claude
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // User yang input
            $table->foreignId('data_lapangan_id')->nullable()->constrained()->nullOnDelete(); // Link ke data lapang
            $table->boolean('is_approved')->default(false); // Approved oleh admin
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes(); // Soft delete

            // Index untuk pencarian cepat
            $table->index(['product_name', 'is_approved']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analisis_products');
    }
};
