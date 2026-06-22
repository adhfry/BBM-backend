<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Syllable;
use App\Models\Vocabulary;
use App\Services\Linguistics\FsaSyllableService;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    /**
     * GET /api/v2/dictionary
     */
    public function search(Request $request)
    {
        $query = Vocabulary::query();

        if ($request->has('search')) {
            $keyword = $request->search;
            $query->where('kata_indo', 'LIKE', "%{$keyword}%")
                ->orWhere('kata_madura', 'LIKE', "%{$keyword}%");
        }

        // Pagination 20 item per halaman
        $results = $query->paginate(20);

        return apiSuccess($results, 'Berhasil mencari kata di kamus.');
    }

    /**
     * GET /api/v2/dictionary/syllables
     */
    public function getSyllables()
    {
        $syllables = Syllable::all();
        return apiSuccess($syllables, 'Berhasil mengambil daftar suku kata.');
    }

    /**
     * POST /api/v2/dictionary/syllabify
     * Menggunakan algoritma FSA (Finite State Automata)
     */
    public function syllabify(Request $request, FsaSyllableService $fsaService)
    {
        $request->validate([
            'kata' => 'required|string|max:100',
        ]);

        // Memproses pemenggalan suku kata dari service yang sudah kita buat
        $hasilSyllabify = $fsaService->process($request->kata);

        $data = [
            'kata_asli'         => $request->kata,
            'hasil_pemenggalan' => $hasilSyllabify,
        ];

        return apiSuccess($data, 'Berhasil memenggal suku kata.');
    }
}
