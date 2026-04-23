<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FontController;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    return view('welcome');
});


// ----STRIPE ROUTES--------- -->
// Any active subscription required 
Route::get('/dashboard',function (){ return view('dashboard');})->middleware('subscribed');
Route::get('/premium-feature',function (){ return view('premium');})->middleware('subscribed:premium');


//public pricing page
Route::get('/pricing',[SubscriptionController::class,"index"])->name("pricing");

// Subscription management (requires authentication)
Route::middleware(['auth'])->group(function (){
    // checkout 
    Route::post('/subscription/checkout',[SubscriptionController::class,"checkout"])->name('subscription.checkout');
    Route::get('/subscription/success',[SubscriptionController::class,"success"])->name('subscription.success');
    Route::get('/subscription/cancel',[SubscriptionController::class,"cancel"])->name('subscription.cancel');

    //plan changes
    Route::put('/subscription/plan',[SubscriptionController::class,'changePlan'])->name('subscription.change-plan');

    //cancel and resourcebundle_get_error_message
    Route::delete('/subscription',[SubscriptionController::class,'cancelSubscription'])->name('subsciption.cancel');
    Route::delete('/subscription/resume',[SubscriptionController::class,'resumeSubscription'])->name('subsciption.resume');
});
// <--STRIPE ROUTES-----------



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