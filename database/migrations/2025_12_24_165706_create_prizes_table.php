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
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nama hadiah (misal: "Hadiah Utama", "Hadiah Ke-2")
            $table->text('description')->nullable(); // Deskripsi hadiah
            $table->integer('quantity')->default(1); // Jumlah hadiah yang tersedia
            $table->integer('order')->default(0); // Urutan hadiah
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tambahkan kolom prize_id ke tabel winners
        Schema::table('winners', function (Blueprint $table) {
            $table->foreignId('prize_id')->nullable()->after('form_submission_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->dropForeign(['prize_id']);
            $table->dropColumn('prize_id');
        });

        Schema::dropIfExists('prizes');
    }
};
