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
        $flag=1;
        return view("fonts.create",compact("flag"));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
