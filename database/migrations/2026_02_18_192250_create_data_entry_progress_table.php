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
        Schema::create('data_entry_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('data_entry_id')->constrained('data_entrys')->onDelete('cascade');
            $table->foreignId('data_lapangan_id')->constrained('data_lapangans')->onDelete('cascade');
            $table->string('action'); 
            $table->json('old_data')->nullable(); 
            $table->json('new_data')->nullable(); 
            $table->timestamp('actioned_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_entry_progress');
    }
};
