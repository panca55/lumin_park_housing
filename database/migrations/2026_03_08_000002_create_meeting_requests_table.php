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
        Schema::create('meeting_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('produk_ids'); // Array produk yang direquest meeting
            $table->date('tanggal_meeting');
            $table->time('jam_meeting');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                ->default('pending');
            $table->text('whatsapp_message')->nullable();
            $table->boolean('notified_if_sold')->default(false);
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['tanggal_meeting', 'status']);
            $table->index('notified_if_sold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_requests');
    }
};
