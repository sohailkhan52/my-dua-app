<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arabic_verses', function (Blueprint $table) {
            $table->id();
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->string('verse_key', 10); 
            $table->text('arabic_text');
            $table->timestamps();
            
            // Indexes
            $table->index(['surah_number', 'ayah_number'], 'arabic_verses_lookup_index');
            $table->unique(['surah_number', 'ayah_number'], 'arabic_verses_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('arabic_verses');
    }
};