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
        Schema::create('wa_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
        });

        // Insert default configurations
        \DB::table('wa_gateway_configs')->insert([
            [
                'key' => 'wa_gateway_url',
                'value' => 'http://localhost:3000',
                'description' => 'Node.js Baileys service URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'base_url',
                'value' => 'http://kawalakugateway.test',
                'description' => 'Kawalaku Gateway Laravel API URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'api_key',
                'value' => '',
                'description' => 'API Key untuk Kawalaku Gateway',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'enabled',
                'value' => '1',
                'description' => 'Aktifkan/Nonaktifkan WhatsApp Gateway',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'default_media_url',
                'value' => 'https://kawulohalal.id/assets/logo.png',
                'description' => 'URL default untuk media/image notification',
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
        Schema::dropIfExists('wa_gateway_configs');
    }
};
