<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $fillable = [
        'pokedex_id',
        'name',
        'display_name',
        'sprite_url',
        'generation',
    ];

    /**
     * Scope: filter by difficulty mode.
     * easy   => generation = 1         (Pokédex #1–151)
     * medium => generation > 1         (everything after Gen 1)
     * hard   => all generations
     */
    public function scopeForMode($query, string $mode)
    {
        return match ($mode) {
            'easy'   => $query->where('generation', 1),
            'medium' => $query->where('generation', '>', 1),
            default  => $query, // hard: all
        };
    }
}
