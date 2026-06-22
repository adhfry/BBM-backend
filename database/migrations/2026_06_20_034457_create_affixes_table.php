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
        Schema::create('affixes', function (Blueprint $table) {
            $table->id();                         // BIGINT PK Auto Increment
            $table->enum('bahasa', ['id', 'md']); // Bahasa Indo atau Madura
            $table->string('awalan', 20)->nullable();
            $table->string('akhiran', 20)->nullable();
            $table->string('letak', 20); // 'awalan', 'akhiran', 'awalan akhiran'
            $table->string('arti_awalan', 50)->nullable();
            $table->string('arti_akhiran', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affixes');
    }
};
