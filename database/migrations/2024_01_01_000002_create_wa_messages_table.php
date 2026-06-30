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
        Schema::create('wa_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wa_device_id')->nullable()->constrained('wa_devices')->onDelete('cascade');
            $table->string('sender_number', 20)->nullable();
            $table->string('receiver_number', 20);
            $table->text('message_template')->nullable();
            $table->text('processed_message')->nullable();
            $table->text('footer_message')->nullable();
            $table->enum('message_type', ['text', 'media'])->default('text');
            $table->string('media_url')->nullable();
            $table->string('media_type', 20)->nullable();
            $table->text('media_caption')->nullable();
            $table->enum('status', ['pending', 'processing', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->enum('message_source', ['website', 'api'])->default('website');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('wa_device_id');
            $table->index('receiver_number');
            $table->index('status');
            $table->index('message_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_messages');
    }
};
