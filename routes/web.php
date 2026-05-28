<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes – PokéQuiz
|--------------------------------------------------------------------------
*/

// Home: mode selection
Route::get('/', [HomeController::class, 'index'])->name('home');

// Game: show the quiz screen (GET starts a new session)
Route::get('/game', [GameController::class, 'show'])->name('game.play');

// Game API: called via fetch() from JavaScript
Route::post('/game/guess',  [GameController::class, 'guess'])->name('game.guess');
Route::post('/game/reveal', [GameController::class, 'reveal'])->name('game.reveal');
