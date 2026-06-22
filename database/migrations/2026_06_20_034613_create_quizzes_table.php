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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();                                                                         // BIGINT PK Auto Increment
            $table->foreignId('module_id')->constrained('learning_modules')->onDelete('cascade'); // FK ke learning_modules
            $table->text('question');                                                             // Pertanyaan kuis
            $table->json('options');                                                              // Pilihan ganda format JSON
            $table->string('correct_answer', 100);                                                // Jawaban benar
            $table->enum('type', ['text', 'audio', 'image']);                                     // Tipe soal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
