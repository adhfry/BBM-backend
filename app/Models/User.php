<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\LearningModule;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    // Relasi ke UserProgress (Riwayat Nilai)
    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'user_id');
    }

    // Relasi Banyak-ke-Banyak ke LearningModule langsung
    public function learningModules()
    {
        return $this->belongsToMany(LearningModule::class, 'user_progress', 'user_id', 'module_id')
            ->withPivot('score', 'is_completed', 'last_accessed')
            ->withTimestamps();
    }
}
