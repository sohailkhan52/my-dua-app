<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Translation;
use App\Models\TranslationLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TranslationController extends Controller
{
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
            'file_name' => 'required|file|mimes:json',
        ],
            [
                'file_name.required' => 'Please upload a file.',
                'file_name.file'     => 'The uploaded item must be a valid file.',
                'file_name.mimes'    => 'Only JSON files are allowed.',
            ]
        );
        $file    = $request->file('file_name');
        $content = file_get_contents($file->getRealPath());

        // Check if JSON is valid
        json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['file' => 'The file must contain valid JSON.']);
        }
        $file      = $request->file('file_name');
        $file_name = $file->getClientOriginalName();

        // Store file in storage
        $path = $file->store('translations');
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

            // Build full path to stored JSON file
            $jsonPath = storage_path("app\private/$path");

            //first get contents then decoded it into an array
            $jsonData  = json_decode(file_get_contents($jsonPath), true);
            $count     = 0;
            $batch     = [];
            $batchSize = 1000;

            // Handle Verse-level translations (category_id = 1)
            if ($request->category_id == 1) {

                foreach ($jsonData as $key => $verse) {

                    // Parse key format: "surah:ayah"
                    $parts = explode(':', $key);
                    $surah = (int) $parts[0];
                    $ayah  = (int) $parts[1];
                    $verse = $verse['t'];
                    $data  = [
                        'surah' => $surah,
                        'ayah'  => $ayah,
                        'verse' => $verse,
                    ];

                    // Validate each record
                    $validator = Validator::make($data, [
                        'surah' => 'required|integer|min:1|max:114',
                        'ayah'  => 'required|integer|min:1',
                        'verse' => 'required|string',
                    ], [
                        // Custom messages
                        'verse.required' => 'verse required.',
                        'verse.integer'  => 'verse must be a string.',
                        'ayah.required'  => 'ayah number is required.',
                        'ayah.integer'   => 'ayah must be a number.',
                        'ayah.min'       => 'ayah number must be greater than 0.',
                        'surah.required' => 'Surah number is required.',
                        'surah.integer'  => 'Surah must be a number.',
                        'surah.min'      => 'Surah number must be greater than 0.',
                        'surah.max'      => 'Surah number must be less than or equal to 114.',
                    ]
                    );

                    if ($validator->fails()) {
                        return back()->withErrors($validator);
                    }

                    // Prepare batch  data in aligned manner that can be stored easily
                    $batch[] = [
                        'translation_id' => $translation->id,
                        'surah_number'   => $surah,
                        'ayah_number'    => $ayah,
                        'verse_text'     => $verse,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
                // Insert all verse records at once
                if (! empty($batch)) {
                    DB::table('translation_verses')->insert($batch);
                }

            }
            // Handle Word-by-word translations (category_id = 2)
            else {

                foreach ($jsonData as $key => $word) {

                    // Parse key format: "surah:ayah:wordNumber"
                    $parts      = explode(':', $key);
                    $surah      = (int) $parts[0];
                    $ayah       = (int) $parts[1];
                    $wordNumber = (int) $parts[2];
                    $data       = [
                        'surah'      => $surah,
                        'ayah'       => $ayah,
                        'wordNumber' => $wordNumber,
                        'word'       => $word,
                    ];
                    // Validate each record
                    $validator = Validator::make($data, [
                        'surah'      => 'required|integer|min:1|max:114',
                        'ayah'       => 'required|integer|min:1',
                        'wordNumber' => 'required|integer|min:1',
                        'word'       => 'required|string',
                    ], [
                        // Custom messages
                        'word.required'       => 'word required.',
                        'word.integer'        => 'word must be a string.',
                        'wordNumber.required' => 'wordNumber number is required.',
                        'wordNumber.integer'  => 'wordNumber must be a number.',
                        'wordNumber.min'      => 'wordNumber number must be greater than 0.',
                        'ayah.required'       => 'ayah number is required.',
                        'ayah.integer'        => 'ayah must be a number.',
                        'ayah.min'            => 'ayah number must be greater than 0.',
                        'surah.required'      => 'Surah number is required.',
                        'surah.integer'       => 'Surah must be a number.',
                        'surah.min'           => 'Surah number must be greater than 0.',
                        'surah.max'           => 'Surah number must be less than or equal to 114.',
                    ]
                    );

                    if ($validator->fails()) {
                        return back()->withErrors($validator);
                    }
                    // Prepare batch  data in aligned manner that can be stored easyly
                    $batch[] = [
                        'translation_id' => $translation->id,
                        'surah_number'   => $surah,
                        'ayah_number'    => $ayah,
                        'word_number'    => $wordNumber,
                        'word_text'      => $word,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                    // Insert in batches to avoid memory overflow
                    if (count($batch) >= $batchSize) {
                        DB::table('translation_words')->insert($batch);
                        $batch = [];
                    }
                }

                // Insert any remaining records
                if (! empty($batch)) {
                    DB::table('translation_words')->insert($batch);
                }

            }

            return redirect("/translation");
        } catch (\Throwable $th) {
            //throw $th;
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
        $translation = Translation::where("id", $request->id)->first();
        $translation = $translation->update(["translator_name" => ucwords($request->translator_name), "language" => $request->language, "category_id" => $request->category_id]);

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
