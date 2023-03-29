<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\QuestionController;
use App\Models\Question;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
Route::get('/change_cache', function () {
    $question = Question::query()->find(1);
    $question->release_at = now()->subDays(20);
    $question->save();
    exit();
    $last_5_days = [];
    for ($i = 5; $i > 0; $i--) {
        $last_5_days[] = Carbon::now()->subDays($i)->toDateString();
    }

    $old_cache_file_names = [];
    foreach ($last_5_days as $day) {
        $old_cache_file_names[] = md5('questions_' . $day);
    }
    $not_in_ids = [];
    foreach ($old_cache_file_names as $cache_file_name) {
        if (Cache::has($cache_file_name)) {
            $old_questions = Cache::get($cache_file_name);
            $not_in_ids = array_merge($not_in_ids, $old_questions->pluck('id')->toArray());
        }
    }

    $today = Carbon::now()->toDateString();
    $cache_file_name = md5('questions_' . $today);

    $subFromQuery = Question::query()
        ->select('id', 'alphabet_id', 'question', 'answer')
        ->inRandomOrder()->toSql();
    $questions = Question::query()
        ->select('id', 'alphabet_id', 'question', 'answer')
        ->whereNotIn('id', $not_in_ids)
        ->from(DB::raw("($subFromQuery) as sub"))
        ->with(['alphabet'])
        ->groupBy('alphabet_id')
        ->get();

    try {
        Cache::forever($cache_file_name, $questions);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }


    $questions = Cache::get($cache_file_name);
    dd($questions, $not_in_ids);



    return false;
    $today = Carbon::now()->toDateString();

    $cache_file_name = md5('questions_' . $today);

//    if (Cache::has($cache_file_name)) {
//        $questions = Cache::get($cache_file_name);
//        $questions[0]['answer'] = 'Antarktika, Antartika';
//        $cache_update = Cache::put($cache_file_name, $questions);
//        dd($cache_update);
//    }
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::resource('questions', QuestionController::class)->names([
        'index' => 'questions'
    ]);

    Route::get('rooms', [QuestionController::class, 'rooms'])->name('rooms');

    Route::resource('calendar', CalendarController::class)->names([
        'index' => 'calendar'
    ]);


    Route::view('profile','users.profile')->name('profile');

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        Question::query()->update(['release_at' => null]);
        return redirect()->back()->with('status', 'Cache Cleared!');
    })->name('clear-cache');
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
