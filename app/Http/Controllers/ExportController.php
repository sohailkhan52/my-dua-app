<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\TranslationWord;
use App\Models\TranslationVerse;
use App\Models\ArabicWord;
use App\Models\ArabicVerse;
use App\Models\Font;

class ExportController extends Controller
{
    
    /**
     * Combine translation and Arabic data
     */
    private function combineTranslationData($translations, $arabic, $flag)
    {
        $combined = [];
        
        if ($flag != 1) {
            // Verse level combination
            foreach ($translations as $tran) {
                $arabicText = $arabic
                    ->where('surah_number', $tran->surah_number)
                    ->where('ayah_number', $tran->ayah_number)
                    ->first();
                
                $combined[] = [
                    'surah_number' => $tran->surah_number,
                    'ayah_number' => $tran->ayah_number,
                    'translation_text' => $tran->verse_text,
                    'arabic_text' => $arabicText->arabic_text ?? '',
                    'translation_id' => $tran->translation_id,
                    'verse_id' => $tran->id
                ];
            }
        } else {
            // Word level combination
            foreach ($translations as $tran) {
                $arabicText = $arabic
                    ->where('surah_number', $tran->surah_number)
                    ->where('ayah_number', $tran->ayah_number)
                    ->where('word_number', $tran->word_number)
                    ->first();
                
                $combined[] = [
                    'surah_number' => $tran->surah_number,
                    'ayah_number' => $tran->ayah_number,
                    'word_number' => $tran->word_number,
                    'word_text' => $tran->word_text,
                    'arabic_text' => $arabicText->arabic_text ?? '',
                    'translation_id' => $tran->translation_id,
                    'word_id' => $tran->id
                ];
            }
        }
        
        return $combined;
    }
    
    
    /**
     * Download specific surah range from translation
     */
    public function downloadSurah(Request $request)
    {

        $translationId = $request->translationId;
        $surah_no = $request->get('surah_no', 1);
       
        
        $mainTranslation = Translation::find($translationId);

        if ($mainTranslation->category_id == 2) {
            $translation = TranslationWord::where('translation_id', $translationId)
                           ->where('surah_number',$surah_no)
                           ->orderBy('surah_number')
                           ->orderBy('ayah_number')
                           ->orderBy('word_number')
                           ->get();
            
            $arabic = ArabicWord::where('surah_number',$surah_no)
                       ->orderBy('surah_number')
                       ->orderBy('ayah_number')
                       ->orderBy('word_number')
                       ->get();
            $flag = 1;
        } else {
            $translation = TranslationVerse::where('translation_id', $translationId)
                           ->where('surah_number',$surah_no)
                           ->orderBy('surah_number')
                           ->orderBy('ayah_number')
                           ->get();
            
            $arabic = ArabicVerse::where('surah_number',$surah_no)
                       ->orderBy('surah_number')
                       ->orderBy('ayah_number')
                       ->get();
            $flag = 0;
        }
        $exportData = $this->combineTranslationData($translation, $arabic, $flag);
        // dd($exportData);
    }
}