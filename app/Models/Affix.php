<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affix extends Model
{
    protected $fillable = ['bahasa', 'awalan', 'akhiran', 'letak', 'arti_awalan', 'arti_akhiran'];
}
