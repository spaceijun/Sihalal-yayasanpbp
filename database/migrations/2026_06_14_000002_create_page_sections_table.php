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
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->constrained()->onDelete('cascade');
            $table->string('section_key', 100)->comment('hero, features, stats, testimonials, etc.');
            $table->string('title', 255)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('extra_data')->nullable()->comment('Store additional data like stats, features list, etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_profile_id', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
