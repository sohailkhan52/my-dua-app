<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArabicVerse extends Model
{
    use HasFactory;

    protected $table = 'arabic_verses';

    protected $fillable = [
        'surah_number',
        'ayah_number',
        'verse_key',
        'arabic_text',
    ];

}