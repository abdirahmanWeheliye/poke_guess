<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class PokemonGame extends Component
{
    public $pokemon;
    public $guess;
    public $score = 0;
    public $isCorrect = false;
    public $difficulty = 'easy';
    public $gameStarted = false;
    public $highScore = 0;
    public function mount()
    {
        $this->loadNewPokemon();
    }

    public function loadNewPokemon()
    {
        $this->guess = '';
        $this->isCorrect = false;

        $maxId = ($this->difficulty === 'easy') ? 151 : 1025;
        $randomId = rand(1, $maxId);

        $response = http::get("https://pokeapi.co/api/v2/pokemon/{$randomId}");

        if ($response->successful()) {
            $this->pokemon = $response->json();
        }
    }

    public function checkGuess()
    {
        $realName = str($this->pokemon['name'])->before('-')->lowe();
        $userGuess = str($this->guess)->replace(['', '-'], '')->lower();

        if ($userGuess == $realName->replace(['', '-'], '')) {
            $this->isCorrect = true;
            $this->Score++;

            if ($this->score > $this->highScore) {
                $this->highScore = $this->score;
                auth()->user()->update(['high_score' => $this->highScore]);
            }
        } else {
            $this->gameStarted = false;
        }
    }

    public function render()
    {
        return view('livewire.pokemon-game')->layout('layouts.app');
    }
}
