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
        Schema::create('fee_enumerators', function (Blueprint $table) {
            $table->id();
            $table->enum('skala', ['Global', 'Provinsi']);
            $table->string('provinsi')->nullable();
            $table->enum('tipe_fee', ['Bulan', 'Target']);
            $table->unsignedInteger('target_data')->nullable();
            $table->unsignedBigInteger('nominal_fee');
            $table->boolean('is_aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_enumerators');
    }
};
