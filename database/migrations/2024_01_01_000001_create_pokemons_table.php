<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            $table->integer('pokedex_id')->unique();
            $table->string('name');          // lowercase slug, e.g. "bulbasaur"
            $table->string('display_name');  // Title case, e.g. "Bulbasaur"
            $table->string('sprite_url');    // Official artwork URL
            $table->integer('generation');   // 1 = Gen1-only, 2+ = newer gens
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }
};
