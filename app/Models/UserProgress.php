<?php
namespace App\Models;

use App\Models\LearningModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    protected $fillable = ['user_id', 'module_id', 'score', 'is_completed', 'last_accessed'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function learningModule()
    {
        return $this->belongsTo(LearningModule::class, 'module_id');
    }
}
