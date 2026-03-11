<?php
// app/Models/ArabicWord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabicWord extends Model
{
    protected $table = 'arabic_words';
    
    protected $fillable = [
        'surah_number',
        'ayah_number', 
        'word_number',
        'arabic_text'
    ];
    
    // Relationship with French translation words
    public function frenchTranslation()
    {
        return $this->hasOne(TranslationWord::class, 'surah_number', 'surah_number')
                    ->whereColumn('ayah_number', 'arabic_words.ayah_number')
                    ->whereColumn('word_number', 'arabic_words.word_number')
                    ->whereHas('translation', function($q) {
                        $q->where('language', 'French');
                    });
    }
    
    // Get full ayah text
    public static function getAyahText($surah, $ayah)
    {
        return self::where('surah_number', $surah)
                   ->where('ayah_number', $ayah)
                   ->orderBy('word_number')
                   ->pluck('arabic_text')
                   ->implode(' ');
    }
}