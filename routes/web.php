<?php

use App\Http\Controllers\GameController;
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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/new-game', [App\Http\Controllers\HomeController::class, 'newGame'])->name('new-game');
Route::get('board/{id}', [GameController::class, 'board'])->name('game.board');
Route::post('play/{id}', [GameController::class, 'play'])->name('game.play');
Route::post('game-over/{id}', [GameController::class, 'gameOver'])->name('game.game-over');
