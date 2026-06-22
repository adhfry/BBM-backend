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
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();                                                           // BIGINT PK Auto Increment [cite: 108]
            $table->string('kata_indo', 100)->index();                              // Kata bahasa Indonesia dengan INDEX [cite: 108]
            $table->string('kata_madura', 100)->index();                            // Kata bahasa Madura dengan INDEX [cite: 108]
            $table->enum('tingkatan', ['lomrah', 'tengngaan', 'alos'])->nullable(); // Tingkatan bahasa Madura [cite: 108]
            $table->string('kategori', 50)->nullable();                             // Kategori kata seperti hewan, angka, dll [cite: 108]
            $table->string('audio_path', 255)->nullable();                          // Path file audio pelafalan asli [cite: 108]
            $table->text('contoh_kalimat')->nullable();                             // Contoh penggunaan dalam kalimat [cite: 108]
            $table->timestamps();
        });
        // Schema::create('vocabularies', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('kata_indo', 100)->index();
        //     $table->string('kata_madura', 100)->index();
        //     $table->enum('tingkatan', ['lomrah', 'tengngaan', 'alos'])->nullable();
        //     $table->string('kategori', 50)->nullable();
        //     $table->string('audio_path', 255)->nullable();
        //     $table->text('contoh_kalimat')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabularies');
    }
};