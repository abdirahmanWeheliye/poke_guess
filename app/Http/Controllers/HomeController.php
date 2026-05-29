<?php

namespace App\Http\Controllers;

use App\Models\Highscore;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the home / mode-selection screen.
     */
    public function index()
    {
        // We show the global best score for each mode so the player
        // can see what they're up against before choosing a difficulty.
        $highscores = [
            'easy'   => Highscore::bestForMode('easy'),
            'medium' => Highscore::bestForMode('medium'),
            'hard'   => Highscore::bestForMode('hard'),
        ];

    return view('home', compact('highscores'));
    }
}
