<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Linguistics\IndoStemmerService;
use App\Services\Linguistics\MaduraStemmerService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /**
     * POST /api/v2/translate
     */
    public function translate(Request $request, IndoStemmerService $indoStemmer, MaduraStemmerService $maduraStemmer)
    {
        $request->validate([
            'text'        => 'required|string',
            'source_lang' => 'required|in:id,md', // id = Indo, md = Madura
            'target_lang' => 'required|in:id,md',
        ]);

        $text = $request->text;

        // TODO: Cek Redis Cache terlebih dahulu

        // TODO: Jika belum ada di cache, jalankan proses Stemming ECS
        // Contoh pemanggilan Service:
        // $hasilStem = $indoStemmer->process($text);

        // TODO: Query ke database berdasar hasil stem dan gabungkan.

        $data = [
            'original_text'   => $text,
            'translated_text' => '...proses belum diimplementasi...',
        ];

        return apiSuccess($data, 'Berhasil memproses terjemahan.');
    }

    /**
     * POST /api/v2/ai/ocr
     */
    public function ocr(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        // TODO: Forward file gambar ke URL API FastAPI Microservice
        // Menggunakan Laravel Http Client (Http::attach(...)->post(...))

        $data = [
            'extracted_text' => 'teks dari fastapi ocr...',
        ];

        return apiSuccess($data, 'Berhasil mengekstrak teks dari gambar.');
    }

    /**
     * POST /api/v2/ai/speech-to-text
     */
    public function speechToText(Request $request)
    {
        $request->validate([
            'audio'         => 'required|file|mimes:wav,webm,mp3|max:10240', // Max 10MB
            'expected_text' => 'nullable|string',
        ]);

        // TODO: Forward file audio ke URL API FastAPI Microservice Whisper

        $data = [
            'transcription'  => 'suara pengguna ke teks...',
            'accuracy_score' => 95.5,
        ];

        return apiSuccess($data, 'Berhasil mengevaluasi pelafalan audio.');
    }
}
