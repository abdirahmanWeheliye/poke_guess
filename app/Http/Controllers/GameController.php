<?php

namespace App\Http\Controllers;

use App\Models\Highscore;
use App\Models\Pokemon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // Max wrong guesses before game over
    private const MAX_CHANCES = 3;

    // -------------------------------------------------------------------------
    // Page: show the game view
    // -------------------------------------------------------------------------

    /**
     * GET /game?mode=easy|medium|hard
     * Starts a fresh game session for the chosen mode.
     */
    public function show(Request $request)
    {
        $mode = $request->query('mode', 'easy');

        if (!in_array($mode, ['easy', 'medium', 'hard'])) {
            return redirect()->route('home');
        }

        // Reset game state in session
        $request->session()->put('game', [
            'mode'         => $mode,
            'score'        => 0,
            'chances_left' => self::MAX_CHANCES,
            'used_ids'     => [],   // Pokémon already shown this session
        ]);

        // Fetch the first Pokémon
        $pokemon = $this->getNextPokemon($mode, []);

        if (!$pokemon) {
            // Edge case: no Pokémon in DB for this mode yet (seeder not run)
            return redirect()->route('home')->with('error', 'No Pokémon found. Please run the seeder first.');
        }

        $request->session()->put('game.current_id', $pokemon->id);
        $request->session()->push('game.used_ids', $pokemon->id);

        $highscore = Highscore::bestForMode($mode);

        return view('game.play', [
            'mode'          => $mode,
            'score'         => 0,
            'chancesLeft'   => self::MAX_CHANCES,
            'highscore'     => $highscore,
            'pokemon'       => $pokemon,
        ]);
    }

    // -------------------------------------------------------------------------
    // API: guess
    // -------------------------------------------------------------------------

    /**
     * POST /game/guess
     * Body: { guess: "pikachu" }
     *
     * Returns JSON:
     *   { correct: true,  score: int, pokemon: {name, display_name, sprite_url} }
     *   { correct: false, chancesLeft: int }
     *   { game_over: true, score: int, pokemon: {name, display_name, sprite_url} }
     */
    public function guess(Request $request): JsonResponse
    {
        $request->validate(['guess' => 'required|string|max:100']);

        $game = $request->session()->get('game');

        if (!$game) {
            return response()->json(['error' => 'No active game session.'], 400);
        }

        $currentPokemon = Pokemon::find($game['current_id']);

        if (!$currentPokemon) {
            return response()->json(['error' => 'Pokemon not found.'], 404);
        }

        // Normalize both sides: lowercase, strip accents, trim
        $guess   = $this->normalize($request->input('guess'));
        $correct = $this->normalize($currentPokemon->name);

        // Also accept the display name (e.g. "Nidoran M" or hyphenated forms)
        $correctAlt = $this->normalize($currentPokemon->display_name);

        if ($guess === $correct || $guess === $correctAlt) {
            // --- CORRECT GUESS ---
            $newScore = $game['score'] + 100;
            $request->session()->put('game.score', $newScore);
            $request->session()->put('game.chances_left', self::MAX_CHANCES); // reset chances for next round

            // Persist highscore
            Highscore::recordScore($game['mode'], $newScore, $request->session()->getId());

            // Load next Pokémon
            $next = $this->getNextPokemon($game['mode'], $game['used_ids']);

            if ($next) {
                $request->session()->put('game.current_id', $next->id);
                $request->session()->push('game.used_ids', $next->id);
            }

            return response()->json([
                'correct'  => true,
                'score'    => $newScore,
                'revealed' => [
                    'name'         => $currentPokemon->name,
                    'display_name' => $currentPokemon->display_name,
                    'sprite_url'   => $currentPokemon->sprite_url,
                ],
                'next'     => $next ? [
                    'sprite_url' => $next->sprite_url,
                ] : null,
            ]);
        }

        // --- WRONG GUESS ---
        $chancesLeft = $game['chances_left'] - 1;
        $request->session()->put('game.chances_left', $chancesLeft);

        if ($chancesLeft <= 0) {
            // Game over – save score and return the answer
            Highscore::recordScore($game['mode'], $game['score'], $request->session()->getId());

            return response()->json([
                'correct'   => false,
                'game_over' => true,
                'score'     => $game['score'],
                'revealed'  => [
                    'name'         => $currentPokemon->name,
                    'display_name' => $currentPokemon->display_name,
                    'sprite_url'   => $currentPokemon->sprite_url,
                ],
            ]);
        }

        return response()->json([
            'correct'      => false,
            'game_over'    => false,
            'chancesLeft'  => $chancesLeft,
        ]);
    }

    // -------------------------------------------------------------------------
    // API: reveal (skip without scoring)
    // -------------------------------------------------------------------------

    /**
     * POST /game/reveal
     * Player gives up on the current Pokémon. No point awarded.
     * Returns the answer and loads the next Pokémon.
     * After 3 consecutive reveals the game ends (optional: adjust as needed).
     */
    public function reveal(Request $request): JsonResponse
    {
        $game = $request->session()->get('game');

        if (!$game) {
            return response()->json(['error' => 'No active game session.'], 400);
        }

        $currentPokemon = Pokemon::find($game['current_id']);

        // Reset chances for the next round (reveal costs nothing but no points)
        $request->session()->put('game.chances_left', self::MAX_CHANCES);

        // Load next Pokémon
        $next = $this->getNextPokemon($game['mode'], $game['used_ids']);

        if ($next) {
            $request->session()->put('game.current_id', $next->id);
            $request->session()->push('game.used_ids', $next->id);
        }

        return response()->json([
            'revealed' => $currentPokemon ? [
                'name'         => $currentPokemon->name,
                'display_name' => $currentPokemon->display_name,
                'sprite_url'   => $currentPokemon->sprite_url,
            ] : null,
            'score' => $game['score'],
            'next'  => $next ? [
                'sprite_url' => $next->sprite_url,
            ] : null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Pick a random Pokémon for the given mode that hasn't been shown yet.
     * If all have been shown, resets the used list and starts over.
     */
    private function getNextPokemon(string $mode, array $usedIds): ?Pokemon
    {
        $query = Pokemon::forMode($mode);

        if (!empty($usedIds)) {
            $next = (clone $query)->whereNotIn('id', $usedIds)->inRandomOrder()->first();

            // If we've exhausted the pool, start fresh
            if (!$next) {
                $next = (clone $query)->inRandomOrder()->first();
            }

            return $next;
        }

        return $query->inRandomOrder()->first();
    }

    /**
     * Normalize a Pokémon name for comparison:
     * lowercase, remove accents, strip special chars (except letters/numbers).
     */
    private function normalize(string $name): string
    {
        $name = mb_strtolower(trim($name));
        // Replace common accented chars
        $name = str_replace(['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ô', 'ö', 'û', 'ü', 'ù', 'î', 'ï', 'ç'],
                            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'o', 'o', 'u', 'u', 'u', 'i', 'i', 'c'],
                            $name);
        // Remove hyphens, spaces, dots
        $name = preg_replace('/[\s\-\.\'\:]/', '', $name);
        return $name;
    }
}
