<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Highscore extends Model
{
    protected $fillable = ['mode', 'score', 'session_id'];

    /**
     * Get the best score for a given mode (global, session-agnostic).
     */
    public static function bestForMode(string $mode): int
    {
        return static::where('mode', $mode)->max('score') ?? 0;
    }

    /**
     * Upsert: keep only the highest score per session+mode combination.
     */
    public static function recordScore(string $mode, int $score, string $sessionId): void
    {
        $existing = static::where('mode', $mode)
            ->where('session_id', $sessionId)
            ->first();

        if ($existing) {
            if ($score > $existing->score) {
                $existing->update(['score' => $score]);
            }
        } else {
            static::create([
                'mode'       => $mode,
                'score'      => $score,
                'session_id' => $sessionId,
            ]);
        }
    }
}
