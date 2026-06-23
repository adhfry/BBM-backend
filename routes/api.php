<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\Api\LearningController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {

    // ==========================================
    // 1. AUTH & USER API
    // ==========================================
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Harus Login (Dilindungi Sanctum Cookie)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
    });

    // ==========================================
    // 2. PUBLIC API
    // ==========================================
    Route::get('/public/about', [PublicController::class, 'about']);
    Route::post('/public/contact', [PublicController::class, 'contact']);

    // ==========================================
    // 3. LEARNING & GAMIFICATION API
    // ==========================================
    Route::get('/learning/modules', [LearningController::class, 'getModules']);
    Route::get('/learning/modules/{id}', [LearningController::class, 'getModuleDetails']);
    Route::get('/learning/modules/{id}/quizzes', [LearningController::class, 'getModuleQuizzes']);

    // Submit Quiz dan Riwayat Belajar butuh login
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/learning/quizzes/submit', [LearningController::class, 'submitQuiz']);
        Route::get('/learning/progress', [LearningController::class, 'getProgress']);
    });

    // ==========================================
    // 4. DICTIONARY & LINGUISTICS API
    // ==========================================
    Route::get('/dictionary', [DictionaryController::class, 'search']);
    Route::get('/dictionary/syllables', [DictionaryController::class, 'getSyllables']);
    Route::post('/dictionary/syllabify', [DictionaryController::class, 'syllabify']);

    // ==========================================
    // 5. TRANSLATION & AI MICROSERVICE API
    // ==========================================
    Route::post('/translate', [TranslationController::class, 'translate']);
    Route::post('/ai/ocr', [TranslationController::class, 'ocr']);
    Route::post('/ai/speech-to-text', [TranslationController::class, 'speechToText']);

});
