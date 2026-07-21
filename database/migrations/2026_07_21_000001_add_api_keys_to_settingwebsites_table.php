<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settingwebsites', function (Blueprint $table) {
            $table->text('gemini_api_key')->nullable()->after('logo');
            $table->text('anthropic_api_key')->nullable()->after('gemini_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('settingwebsites', function (Blueprint $table) {
            $table->dropColumn(['gemini_api_key', 'anthropic_api_key']);
        });
    }
};
