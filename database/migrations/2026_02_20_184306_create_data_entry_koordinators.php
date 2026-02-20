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
        Schema::create('data_entry_koordinator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_entry_id')->constrained('data_entrys')->onDelete('cascade');
            $table->foreignId('koordinator_id')->constrained('koordinators')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_entry_koordinators');
    }
};
