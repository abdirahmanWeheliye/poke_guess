<?php

namespace Database\Seeders;

use App\Models\Pokemon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * PokemonSeeder
 *
 * Seeds the `pokemons` table by fetching data from the PokéAPI.
 * Run once:  php artisan db:seed --class=PokemonSeeder
 *
 * Generation boundaries (by Pokédex number):
 *   Gen 1: #1  – #151
 *   Gen 2: #152 – #251
 *   Gen 3: #252 – #386
 *   Gen 4: #387 – #493
 *   Gen 5: #494 – #649
 *   Gen 6: #650 – #721
 *   Gen 7: #722 – #809
 *   Gen 8: #810 – #905
 *   Gen 9: #906 – #1025
 */
class PokemonSeeder extends Seeder
{
    private const GENERATION_RANGES = [
        1 => [1,   151],
        2 => [152, 251],
        3 => [252, 386],
        4 => [387, 493],
        5 => [494, 649],
        6 => [650, 721],
        7 => [722, 809],
        8 => [810, 905],
        9 => [906, 1025],
    ];

    public function run(): void
    {
        $this->command->info('Fetching Pokémon from PokéAPI… this may take a minute.');

        Pokemon::truncate();

        // Fetch the full list once (limit high enough to get all ~1025)
        $listResponse = Http::timeout(30)->get('https://pokeapi.co/api/v2/pokemon', [
            'limit'  => 1100,
            'offset' => 0,
        ]);

        if ($listResponse->failed()) {
            $this->command->error('Failed to fetch Pokémon list from PokéAPI.');
            return;
        }

        $results = $listResponse->json('results');
        $total   = count($results);
        $bar     = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        $batch = [];

        foreach ($results as $index => $entry) {
            // Extract numeric ID from the URL, e.g. "https://pokeapi.co/api/v2/pokemon/25/"
            preg_match('/\/pokemon\/(\d+)\/$/', $entry['url'], $m);
            $pokedexId = isset($m[1]) ? (int) $m[1] : ($index + 1);

            // Skip forms / variants above 10 000 (they have large IDs)
            if ($pokedexId > 1025) {
                $bar->advance();
                continue;
            }

            $generation = $this->generationFor($pokedexId);
            $name       = $entry['name'];          // e.g. "bulbasaur"
            $displayName = Str::title(str_replace('-', ' ', $name)); // "Bulbasaur"

            // Official artwork – available without an extra API call
            $spriteUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{$pokedexId}.png";

            $batch[] = [
                'pokedex_id'   => $pokedexId,
                'name'         => $name,
                'display_name' => $displayName,
                'sprite_url'   => $spriteUrl,
                'generation'   => $generation,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            // Insert in chunks of 100 to avoid memory issues
            if (count($batch) >= 100) {
                Pokemon::insert($batch);
                $batch = [];
            }

            $bar->advance();
        }

        // Insert any remaining records
        if (!empty($batch)) {
            Pokemon::insert($batch);
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Seeded ' . Pokemon::count() . ' Pokémon successfully!');
    }

    private function generationFor(int $pokedexId): int
    {
        foreach (self::GENERATION_RANGES as $gen => [$start, $end]) {
            if ($pokedexId >= $start && $pokedexId <= $end) {
                return $gen;
            }
        }
        return 9; // fallback for anything beyond the known ranges
    }
}
