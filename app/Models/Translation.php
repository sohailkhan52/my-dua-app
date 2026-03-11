<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'translator_name',
        'category_id',
        'language_id',
        'file_name',
        'file_path',
        'file_size'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function language(){
        return $this->belongsTo(TranslationLanguage::class);
    }
}