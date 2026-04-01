<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\translationWord;
use App\Models\ArabicWord;
use App\Models\Category;
use App\Models\Font;
use App\Models\TranslationLanguage;
use App\Models\translationVerse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\ArabicVerse;
class TranslationController extends Controller
{


    public function index(){
        $translations=Translation::all();
        $categories=Category::all();
        $languages=TranslationLanguage::all();
        return view('translations.home',compact("translations",'categories','languages'));
    }


    public function addTranslation(){
        $categories=Category::all();
        $languages=TranslationLanguage::all();
        $flag=1;
        return view("translations.create",compact('categories','flag','languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
         'file_name' => 'required|file|mimes:json'
        ]);
         $file = $request->file('file_name');
         $content = file_get_contents($file->getRealPath());

         json_decode($content);
     
        if (json_last_error() !== JSON_ERROR_NONE) {
             return back()->withErrors(['file' => 'The file must contain valid JSON.']);
         }
        $file = $request->file('file_name');
        $file_name=$file->getClientOriginalName();

        $path = $file->store('translations');
        try {
        
        
       $translation= Translation::create([
            "translator_name" => $request->translator_name,
            "language_id" => $request->language_id,
            "category_id" => $request->category_id,
            "file_name" => $file->getClientOriginalName(),
            "file_path" => $path,
            "file_size" => $file->getSize(),
            "created_at" => now(),
            "updated_at" => now()
        ]);

            set_time_limit(600);    
            if (!$translation) {
                return " translation language not found!";
            }    
            $jsonPath = storage_path("app\private/$path"); 
            
            $jsonData = json_decode(file_get_contents($jsonPath), true);    
            $count = 0;
            $batch = [];
            $batchSize = 1000;  
              
        if($request->category_id==1){

            foreach($jsonData as $key => $word) {
                
                $parts = explode(':', $key);
                $surah = (int)$parts[0];
                $ayah = (int)$parts[1];
                $word=$word['t'];
                $data=[
                    'surah' => $surah,
                    'ayah' => $ayah,
                    'word' => $word,
                ];
                $validator = Validator::make($data, [
                    'surah' => 'required|integer|min:1|max:114',
                    'ayah'  => 'required|integer|min:1',
                    'word'  => 'required|string'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator);
                }
                $batch[] = [
                    'translation_id' => $translation->id,
                    'surah_number' => $surah,
                    'ayah_number' => $ayah,
                    'verse_text' => $word,
                    'created_at' => now(),
                    'updated_at' => now()
                ];        
                $count++;
        
                if (count($batch) >= $batchSize) {
                    DB::table('translation_verses')->insert($batch);
                    $batch = [];
                    flush();
                }
            }
            if (!empty($batch)) {
                DB::table('translation_verses')->insert($batch);
            }
    
     
        }else{

            foreach($jsonData as $key => $word) {
                
                $parts = explode(':', $key);
                $surah = (int)$parts[0];
                $ayah = (int)$parts[1];
                $wordNumber = (int)$parts[2];
                $data=[
                    'surah' => $surah,
                    'ayah' => $ayah,
                    'wordNumber' => $wordNumber,
                    'word' => $word,
                ];
                $validator = Validator::make($data, [
                    'surah' => 'required|integer|min:1|max:114',
                    'ayah'  => 'required|integer|min:1',
                    'wordNumber'  => 'required|integer|min:1',
                    'word'  => 'required|string'
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator);
                }
                $batch[] = [
                    'translation_id' => $translation->id,
                    'surah_number' => $surah,
                    'ayah_number' => $ayah,
                    'word_number' => $wordNumber, 
                    'word_text' => $word,
                    'created_at' => now(),
                    'updated_at' => now()
                ];        
                $count++;
        
                if (count($batch) >= $batchSize) {
                    DB::table('translation_words')->insert($batch);
                    $batch = [];
                    flush();
                }
            }
    
            if (!empty($batch)) {
                DB::table('translation_words')->insert($batch);
            }
    
     
        }

        return redirect("/translation");
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function editTranslation(Request $request)
    {
        $translation=Translation::where("id",$request->id)->first();
        $categories=Category::all();        
        $languages=TranslationLanguage::all();
        $flag=0;
    
        return view("translations.create",compact("translation",'categories','flag','languages'));
    }
    public function update(Request $request)
    {
        $translation=Translation::where("id",$request->id)->first();
        $translation=$translation->update(["translator_name"=>$request->translator_name,"language"=>$request->language,"category_id"=>$request->category_id]);
    
    
        return redirect("/translation");
    }
    public function delete(Request $request)
    {


        $translation=Translation::where("id",$request->id)->first();
        if($translation){
        Storage::delete($translation->file_path);        
        $translation=$translation->delete();    
        }
    
        return redirect("/translation");
    }


    public function getFonts()
    {

        return view('translation.index',compact('fonts','defaultFont'));
    }

}