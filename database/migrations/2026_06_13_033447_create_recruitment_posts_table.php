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
        Schema::create('recruitment_posts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_loker');
            $table->enum('posisi', ['PENDAMPING', 'DATA ENTRY', 'ADMIN UMUM']);
            $table->text('deskripsi')->nullable();
            $table->text('jobdesk')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('slug')->unique(); // Slug unik untuk URL publik
            // requirements: JSON array of field definitions
            // [{"label":"Nama Lengkap","type":"text","required":true}, ...]
            // type: text, textarea, file, checkbox, select, radio
            $table->json('requirements')->nullable();
            $table->timestamp('tanggal_buka')->nullable();
            $table->timestamp('tanggal_tutup')->nullable();
            $table->timestamps();
        });

        // Tambah foreign key ke recruitments untuk menghubungkan pendaftar ke lowongan
        Schema::table('recruitments', function (Blueprint $table) {
            $table->foreignId('recruitment_post_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('recruitment_posts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropForeign(['recruitment_post_id']);
            $table->dropColumn('recruitment_post_id');
        });
        Schema::dropIfExists('recruitment_posts');
    }
};
