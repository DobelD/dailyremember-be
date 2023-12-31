<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\SpeakingController;
use App\Http\Controllers\FirebaseServiceController;
use App\Http\Controllers\ProgressVocabularyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('update/{id}', [AuthController::class, 'updateProfile']);
});


Route::resource('/word', WordController::class);
Route::resource('/speaking', SpeakingController::class);
Route::delete('speaking/{id}/{idTranscribe}', [SpeakingController::class, 'destroy']);
Route::put('transcribe/{id}/{idTranscribe}', [SpeakingController::class, 'addTranscribeToSpeaking']);
Route::resource('/progress-vocabulary', ProgressVocabularyController::class);

Route::post('/send-notification', [FirebaseServiceController::class, 'sendFirebaseNotification']);









