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
        Schema::create('fighter_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fighter_id')->constrained('fighters')->onDelete('cascade');
            $table->string('photo_path');
            $table->string('photo_name')->nullable();
            $table->text('caption')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['fighter_id', 'is_primary']);
            $table->index(['fighter_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fighter_photos');
    }
};
