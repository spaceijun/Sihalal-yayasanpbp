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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('page', 50)->unique()->comment('home, about, contact');
            $table->string('title', 255);
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
        });

        // Insert default pages
        DB::table('company_profiles')->insert([
            [
                'page' => 'home',
                'title' => 'Kawulo Halal - Jasa Sertifikasi Halal Low Risk',
                'meta_description' => 'Kawulo Halal memberikan layanan sertifikasi halal untuk UMKM dengan proses mudah dan cepat.',
                'meta_keywords' => 'sertifikasi halal, halal, umkm, low risk, kawulo halal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'about',
                'title' => 'Tentang Kami - Kawulo Halal',
                'meta_description' => 'Kawulo Halal adalah solusi terpercaya untuk sertifikasi halal UMKM di Indonesia.',
                'meta_keywords' => 'tentang kami, kawulo halal, sertifikasi halal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page' => 'contact',
                'title' => 'Hubungi Kami - Kawulo Halal',
                'meta_description' => 'Hubungi Kawulo Halal untuk informasi sertifikasi halal.',
                'meta_keywords' => 'kontak, hubungi kami, kawulo halal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
