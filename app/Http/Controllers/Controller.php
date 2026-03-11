<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
    /**
    * this function will store the word by word file in database
    set_time_limit(600);    
    $translation = DB::table('translations')->where('language', 'French')->first();    
    if (!$translation) {
        return "French translation not found!";
    }    
    $jsonPath = storage_path('app/translations/french_words.json'); // Adjust filename as needed
    $jsonData = json_decode(file_get_contents($jsonPath), true);    
    $count = 0;
    $batch = [];
    $batchSize = 1000;    
    foreach($jsonData as $key => $word) {
        // Parse the key format "surah:ayah:word_number"
        $parts = explode(':', $key);
        $surah = (int)$parts[0];
        $ayah = (int)$parts[1];
        $wordNumber = (int)$parts[2];
        
        $batch[] = [
            'translation_id' => $translation->id,
            'surah_number' => $surah,
            'ayah_number' => $ayah,
            'word_number' => $wordNumber, // You'll need this column in your table
            'verse_text' => $word, // This contains the HTML spans
            'created_at' => now(),
            'updated_at' => now()
        ];        
        $count++;
        
        if (count($batch) >= $batchSize) {
            DB::table('translation_verses')->insert($batch);
            $batch = [];
            echo "Inserted $count French words...<br>";
            flush(); // Force output to browser
        }
    }
    
    if (!empty($batch)) {
        DB::table('translation_verses')->insert($batch);
    }
    
    return "✅ Success! Imported $count French word-by-word translations."; */
