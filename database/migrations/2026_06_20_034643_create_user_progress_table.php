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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();                                                                         // BIGINT PK Auto Increment [cite: 120]
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');              // FK ke users [cite: 120]
            $table->foreignId('module_id')->constrained('learning_modules')->onDelete('cascade'); // FK ke learning_modules [cite: 120]
            $table->integer('score')->default(0);                                                 // Nilai kuis user [cite: 120]
            $table->boolean('is_completed')->default(false);                                      // Status penyelesaian [cite: 120]
            $table->timestamp('last_accessed')->nullable();                                       // Tanggal terakhir diakses [cite: 120]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
