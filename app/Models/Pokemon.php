<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemons'; // ← add this line

    protected $fillable = [
        'pokedex_id',
        'name',
        'display_name',
        'sprite_url',
        'generation',
    ];

    public function scopeForMode($query, string $mode)
    {
        return match ($mode) {
            'easy'   => $query->where('generation', 1),
            'medium' => $query->where('generation', '>', 1),
            default  => $query,
        };
    }
}
