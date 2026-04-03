<?php
namespace App\Http\Controllers;

use App\Models\ArabicVerse;
use App\Models\ArabicWord;
use App\Models\Font;
use App\Models\translation;
use App\Models\translationVerse;
use App\Models\translationWord;
use Illuminate\Http\Request;

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

    /**
     * Display translation with associated Arabic text and fonts
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        // Number of records per page for pagination
        $perPage = 16;
        // Fetch the main translation record
        $mainTranslation = Translation::where('id', $request->id)->first();
        // Check if translation is word-by-word (category_id = 2) or verse-by-verse
        if ($mainTranslation->category->slug=="word-by-word") {
            // WORD-BY-WORD TRANSLATION
            // Fetch paginated word translations
            $translation = TranslationWord::where('translation_id', $request->id)
                ->paginate($perPage);

            // Fetch corresponding Arabic words based on surah and ayah numbers from translation
            $arabic = ArabicWord::whereIn('surah_number', $translation->pluck('surah_number')->unique())
                ->whereIn('ayah_number', $translation->pluck('ayah_number')->unique())
                ->orderBy('surah_number')
                ->orderBy('ayah_number')
                ->orderBy('word_number')
                ->get();
            $flag = 1;//this flag will help to identify the category type
        } elseif($mainTranslation->category->slug=="ayah-by-ayah") {
            // VERSE-BY-VERSE TRANSLATION
            // Fetch paginated verse translations
            $translation = TranslationVerse::where('translation_id', $request->id)
                ->paginate($perPage);

            // Fetch corresponding Arabic verses based on surah and ayah numbers from translation
            $arabic = ArabicVerse::whereIn('surah_number', $translation->pluck('surah_number')->unique())
                ->whereIn('ayah_number', $translation->pluck('ayah_number')->unique())
                ->get();
            $flag = 0;//this flag will help to identify the category type
        }

        // Fetch all available fonts from database
        $fonts = Font::orderBy("font_name")->get();

        // Set default font - use session value if exists, otherwise fallback to font ID 3
        $defaultFont = session('default_font_id') ?? 3;

        // Create array of surah numbers (1 to 114) for surah-wise download functionality
        $surahs = range(1, 114);

        return view('translations.index', compact("surahs", "translation", 'arabic', 'defaultFont', 'flag', 'fonts', 'mainTranslation'));
    }

}
