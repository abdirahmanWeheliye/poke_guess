<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PokedexController;
use Illuminate\Support\Facades\Route;

// Home: mode selection
Route::get('/', [HomeController::class, 'index'])->name('home');

// Pokédex
Route::get('/pokedex',      [PokedexController::class, 'index'])->name('pokedex.index');
Route::get('/pokedex/{id}', [PokedexController::class, 'show'])->name('pokedex.show')->where('id', '[0-9]+');

// Game: show the quiz screen (GET starts a new session)
Route::get('/game', [GameController::class, 'show'])->name('game.play');

// Game API: called via fetch() from JavaScript
Route::post('/game/guess',  [GameController::class, 'guess'])->name('game.guess');
Route::post('/game/reveal', [GameController::class, 'reveal'])->name('game.reveal');
