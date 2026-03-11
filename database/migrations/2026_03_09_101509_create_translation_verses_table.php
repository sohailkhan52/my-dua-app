<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translation_verses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('translation_id');
            $table->integer('surah_number');
            $table->integer('ayah_number');
            $table->text('verse_text');
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('translation_id')
                  ->references('id')
                  ->on('translations')
                  ->onDelete('cascade');
                  
            // Indexes for faster queries
            $table->index('translation_id');
            $table->index(['surah_number', 'ayah_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translation_verses');
    }
};