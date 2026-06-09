<?php

namespace App\Http\Controllers;

use App\Models\Highscore;
use App\Models\Pokemon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    private const MAX_CHANCES = 3;

    // Reveal limits per mode: easy = unlimited, medium = 3, hard = 0
    private const REVEAL_LIMITS = [
        'easy'   => -1, // -1 = unlimited
        'medium' => 3,
        'hard'   => 0,
    ];

    // -------------------------------------------------------------------------
    // Page: show the game view
    // -------------------------------------------------------------------------

    public function show(Request $request)
    {
        $mode = $request->query('mode', 'easy');

        if (!in_array($mode, ['easy', 'medium', 'hard'])) {
            return redirect()->route('home');
        }

        $revealLimit = self::REVEAL_LIMITS[$mode];

        $request->session()->put('game', [
            'mode'          => $mode,
            'score'         => 0,
            'chances_left'  => self::MAX_CHANCES,
            'reveals_left'  => $revealLimit, // -1 = unlimited
            'used_ids'      => [],
        ]);

        $pokemon = $this->getNextPokemon($mode, []);

        if (!$pokemon) {
            return redirect()->route('home')->with('error', 'No Pokémon found. Please run the seeder first.');
        }

        $request->session()->put('game.current_id', $pokemon->id);
        $request->session()->push('game.used_ids', $pokemon->id);

        $highscore = Highscore::bestForMode($mode);

        return view('game.play', [
            'mode'         => $mode,
            'score'        => 0,
            'chancesLeft'  => self::MAX_CHANCES,
            'revealsLeft'  => $revealLimit,
            'revealLimit'  => $revealLimit,
            'highscore'    => $highscore,
            'pokemon'      => $pokemon,
        ]);
    }

    // -------------------------------------------------------------------------
    // API: guess
    // -------------------------------------------------------------------------

    public function guess(Request $request): JsonResponse
    {
        $request->validate(['guess' => 'required|string|max:100']);

        $game = $request->session()->get('game');
        if (!$game) return response()->json(['error' => 'No active game session.'], 400);

        $currentPokemon = Pokemon::find($game['current_id']);
        if (!$currentPokemon) return response()->json(['error' => 'Pokemon not found.'], 404);

        $guess      = $this->normalize($request->input('guess'));
        $correct    = $this->normalize($currentPokemon->name);
        $correctAlt = $this->normalize($currentPokemon->display_name);

        if ($guess === $correct || $guess === $correctAlt) {
            // ── CORRECT ──
            $newScore = $game['score'] + 100;
            $request->session()->put('game.score', $newScore);
            // Reset chances but NOT reveals — reveals persist across rounds
            $newChances = min(self::MAX_CHANCES, $game['chances_left'] + 1);
            $request->session()->put('game.chances_left', $newChances);

            Highscore::recordScore($game['mode'], $newScore, $request->session()->getId());

            // Preload next Pokémon into session so it's ready
            $next = $this->getNextPokemon($game['mode'], $game['used_ids']);
            if ($next) {
                $request->session()->put('game.current_id', $next->id);
                $request->session()->push('game.used_ids', $next->id);
            }

            return response()->json([
                'correct'     => true,
                'score'       => $newScore,
                'revealsLeft' => $game['reveals_left'],
                'chancesLeft' => $newChances,
                'revealed'    => [
                    'display_name' => $currentPokemon->display_name,
                    'sprite_url'   => $currentPokemon->sprite_url,
                ],
                'next' => $next ? ['sprite_url' => $next->sprite_url] : null,
            ]);
        }

        // ── WRONG ──
        $chancesLeft = $game['chances_left'] - 1;
        $request->session()->put('game.chances_left', $chancesLeft);

        if ($chancesLeft <= 0) {
            Highscore::recordScore($game['mode'], $game['score'], $request->session()->getId());

            return response()->json([
                'correct'   => false,
                'game_over' => true,
                'score'     => $game['score'],
                'revealed'  => [
                    'display_name' => $currentPokemon->display_name,
                    'sprite_url'   => $currentPokemon->sprite_url,
                ],
            ]);
        }

        return response()->json([
            'correct'     => false,
            'game_over'   => false,
            'chancesLeft' => $chancesLeft,
        ]);
    }

    // -------------------------------------------------------------------------
    // API: reveal
    // -------------------------------------------------------------------------

    public function reveal(Request $request): JsonResponse
    {
        $game = $request->session()->get('game');
        if (!$game) return response()->json(['error' => 'No active game session.'], 400);

        $revealsLeft = $game['reveals_left'];

        // Hard mode: reveals completely disabled
        if ($revealsLeft === 0) {
            return response()->json(['error' => 'Reveals are not allowed in this mode.'], 403);
        }

        $currentPokemon = Pokemon::find($game['current_id']);

        // Decrement reveals (only if limited; -1 = unlimited stays -1)
        $newRevealsLeft = $revealsLeft === -1 ? -1 : $revealsLeft - 1;
        $request->session()->put('game.reveals_left', $newRevealsLeft);

        // Keep current chances as-is — do NOT reset them
        // (fix for issue #1: lives lost before reveal stay lost)

        // Pre-load next Pokémon into session
        $next = $this->getNextPokemon($game['mode'], $game['used_ids']);
        if ($next) {
            $request->session()->put('game.current_id', $next->id);
            $request->session()->push('game.used_ids', $next->id);
        }

        return response()->json([
            'revealed' => $currentPokemon ? [
                'display_name' => $currentPokemon->display_name,
                'sprite_url'   => $currentPokemon->sprite_url,
            ] : null,
            'revealsLeft' => $newRevealsLeft,
            'score'       => $game['score'],
            'next'        => $next ? ['sprite_url' => $next->sprite_url] : null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getNextPokemon(string $mode, array $usedIds): ?Pokemon
    {
        $query = Pokemon::forMode($mode);

        if (!empty($usedIds)) {
            $next = (clone $query)->whereNotIn('id', $usedIds)->inRandomOrder()->first();
            if (!$next) {
                $next = (clone $query)->inRandomOrder()->first();
            }
            return $next;
        }

        return $query->inRandomOrder()->first();
    }

    private function normalize(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = str_replace(
            ['é','è','ê','ë','à','â','ä','ô','ö','û','ü','ù','î','ï','ç'],
            ['e','e','e','e','a','a','a','o','o','u','u','u','i','i','c'],
            $name
        );
        $name = preg_replace('/[\s\-\.\'\:]/', '', $name);
        return $name;
    }
}
