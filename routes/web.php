<?php

use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::resource('questions', QuestionController::class)->names([
        'index' => 'questions'
    ]);

    Route::view('profile','users.profile')->name('profile');

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return redirect()->back()->with('status', 'Cache Cleared!');
    })->name('clear-cache');
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
