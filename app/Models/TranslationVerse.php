<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationVerse extends Model
{
    use HasFactory;

    protected $table = 'translation_verses';

    protected $fillable = [
        'translation_id',
        'surah_number',
        'ayah_number',
        'verse_text',
    ];

    /**
     * Relationship: A verse belongs to a translation
     */
    public function translation()
    {
        return $this->belongsTo(Translation::class);
    }
}