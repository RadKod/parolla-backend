<?php

use App\Http\Controllers\Api\V1\AlphabetController;
use App\Http\Controllers\Api\V1\AuthController;
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

Route::middleware("localization")->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.me');
        Route::put('/auth/me', [AuthController::class, 'update'])->name('api.update')
            ->middleware(['throttle:2,1']);

        Route::get('/statistics', [QuestionController::class, 'statistics'])->name('api.statistics');

        Route::get('/alphabet', [AlphabetController::class, 'index'])->name('api.alphabet');
        Route::get('/questions', [QuestionController::class, 'index'])->name('api.questions');
        Route::get('/modes/unlimited', [QuestionController::class, 'unlimited'])->name('api.modes.unlimited');
        Route::post('/modes/custom', [QuestionController::class, 'custom_store'])
            ->name('api.modes.custom_store')->middleware(['throttle:5,1']);
        Route::get('/modes/custom', [QuestionController::class, 'custom_get'])->name('api.modes.custom_get');
        Route::get('/rooms', [QuestionController::class, 'rooms'])->name('api.rooms');
        Route::get('/rooms/{room}/reviews', [QuestionController::class, 'reviews'])->name('api.reviews');
        Route::post('/rooms/{room}/reviews', [QuestionController::class, 'reviews_store'])->name('api.reviews');
    });
});
