<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\TranslationController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Auth::routes(['register' => false]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/translation', [App\Http\Controllers\TranslationController::class, 'index']);
Route::get('/translation/addtranslation', [App\Http\Controllers\TranslationController::class, 'addTranslation']);
Route::post('/translation/store', [App\Http\Controllers\TranslationController::class, 'store'])->name('translation.store');
Route::get('/translation/show/{id}', [App\Http\Controllers\HomeController::class, 'show']);
Route::get('/translation/edit/{id}', [App\Http\Controllers\TranslationController::class, 'editTranslation']);
Route::get('/translation/delete/{id}', [App\Http\Controllers\TranslationController::class, 'delete']);
Route::put('/translation/update/{id}', [App\Http\Controllers\TranslationController::class, 'update'])->name('translation.update');

    


 