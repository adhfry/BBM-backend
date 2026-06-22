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
        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();                                                    // BIGINT PK Auto Increment [cite: 116]
            $table->string('title', 150);                                    // Judul modul [cite: 116]
            $table->text('description')->nullable();                         // Deskripsi modul [cite: 116]
            $table->enum('type', ['huruf', 'suku_kata', 'kata', 'kalimat']); // Tipe metodologi [cite: 116]
            $table->integer('order_index');                                  // Urutan modul belajar [cite: 116]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_modules');
    }
};
