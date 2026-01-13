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
        if (!Schema::hasColumn('winners', 'prize_id')) {
            Schema::table('winners', function (Blueprint $table) {
                // Add column first without constraint
                $table->unsignedBigInteger('prize_id')->nullable()->after('form_submission_id');
                // Add constraint separately
                $table->foreign('prize_id')->references('id')->on('prizes')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('winners', 'prize_id')) {
            Schema::table('winners', function (Blueprint $table) {
                $table->dropForeign(['prize_id']);
                $table->dropColumn('prize_id');
            });
        }
    }
};
