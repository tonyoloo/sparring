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
        Schema::table('fighters', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('region');
            $table->unsignedBigInteger('city_id')->nullable()->after('country_id');

            // Add foreign key constraints
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('city_id')->references('id')->on('cities');

            // Add indexes for performance
            $table->index('country_id');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fighters', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropIndex(['country_id']);
            $table->dropIndex(['city_id']);
            $table->dropColumn(['country_id', 'city_id']);
        });
    }
};
