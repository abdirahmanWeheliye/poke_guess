<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PokedexController extends Controller
{
    // -------------------------------------------------------------------------
    // Pokédex list page
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $type   = $request->query('type', '');
        $gen    = $request->query('gen', '');

        $query = Pokemon::query()->orderBy('pokedex_id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('pokedex_id', 'like', "%{$search}%");
            });
        }

        if ($gen) {
            $query->where('generation', $gen);
        }

        $pokemons = $query->get();

        return view('pokedex.index', compact('pokemons', 'search', 'type', 'gen'));
    }

    // -------------------------------------------------------------------------
    // Individual Pokémon detail page
    // -------------------------------------------------------------------------

    public function show(int $pokedexId)
    {
        $pokemon = Pokemon::where('pokedex_id', $pokedexId)->firstOrFail();

        // Cache all API data for 24 hours to avoid hammering PokéAPI
        $data = Cache::remember("pokedex_{$pokedexId}", 86400, function () use ($pokedexId, $pokemon) {
            return $this->fetchPokemonData($pokedexId, $pokemon->name);
        });

        return view('pokedex.show', compact('pokemon', 'data'));
    }

    // -------------------------------------------------------------------------
    // Fetch full Pokémon data from PokéAPI
    // -------------------------------------------------------------------------

    private function fetchPokemonData(int $pokedexId, string $name): array
    {
        // Base Pokémon data (stats, types, moves, height, weight)
        $pokeResponse = Http::timeout(10)->get("https://pokeapi.co/api/v2/pokemon/{$name}");

        // Species data (flavour text, evolution chain URL)
        $speciesResponse = Http::timeout(10)->get("https://pokeapi.co/api/v2/pokemon-species/{$pokedexId}");

        if ($pokeResponse->failed() || $speciesResponse->failed()) {
            return $this->emptyData();
        }

        $pokeData    = $pokeResponse->json();
        $speciesData = $speciesResponse->json();

        // ── Types ──────────────────────────────────────────────────────────
        $types = collect($pokeData['types'])
            ->sortBy('slot')
            ->pluck('type.name')
            ->map(fn($t) => ucfirst($t))
            ->toArray();

        // ── Stats ──────────────────────────────────────────────────────────
        $statMap = [
            'hp'              => 'HP',
            'attack'          => 'Attack',
            'defense'         => 'Defense',
            'special-attack'  => 'Sp. Atk',
            'special-defense' => 'Sp. Def',
            'speed'           => 'Speed',
        ];
        $stats = [];
        foreach ($pokeData['stats'] as $stat) {
            $key = $stat['stat']['name'];
            if (isset($statMap[$key])) {
                $stats[] = [
                    'name'  => $statMap[$key],
                    'value' => $stat['base_stat'],
                ];
            }
        }

        // ── Moves (first 20, level-up only) ───────────────────────────────
        $moves = collect($pokeData['moves'])
            ->filter(fn($m) => collect($m['version_group_details'])
                ->contains(fn($v) => $v['move_learn_method']['name'] === 'level-up'))
            ->take(20)
            ->map(fn($m) => ucwords(str_replace('-', ' ', $m['move']['name'])))
            ->values()
            ->toArray();

        // ── Flavour text (first English entry) ────────────────────────────
        $flavour = collect($speciesData['flavor_text_entries'] ?? [])
            ->firstWhere('language.name', 'en');
        $flavourText = $flavour
            ? preg_replace('/\s+/', ' ', str_replace(["\n", "\f"], ' ', $flavour['flavor_text']))
            : 'No description available.';

        // ── Evolution chain ───────────────────────────────────────────────
        $evolutionChain = [];
        $evoUrl = $speciesData['evolution_chain']['url'] ?? null;
        if ($evoUrl) {
            $evoResponse = Http::timeout(10)->get($evoUrl);
            if ($evoResponse->ok()) {
                $evolutionChain = $this->parseEvolutionChain($evoResponse->json('chain'));
            }
        }

        // ── Abilities ─────────────────────────────────────────────────────
        $abilities = collect($pokeData['abilities'])
            ->map(fn($a) => ucwords(str_replace('-', ' ', $a['ability']['name'])))
            ->toArray();

        return [
            'types'          => $types,
            'stats'          => $stats,
            'moves'          => $moves,
            'flavour_text'   => $flavourText,
            'evolution_chain'=> $evolutionChain,
            'abilities'      => $abilities,
            'height'         => $pokeData['height'] / 10,   // decimetres → metres
            'weight'         => $pokeData['weight'] / 10,   // hectograms → kg
        ];
    }

    // -------------------------------------------------------------------------
    // Recursively parse the evolution chain into a flat array
    // -------------------------------------------------------------------------

    private function parseEvolutionChain(array $chain): array
    {
        $result = [];
        $current = $chain;

        while ($current) {
            $speciesName = $current['species']['name'];

            // Get the Pokémon from our DB to get the sprite + pokedex_id
            $pokemon = Pokemon::where('name', $speciesName)->first();

            $result[] = [
                'name'       => ucfirst($speciesName),
                'pokedex_id' => $pokemon?->pokedex_id,
                'sprite_url' => $pokemon?->sprite_url,
            ];

            $current = $current['evolves_to'][0] ?? null;
        }

        return $result;
    }

    // -------------------------------------------------------------------------
    // Empty fallback if API is unreachable
    // -------------------------------------------------------------------------

    private function emptyData(): array
    {
        return [
            'types'           => [],
            'stats'           => [],
            'moves'           => [],
            'flavour_text'    => 'Data currently unavailable.',
            'evolution_chain' => [],
            'abilities'       => [],
            'height'          => 0,
            'weight'          => 0,
        ];
    }
}
