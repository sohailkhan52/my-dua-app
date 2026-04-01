<?php

namespace App\Http\Controllers;

use App\Models\Font;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FontController extends Controller
{
    //ALLOWED FONT FORMATS
    protected $allowedExtensions=['ttf','otf','woff','woff2'];

    protected $maxFileSize=10240;

    public function index()
    {
        $fonts=Font::all();
        return view("fonts.index",compact('fonts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view("fonts.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'font_name'=>'required|string|max:255',
            'font_file'=>'required|file|max:'.$this->maxFileSize,
        ]);
        $file =$request->file('font_file');
        $extension=strtolower($file->getClientOriginalExtension());
        if(!in_array($extension,$this->allowedExtensions)){
            return back()->witherrors(['font_file'=>"Only TTF,OTF,WOFF,and WOFF2 files are allowed."])->withInput();
        }
        $originalName=pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
        $safeName=Str::slug($request->font_name)?:'font';
        $fileName=time().'_'.$safeName.'_'.uniqid().'.'.$extension;
        $path=$file->storeAs('fonts',$fileName,'public');
        
        Font::create([
            'font_name'=>$request->font_name,
            'font_path'=>$path,
            'original_filename'=>$file->getClientOriginalName(),
            'file_extension'=>$extension,
            'file_size'=>$file->getSize(),
        ]);
        return redirect()->route('fonts.index')
            ->with('success', 'Font uploaded successfully.');

        
    } 

    /**
     * Display the specified resource.
     */
    public function changeFont(int $id)
    {
        $font = Font::find($id);
        if ($font) {
            
            session(['default_font_id' => $font->id]);

        }
        return back()->with('success', 'Font changed successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $font=Font::find($id);
      return view('fonts.edit',compact('font'))  ; 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Font $font)
    {
        $request->validate([
            'font_name'=>'required|string|max:255',
            'font_file'=>'nullable|file|max:'.$this->maxFileSize,
        ]);

        $data = ['font_name'=>$request->font_name];

        if($request->hasFile('font_file')){
            $file =$request->file('font_file');
            $extension = strtolower($file->getClientOriginalExtension());

            if(!in_array($extension,$this->allowedExtensions))
                {
                    return back()->withErrors(['font_file'=>'Only TTF, OTF, WOFF, and WOFF2 files are allowed.'])->withInput();
                }

                if($font->font_path&& Storage::disk('public')->exists($font->font_path)){
                    Storage::disk('public')->delete($font->font_path);
                }

                $safeName=Str::slug($request->font_name)?:'font';
                $fileName=time().'_'.$safeName.uniqid().'.'.$extension;
                $path=$file->storeAs('fonts',$fileName,'public');

                $data['file_path']=$path;
                $data['original_filename']=$file->getClientOriginalName();
                $data['file_extension']=$extension;
                $data['file_size']=$file->getSize();
        }

        $font->update($data);
        return redirect()->route('fonts.index')
            ->with('success', 'Font updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Font $font)
    {
        if($font->font_path  && Storage::disk('public')->exists($font->font_path)){
             Storage::disk('public')->delete($font->font_path);
            }
        $font->delete();

        return redirect()->route('fonts.index')
            ->with('success', 'Font deleted successfully.');        
    }

public function download(Font $font)
{
    if(!$font->font_path){
        abort(404,'File path not found');
    }

    if(Storage::disk('public')->exists($font->font_path)){
        return Storage::disk('public')->download(
            $font->font_path,
            $font->original_filename
        );
    }

    abort(404,'File not found');
}

public function saveFontSelection(Request $request)
{
    $request->validate([
        'font_id' => 'nullable|exists:fonts,id'
    ]);
    
    session(['selected_font' => $request->font_id]);
    
    return response()->json(['success' => true]);
}
}
