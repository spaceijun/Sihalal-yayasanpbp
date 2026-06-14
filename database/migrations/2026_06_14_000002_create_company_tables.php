<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Company Histories (Timeline)
        Schema::create('company_histories', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->year('year');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Company Teams (Management)
        Schema::create('company_teams', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->string('name', 100);
            $table->string('position', 100);
            $table->text('description')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Company Statistics
        Schema::create('company_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('value', 50);
            $table->string('suffix', 20)->default('+');
            $table->string('icon', 50)->default('ri-bar-chart-line');
            $table->string('color', 20)->default('#22c55e');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Company Benefits
        Schema::create('company_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('icon', 50)->default('ri-check-line');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('photo')->nullable();
            $table->string('name', 100);
            $table->string('position', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->longText('testimonial');
            $table->tinyInteger('rating')->default(5);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Article Categories
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });

        // Social Media
        Schema::create('social_media', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 50);
            $table->string('url', 255);
            $table->string('icon', 50)->default('ri-share-line');
            $table->string('color', 20)->default('#22c55e');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default data
        DB::table('company_statistics')->insert([
            ['title' => 'UMKM Tersertifikasi', 'value' => '5000', 'suffix' => '+', 'icon' => 'ri-building-line', 'color' => '#22c55e', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Hari Proses', 'value' => '30', 'suffix' => '', 'icon' => 'ri-timer-line', 'color' => '#2563eb', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Pendamping Halal', 'value' => '50', 'suffix' => '+', 'icon' => 'ri-user-star-line', 'color' => '#f59e0b', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Tingkat Kepuasan', 'value' => '98', 'suffix' => '%', 'icon' => 'ri-heart-line', 'color' => '#ec4899', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('company_benefits')->insert([
            ['title' => 'Proses Cepat', 'icon' => 'ri-speed-line', 'description' => 'Waktu proses sertifikasi hanya 30 hari kerja', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Biaya Terjangkau', 'icon' => 'ri-wallet-line', 'description' => 'Investasi terjangkau untuk UMKM', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Pendampingan Lengkap', 'icon' => 'ri-user-heart-line', 'description' => 'Tim berpengalaman membantu dari awal hingga akhir', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Dokumentasi Mudah', 'icon' => 'ri-file-list-3-line', 'description' => 'Sistem digital untuk upload dokumen', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('testimonials')->insert([
            [
                'name' => 'Ahmad Sutrisno',
                'position' => 'Pemilik Usaha Makanan',
                'company' => 'UD. Berkah Jaya',
                'testimonial' => 'Proses sertifikasi halal bersama Kawulo Halal sangat mudah dan cepat. Timnya sangat membantu dari awal hingga akhir. Sangat direkomendasikan!',
                'rating' => 5,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Rina Marlina',
                'position' => 'Produsen Minuman Tradisional',
                'company' => 'Minuman Segar Indonesia',
                'testimonial' => 'Biaya terjangkau dan prosesnya transparan. Sangat direkomendasikan untuk pelaku UMKM seperti saya yang baru pertama kali mengurus sertifikasi.',
                'rating' => 5,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Budi Santoso',
                'position' => 'Pengusaha Kue Basah',
                'company' => 'Kue Nusantara',
                'testimonial' => 'Dulu saya bingung caranya sertifikasi, ternyata dengan bantuan Kawulo Halal jadi sangat gampang. Terima kasih banyak!',
                'rating' => 4,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        DB::table('article_categories')->insert([
            ['name' => 'Sertifikasi Halal', 'slug' => 'sertifikasi-halal', 'description' => 'Artikel tentang proses dan tips sertifikasi halal', 'icon' => 'ri-shield-check-line', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tips Bisnis', 'slug' => 'tips-bisnis', 'description' => 'Tips dan strategi untuk pelaku UMKM', 'icon' => 'ri-lightbulb-line', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Berita', 'slug' => 'berita', 'description' => 'Berita terbaru tentang ekosistem halal', 'icon' => 'ri-newspaper-line', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tutorial', 'slug' => 'tutorial', 'description' => 'Panduan langkah demi langkah', 'icon' => 'ri-book-open-line', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('social_media')->insert([
            ['platform' => 'Facebook', 'url' => 'https://facebook.com/kawulohalal', 'icon' => 'ri-facebook-fill', 'color' => '#1877f2', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['platform' => 'Instagram', 'url' => 'https://instagram.com/kawulohalal', 'icon' => 'ri-instagram-fill', 'color' => '#e4405f', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['platform' => 'WhatsApp', 'url' => 'https://wa.me/6281234567890', 'icon' => 'ri-whatsapp-fill', 'color' => '#25d366', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['platform' => 'YouTube', 'url' => 'https://youtube.com/@kawulohalal', 'icon' => 'ri-youtube-fill', 'color' => '#ff0000', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media');
        Schema::dropIfExists('article_categories');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('company_benefits');
        Schema::dropIfExists('company_statistics');
        Schema::dropIfExists('company_teams');
        Schema::dropIfExists('company_histories');
    }
};
