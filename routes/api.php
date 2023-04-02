<?php

use App\Http\Controllers\Api\V1\AlphabetController;
use App\Http\Controllers\Api\V1\QuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/alphabet', [AlphabetController::class, 'index'])->name('api.alphabet');
    Route::get('/questions', [QuestionController::class, 'index'])->name('api.questions');
    Route::get('/modes/unlimited', [QuestionController::class, 'unlimited'])->name('api.modes.unlimited');
    Route::post('/modes/custom', [QuestionController::class, 'custom_store'])->name('api.modes.custom_store');
    Route::get('/modes/custom', [QuestionController::class, 'custom_get'])->name('api.modes.custom_get');
    Route::get('/rooms', [QuestionController::class, 'rooms'])->name('api.rooms');
});
