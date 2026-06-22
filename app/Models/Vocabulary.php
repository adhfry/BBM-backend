<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    protected $fillable = ['kata_indo', 'kata_madura', 'tingkatan', 'kategori', 'audio_path', 'contoh_kalimat'];
}
