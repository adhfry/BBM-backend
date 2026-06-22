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
        Schema::create('syllables', function (Blueprint $table) {
            $table->id();                              // BIGINT PK Auto Increment [cite: 112]
            $table->string('suku_kata', 50)->unique(); // UNIQUE, NOT NULL [cite: 112]
            $table->string('pola', 10)->nullable();    // Pola FSA seperti V, KV, KKV [cite: 112]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllables');
    }
};