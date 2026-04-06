<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Translation;
use App\Models\TranslationLanguage;
use App\Models\TranslationVerse;
use App\Models\TranslationWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TranslationController extends Controller
{

    /**
     * this function will help to both update and store function to store translation file in database 
     */
    private function processTranslationFile($translation, $file)
    {
        $content = file_get_contents($file->getRealPath());

        // Validate JSON
        json_decode($content);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON file');
        }

        $jsonData = json_decode($content, true);

        foreach ($jsonData as $key => $value) {
            if (! preg_match('/^\d+:\d+(:\d+)?$/', $key)) {
                throw new \Exception('Invalid file format');
            }
        }

        $batch     = [];
        $batchSize = 1000;

        // delete old data
        TranslationVerse::where('translation_id', $translation->id)->delete();
        TranslationWord::where('translation_id', $translation->id)->delete();

        if ($translation->category->slug == "ayah-by-ayah") {

            foreach ($jsonData as $key => $verse) {

                $parts = explode(':', $key);

                $batch[] = [
                    'translation_id' => $translation->id,
                    'surah_number'   => (int) $parts[0],
                    'ayah_number'    => (int) $parts[1],
                    'verse_text'     => $verse['t'],
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }

            if (! empty($batch)) {
                DB::table('translation_verses')->insert($batch);
            }

        } else {

            foreach ($jsonData as $key => $word) {

                if (! preg_match('/^\d+:\d+:\d+$/', $key)) {
                    throw new \Exception('Invalid word format');
                }

                $parts = explode(':', $key);

                $text = is_array($word) ? ($word['t'] ?? null) : $word;

                $batch[] = [
                    'translation_id' => $translation->id,
                    'surah_number'   => (int) $parts[0],
                    'ayah_number'    => (int) $parts[1],
                    'word_number'    => (int) $parts[2],
                    'word_text'      => $text,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('translation_words')->insert($batch);
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                DB::table('translation_words')->insert($batch);
            }
        }
    }
    /**
     * Display the main translations listing page where user can select the translation
     */
    public function index()
    {
        // Fetch all records for display
        $translations = Translation::orderBy("translator_name")->get();
        $categories   = Category::orderBy("name")->get();
        $languages    = TranslationLanguage::all();
        return view('translations.home', compact("translations", 'categories', 'languages'));
    }
    /**
     * Show the form for creating a new translation
     */
    public function addTranslation()
    {
        $categories = Category::all();
        $languages  = TranslationLanguage::all();
        $flag       = 1; // Flag indicates creation mode (1 = create, 0 = edit)
        return view("translations.create", compact('categories', 'flag', 'languages'));
    }
    /**
     * Store a newly created translation from JSON file
     */
    public function store(Request $request)
    {
        // Validate that file exists and is valid JSON
        $request->validate([
            'file_name' => 'required|file|mimetypes:application/json,text/plain',
        ],
            [
                'file_name.required'  => 'Please upload a file.',
                'file_name.file'      => 'The uploaded item must be a valid file.',
                'file_name.mimetypes' => 'Only JSON files are allowed.',
            ]
        );
        $file    = $request->file('file_name');
        $content = file_get_contents($file->getRealPath());

        // Check if JSON is valid
        json_decode($content);


        $file      = $request->file('file_name');
        $file_name = $file->getClientOriginalName();

        // Store file in storage
        $path = $file->store('translations');
        // Build full path to stored JSON file
        $jsonPath = storage_path("app\private/$path");

        //first get contents then decoded it into an array
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        //the below condition will check the file structure on the bases of ayah and surah or word
        foreach ($jsonData as $key => $value) {
            if (! preg_match('/^\d+:\d+(:\d+)?$/', $key)) {
                return back()->withErrors([
                    'file' => "Invalid file format:",
                ]);
            }
        }
        $count     = 0;
        $batch     = [];
        $batchSize = 1000;
        try {
            // Create translation record in database
            $translation = Translation::create([
                "translator_name" => ucwords($request->translator_name),
                "language_id"     => $request->language_id,
                "category_id"     => $request->category_id,
                "file_name"       => $file->getClientOriginalName(),
                "file_path"       => $path,
                "file_size"       => $file->getSize(),
                "created_at"      => now(),
                "updated_at"      => now(),
            ]);

            if (! $translation) {
                return " translation language not found!";
            }
// the below like is call the processTranslation File by passing the params
            $this->processTranslationFile($translation, $file);

            return redirect("/translation");
        } catch (\Throwable $th) {
            return back()->withErrors(['error' => $th->getMessage()]);
        }
    }
    /**
     * Show the edit form for a translation
     */
    public function editTranslation(Request $request)
    {
        $translation = Translation::where("id", $request->id)->first();
        $categories  = Category::all();
        $languages   = TranslationLanguage::all();
        $flag        = 0; // Flag indicates edit mode (0 = edit, 1 = create)

        return view("translations.create", compact("translation", 'categories', 'flag', 'languages'));
    }
    /**
     * Update an existing translation record
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // these lines of code will will get the translation according to  the requested id
        $translation = Translation::where("id", $request->id)->first();

        // this condition will check if that the changing categroy should have file to replace
        if ($translation->category_id != $request->category_id && ! $request->hasFile('file_name')) {
            return back()->withErrors([
                'file' => 'Please upload file too when category changes.',
            ]);
        }

        if ($translation) {

            try {
                // these lines of code will update the name, language and category of the translation
                $translation->update(["translator_name" => ucwords($request->translator_name), "language_id" => $request->language_id, "category_id" => $request->category_id, "updated_at" => now()]);

                // if request has file the these lines will adjust them
                if ($request->hasFile('file_name')) {
                    // these lines will validate the files
                    $request->validate([
                        'file_name' => 'file|mimetypes:application/json,text/plain',
                    ],
                        [
                            'file_name.file'      => 'The uploaded item must be a valid file.',
                            'file_name.mimetypes' => 'Only JSON files are allowed.',
                        ]
                    );

                    $file    = $request->file('file_name');
                    $content = file_get_contents($file->getRealPath());

                    // Check if JSON is valid
                    json_decode($content);
                    $file      = $request->file('file_name');
                    $file_name = $file->getClientOriginalName();

                    // Store file in storage
                    $path = $file->store('translations');
                    // Build full path to stored JSON file
                    $jsonPath = storage_path("app\private/$path");

                    //first get contents then decoded it into an array
                    $jsonData = json_decode(file_get_contents($jsonPath), true);

                    //the below condition will check the file structure on the bases of ayah and surah or word
                    foreach ($jsonData as $key => $value) {
                        if (! preg_match('/^\d+:\d+(:\d+)?$/', $key)) {
                            return back()->withErrors([
                                'file' => "Invalid file format:",
                            ]);
                        }
                    }
                    $count     = 0;
                    $batch     = [];
                    $batchSize = 1000;

                    Storage::delete($translation->file_path); // Delete file from storage

                    // the below like is call the processTranslation File by passing the params
                    $this->processTranslationFile($translation, $file);

                    // these lines of code will update the file_name, file_path and file_size of the translation
                    $translation->update([
                        "file_name" => $file->getClientOriginalName(),
                        "file_path" => $path,
                        "file_size" => $file->getSize(),
                    ]);

                }
            } catch (\Throwable $th) {
                return back()->withErrors(['error' => $th->getMessage()]);
            }

        }
        return redirect("/translation");

    }
    /**
     * Delete a translation and its associated file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {

        $translation = Translation::where("id", $request->id)->first();
        if ($translation) {
            Storage::delete($translation->file_path); // Delete file from storage
            $translation = $translation->delete();    // Delete database record
        }

        return redirect("/translation");
    }

}
