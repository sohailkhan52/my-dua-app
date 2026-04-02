<?php
namespace App\Http\Controllers;

use App\Models\ArabicVerse;
use App\Models\ArabicWord;
use App\Models\Translation;
use App\Models\TranslationVerse;
use App\Models\TranslationWord;
use Illuminate\Http\Request;

class ExportController extends Controller
{

    /**
     * Combine translation and Arabic data into a single array
     *
     * @param Collection $translations  // Translation records (either verse or word level)
     * @param Collection $arabic        // Arabic text records (verse or word level)
     * @param int $flag                 // 1 = word level, 0 = verse level
     * @return array                    // Combined data array
     */
    private function combineTranslationData($translations, $arabic, $flag)
    {
        // Verse by verse combination (flag = 0)
        $combined = [];

        if ($flag != 1) {
            // Verse level combination
            foreach ($translations as $tran) {
                // Find matching Arabic text for this verse
                $arabicText = $arabic
                    ->where('surah_number', $tran->surah_number)
                    ->where('ayah_number', $tran->ayah_number)
                    ->first();

                $combined[] = [
                    'surah_number'     => $tran->surah_number,
                    'ayah_number'      => $tran->ayah_number,
                    'translation_text' => $tran->verse_text,
                    'arabic_text'      => $arabicText->arabic_text ?? '',
                    'translation_id'   => $tran->translation_id,
                    'verse_id'         => $tran->id,
                ];
            }
        } else {
            // Word by word combination (flag = 1)
            foreach ($translations as $tran) {
                // Find matching Arabic text for this word (by surah, ayah, AND word number)
                $arabicText = $arabic
                    ->where('surah_number', $tran->surah_number)
                    ->where('ayah_number', $tran->ayah_number)
                    ->where('word_number', $tran->word_number)
                    ->first();

                $combined[] = [
                    'surah_number'   => $tran->surah_number,
                    'ayah_number'    => $tran->ayah_number,
                    'word_number'    => $tran->word_number,
                    'word_text'      => $tran->word_text,
                    'arabic_text'    => $arabicText->arabic_text ?? '',
                    'translation_id' => $tran->translation_id,
                    'word_id'        => $tran->id,
                ];
            }
        }

        return $combined;
    }

    /**
     * Download translation data for a specific surah as JSON
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadSurah(Request $request)
    {
        // Get parameters from request
        $translationId = $request->translationId;
        $surah_no      = $request->get('surah_no', 1);

        // Find the main translation record
        $mainTranslation = Translation::find($translationId);

        // Handle Word-by-word translation (category_id = 2)
        if ($mainTranslation->category_id == 2) {
            // Get word-by-word translations for this surah only
            $translation = TranslationWord::where('translation_id', $translationId)
                ->where('surah_number', $surah_no)
                ->orderBy('surah_number')
                ->orderBy('ayah_number')
                ->orderBy('word_number')
                ->get();

            // Get corresponding Arabic words for this surah
            $arabic = ArabicWord::where('surah_number', $surah_no)
                ->orderBy('surah_number')
                ->orderBy('ayah_number')
                ->orderBy('word_number')
                ->get();
            $flag = 1;
        }
        // Handle Verse-by-verse translation (category_id = 1)
        else {
            // Get verse-by-verse translations for this surah only
            $translation = TranslationVerse::where('translation_id', $translationId)
                ->where('surah_number', $surah_no)
                ->orderBy('surah_number')
                ->orderBy('ayah_number')
                ->get();

            // Get corresponding Arabic verses for this surah
            $arabic = ArabicVerse::where('surah_number', $surah_no)
                ->orderBy('surah_number')
                ->orderBy('ayah_number')
                ->get();
            $flag = 0; // Verse by verse flag
        }
        // Combine translation and Arabic data into single array
        $exportData = $this->combineTranslationData($translation, $arabic, $flag);

        // Generate filename: translation-name-surahs-{surah}-{date}.json
        $filename = sprintf('translation-%s-surahs-%d-to%d.json', $mainTranslation->translator_name ?? 'unknown', $surah_no, date('Y-m-d'));

        // Stream the JSON file as download (memory efficient for large datasets)
        return response()->streamDownload(function () use ($exportData) {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);;
        }, $filename);
    }
}
