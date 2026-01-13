<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create prize_categories table
        Schema::create('prize_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama kategori (misal: "Hadiah Utama", "Hadiah Doorprize")
            $table->integer('order')->default(0); // Urutan kategori
            $table->timestamps();
        });

        // Add category_id to prizes table
        Schema::table('prizes', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('form_id')->constrained('prize_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prizes', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('prize_categories');
    }
};
