<?php
// app/Models/TranslationWord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationWord extends Model
{
    protected $table = 'translation_words';
    
    protected $fillable = [
        'translation_id',
        'surah_number',
        'ayah_number',
        'word_number',
        'word_text'
    ];
    
    // Relationships
    public function translation()
    {
        return $this->belongsTo(Translation::class);
    }
    
    // Helper method to get words for a specific ayah
    public static function getAyahWords($translationId, $surahNumber, $ayahNumber)
    {
        return self::where('translation_id', $translationId)
                   ->where('surah_number', $surahNumber)
                   ->where('ayah_number', $ayahNumber)
                   ->orderBy('word_number')
                   ->get();
    }
    
    // Helper method to get full ayah text (combine words)
    public static function getFullAyahText($translationId, $surahNumber, $ayahNumber)
    {
        $words = self::where('translation_id', $translationId)
                     ->where('surah_number', $surahNumber)
                     ->where('ayah_number', $ayahNumber)
                     ->orderBy('word_number')
                     ->pluck('word_text')
                     ->toArray();
        
        return implode(' ', $words);
    }
}