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
        Schema::create('verifikators', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('telephone');
            $table->text('alamat_lengkap');
            $table->decimal('rate_per_data', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikators');
    }
};
