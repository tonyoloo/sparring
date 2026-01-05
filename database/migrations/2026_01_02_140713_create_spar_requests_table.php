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
        Schema::create('spar_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('fighters')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('fighters')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('message')->nullable();
            $table->dateTime('requested_date')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['sender_id', 'status']);
            $table->index(['receiver_id', 'status']);
            $table->index('status');
            $table->index('requested_date');

            // Prevent duplicate pending requests between same users
            $table->unique(['sender_id', 'receiver_id'], 'unique_pending_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spar_requests');
    }
};
