<?php
namespace App\Models;

use App\Models\LearningModule;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['module_id', 'question', 'options', 'correct_answer', 'type'];

    // Casting JSON options menjadi Array PHP secara otomatis
    protected $casts = [
        'options' => 'array',
    ];

    // Relasi balik ke LearningModule
    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class, 'module_id');
    }
}
