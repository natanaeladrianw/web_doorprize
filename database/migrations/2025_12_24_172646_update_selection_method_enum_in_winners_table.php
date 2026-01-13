<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah kolom selection_method untuk menerima 'random' sebagai tambahan dari 'manual' dan 'automatic'
        DB::statement("ALTER TABLE winners MODIFY selection_method ENUM('manual', 'automatic', 'random') DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE winners MODIFY selection_method ENUM('manual', 'automatic') DEFAULT 'manual'");
    }
};
