<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningModule;
use App\Models\Quiz;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    /**
     * GET /api/v2/learning/modules
     */
    public function getModules()
    {
        $modules = LearningModule::orderBy('order_index', 'asc')->get();
        return apiSuccess($modules, 'Berhasil mengambil daftar modul belajar.');
    }

    /**
     * GET /api/v2/learning/modules/{id}
     */
    public function getModuleDetails($id)
    {
        $module = LearningModule::findOrFail($id);
        return apiSuccess($module, 'Berhasil mengambil detail modul.');
    }

    /**
     * GET /api/v2/learning/modules/{id}/quizzes
     */
    public function getModuleQuizzes($id)
    {
        // Hanya ambil soal dan pilihan ganda, jangan kirim kunci jawaban ke frontend!
        $quizzes = Quiz::where('module_id', $id)->get(['id', 'module_id', 'question', 'options', 'type']);
        return apiSuccess($quizzes, 'Berhasil mengambil kuis modul.');
    }

    /**
     * POST /api/v2/learning/quizzes/submit
     */
    public function submitQuiz(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:learning_modules,id',
            'answers'   => 'required|array', // ['quiz_id' => 'jawaban_user']
        ]);

        $userId = $request->user()->id;

        // TODO: Hitung skor berdasarkan pencocokan array $validated['answers'] dengan $quiz->correct_answer
        $score = 0;

        // Simpan progress
        $progress = UserProgress::updateOrCreate(
            ['user_id' => $userId, 'module_id' => $validated['module_id']],
            ['score' => $score, 'is_completed' => true, 'last_accessed' => now()]
        );

        return apiSuccess(['score' => $score], 'Kuis berhasil disubmit!');
    }

    /**
     * GET /api/v2/learning/progress
     */
    public function getProgress(Request $request)
    {
        $progress = UserProgress::with('learningModule')
            ->where('user_id', $request->user()->id)
            ->get();

        return apiSuccess($progress, 'Berhasil mengambil riwayat progres belajar.');
    }
}
