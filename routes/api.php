<?php

use App\Http\Controllers\Api\V1\AlphabetController;
use App\Http\Controllers\Api\V1\QuestionController;
use Illuminate\Routing\Router;

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

/** @var Router $router */
/** @noinspection PhpUnhandledExceptionInspection */
$router = app()->make('router');

$router->group(['prefix' => 'v1', 'as' => 'api.'], function (Router $router) {
    $router->get('alphabet', [AlphabetController::class, 'index'])->name('alphabet');
    $router->get('questions', [QuestionController::class, 'index'])->name('questions');
});
