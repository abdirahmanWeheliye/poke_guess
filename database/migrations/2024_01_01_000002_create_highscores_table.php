<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('highscores', function (Blueprint $table) {
            $table->id();
            $table->string('mode');       // easy | medium | hard
            $table->integer('score')->default(0);
            $table->string('session_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('highscores');
    }
};
