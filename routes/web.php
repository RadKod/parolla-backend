<?php

use App\Http\Controllers\QuestionController;
use Illuminate\Routing\Router;

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

/** @var Router $router */
/** @noinspection PhpUnhandledExceptionInspection */
$router = app()->make('router');

$router->view('/', 'welcome');

$router->group(['middleware' => ['auth:sanctum', 'verified']], function (Router $router) {
    $router->get('questions', [QuestionController::class, 'index'])->name('questions');
    $router->view('profile', 'users.profile')->name('user.profile');
    $router->get('clear-cache', function () {
        cache()->flush();
        return redirect()->back()->with('status', 'Cache Cleared!');
    })->name('utils.clear-cache');
    $router->view('dashboard', 'dashboard');
});
