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
        Schema::create('fighters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('region')->nullable();
            $table->string('discipline')->nullable();
            $table->enum('stance', ['orthodox', 'southpaw'])->nullable();
            $table->enum('experience', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->enum('level', ['amateur', 'semi_pro', 'professional'])->nullable();
            $table->integer('height')->nullable(); // in cm
            $table->integer('weight')->nullable(); // in kg
            $table->integer('age')->nullable();
            $table->string('primary_profession')->nullable();
            $table->enum('category', ['fighters', 'professionals', 'gyms'])->default('fighters');
            $table->string('profile_image')->nullable();
            $table->text('bio')->nullable();
            $table->json('contact_info')->nullable(); // Store additional contact details as JSON
            $table->enum('badge_level', ['bronze', 'silver', 'gold'])->nullable(); // For professionals
            $table->integer('profession_count')->default(1); // Number of professions/skills
            $table->string('gym_type')->nullable(); // For gyms
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['category', 'is_active']);
            $table->index('region');
            $table->index('discipline');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fighters');
    }
};
