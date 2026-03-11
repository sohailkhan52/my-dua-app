<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translation_words', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('translation_id');
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->integer('word_number');
            $table->text('word_text'); 
            $table->timestamps();
            
            
            $table->foreign('translation_id')
                  ->references('id')
                  ->on('translations')
                  ->onDelete('cascade');
            
            
            $table->index(['surah_number', 'ayah_number']);
            $table->index('word_number');
            $table->index(['translation_id', 'surah_number', 'ayah_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('translation_words');
    }
};