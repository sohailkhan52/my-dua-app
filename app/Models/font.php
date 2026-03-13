<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class font extends Model
{
    protected $fillable =[
        'font_name',
        'font_path',
        'original_filename',
        'file_extension',
        'file_size',
    ];

    protected static function booted()
    {
        static::deleting(function($font){
            if($font->file_path&& Storage::disk("public")->exists($font->file_path)){
                Storage::disk('public')->delete($font->file_path);
            }
        });
    }
}
