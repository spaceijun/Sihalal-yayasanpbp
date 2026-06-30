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
        Schema::create('wa_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->json('qr_code')->nullable();
            $table->text('credentials')->nullable();
            $table->enum('status', ['disconnected', 'connecting', 'connected'])->default('disconnected');
            $table->timestamp('last_connected_at')->nullable();
            $table->json('device_info')->nullable();
            $table->boolean('reject_call')->default(false);
            $table->boolean('available')->default(true);
            $table->boolean('typing')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_devices');
    }
};
