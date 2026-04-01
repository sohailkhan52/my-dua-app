<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\TranslationWord;
use App\Models\ArabicWord;
use App\Models\Category;
use App\Models\Font;
use App\Models\TranslationVerse;
use App\Models\ArabicVerse;
use Barryvdh\Snappy\Facades\SnappyPdf;;

class PDFController extends Controller
{

public function download(Request $request)
{
    $perPage = 16;
    $currentPage = $request->query('page', 1);

    $mainTranslation = Translation::findOrFail($request->id);

    if ($mainTranslation->category_id == 2) {
        $query = TranslationWord::with('translation.language')
            ->where('translation_id', $request->id);

        $translation = $query->paginate($perPage, ['*'], 'page', $currentPage);

        $arabic = ArabicWord::whereIn('surah_number', $translation->pluck('surah_number')->unique())
            ->whereIn('ayah_number', $translation->pluck('ayah_number')->unique())
            ->get();

        $flag = 1;
    } else {
        $query = TranslationVerse::with('translation.language')
            ->where('translation_id', $request->id);

        $translation = $query->paginate($perPage, ['*'], 'page', $currentPage);

        $arabic = ArabicVerse::whereIn('surah_number', $translation->pluck('surah_number')->unique())
            ->whereIn('ayah_number', $translation->pluck('ayah_number')->unique())
            ->get();

        $flag = 0;
    }

    $html = view('translations.download', compact('translation','arabic','flag','mainTranslation'))->render();

    return SnappyPdf::loadHTML($html)
        ->setPaper('a4')
        ->setOrientation('landscape')
        ->setOption('enable-local-file-access', true)
        ->download("translation_page_{$currentPage}.pdf");
}
}