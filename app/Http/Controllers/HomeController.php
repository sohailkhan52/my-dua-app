<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\translation;
use App\Models\translationWord;
use App\Models\ArabicWord;
use App\Models\Category;
use App\Models\Font;
use App\Models\translationVerse;
use App\Models\ArabicVerse;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

   public function show(Request $request){
    $perPage = 16;
    $mainTranslation=Translation::where('id', $request->id)->first();
    if($mainTranslation->category_id==2){
        
        $translation = TranslationWord::where('translation_id', $request->id)
                       ->paginate($perPage);
        
        
        $arabic = ArabicWord::whereIn('surah_number', $translation->pluck('surah_number')->unique())
                   ->whereIn('ayah_number', $translation->pluck('ayah_number')->unique())
                   ->orderBy('surah_number')
                   ->orderBy('ayah_number')
                   ->orderBy('word_number')
                   ->get();
        $flag = 1;
    } else {
        $translation = TranslationVerse::where('translation_id', $request->id)
                       ->paginate($perPage);
        
        $arabic = ArabicVerse::whereIn('surah_number', $translation->pluck('surah_number')->unique())
                   ->whereIn('ayah_number', $translation->pluck('ayah_number')->unique())
                   ->get();
        $flag = 0;
    }
    $fonts=Font::all();
    // dd(session('default_font_id'));
        $defaultFont= 3;
       


    return view('translations.index', compact("translation", 'arabic','defaultFont','flag','fonts','mainTranslation'));
}


}
