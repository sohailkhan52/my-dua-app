<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translator_name');
            $table->string('category_id');
            $table->string('language_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
};