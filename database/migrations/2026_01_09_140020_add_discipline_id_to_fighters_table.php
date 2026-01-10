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
            $table->unsignedBigInteger('discipline_id')->nullable()->after('discipline');

            // Add foreign key constraint
            $table->foreign('discipline_id')->references('id')->on('disciplines');

            // Add index for performance
            $table->index('discipline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fighters', function (Blueprint $table) {
            $table->dropForeign(['discipline_id']);
            $table->dropIndex(['discipline_id']);
            $table->dropColumn('discipline_id');
        });
    }
};
