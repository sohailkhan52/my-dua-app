<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\translationWord;
use App\Models\ArabicWord;
use App\Models\Category;
use App\Models\TranslationLanguage;
use App\Models\translationVerse;
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

    public function store(Request $request){

    
    $file_name=$request->file_name;
    $file=$request->file('file_name');
    $path = $file->storeAs('translations', $request->file_name);

        $translation=Translation::create(["translator_name"=>$request->translator_name,"language_id"=>$request->language_id,"category_id"=>$request->category_id,'file_name'=>$file_name,'file_path'=>$path,'file_size'=>filesize("$request->file_name"),"created_at"=>now(),'updated_at'=>now()]);
        return redirect("/translation");
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
        $translation=Translation::where("id",$request->id);
        $translation=$translation->delete();
    
    
        return redirect("/translation");
    }


}