<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_pendampings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('data_lapangan_id')
                ->nullable()
                ->constrained('data_lapangans')
                ->nullOnDelete();
            $table->string('no_tiket')->unique();
            $table->text('isi_kendala');
            $table->enum('status', ['Open', 'Proses', 'Closed'])->default('Open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_pendampings');
    }
};
