<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arabic_words', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->integer('word_number');
            $table->string('arabic_text', 500); 
            $table->timestamps();
            

            $table->index(['surah_number', 'ayah_number', 'word_number'], 'arabic_words_lookup_index');
            

            $table->unique(['surah_number', 'ayah_number', 'word_number'], 'arabic_words_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('arabic_words');
    }
};