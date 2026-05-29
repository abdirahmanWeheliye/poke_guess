<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Home: mode selection
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/game', [GameController::class, 'show'])->name('game.play');
Route::post('/game/guess',  [GameController::class, 'guess'])->name('game.guess');
Route::post('/game/reveal', [GameController::class, 'reveal'])->name('game.reveal');
