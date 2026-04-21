<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FontController;

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();
// Chat route (if you want a dedicated page)
Route::middleware(['auth'])->group(function () {
    Route::view('/chat', 'chat')->name('chat');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/translation', [TranslationController::class, 'index']);
Route::get('/translation/addtranslation', [TranslationController::class, 'addTranslation']);
Route::post('/translation/store', [TranslationController::class, 'store'])->name('translation.store');
Route::get('/translation/show/{id}', [HomeController::class, 'show']);
Route::get('/fontt/{id}', [FontController::class, 'changeFont']);
Route::get('/translation/edit/{id}', [TranslationController::class, 'editTranslation']);
Route::get('/translation/delete/{id}', [TranslationController::class, 'delete']);
Route::put('/translation/update/{id}', [TranslationController::class, 'update'])->name('translation.update');

Route::resource('fonts',FontController::class);
    Route::get('fonts/{font}/download', [FontController::class, 'download'])
        ->name('fonts.download');
    

Route::post('/surah', [ExportController::class, 'downloadSurah']);

});