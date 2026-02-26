<div class="min-h-screen flex flex-col items-center justify-center bg-[#ff0000] py-10">
    <style>
        /* The central stage */
        .pokemon-stage {
            width: 350px;
            height: 350px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 0 80px 20px rgba(255, 255, 255, 0.8), 0 0 120px 40px rgba(59, 130, 246, 0.5);
            margin-bottom: 40px;
        }

        /* The silhouette logic */
        .silhouette {
            filter: brightness(0);
            transition: filter 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .revealed {
            filter: brightness(1);
        }

        .pokemon-img {
            width: 70%;
            height: 70%;
            object-fit: contain;
            z-index: 10;
        }

        .game-input {
            background: white;
            border: 4px solid #3b82f6;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 1.25rem;
            font-weight: 600;
            width: 300px;
            text-align: center;
            outline: none;
        }

        .guess-button {
            background: #ffcb05;
            border: 4px solid #3b82f6;
            color: #3b82f6;
            font-weight: 900;
            text-transform: uppercase;
            padding: 12px 36px;
            border-radius: 12px;
            font-size: 1.25rem;
            cursor: pointer;
            transition: transform 0.1s ease;
            margin-top: 10px;
        }

        .guess-button:active {
            transform: scale(0.95);
        }

        .next-button {
            background: #22c55e;
            color: white;
            border: none;
            padding: 12px 36px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1.25rem;
            cursor: pointer;
        }

        .menu-card {
            background: white;
            border: 8px solid #3b82f6;
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>

    @if (!$gameStarted)
        <div class="menu-card max-w-sm w-full mx-4">
            <h1 class="text-4xl font-black text-blue-600 mb-2 italic">PokéGuess</h1>
            <p class="text-gray-500 mb-6 font-bold uppercase tracking-tighter">High Score: {{ $highScore }}</p>

            <div class="flex flex-col gap-4">
                <button wire:click="startGame('easy')" class="guess-button">Easy (Gen 1)</button>
                <button wire:click="startGame('hard')" class="guess-button bg-orange-400">Hard (All Gen)</button>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center">
            <div class="mb-4 text-white font-black text-xl flex gap-8 uppercase italic tracking-widest">
                <span>Score: {{ $score }}</span>
                <span>Best: {{ $highScore }}</span>
            </div>

            <div class="pokemon-stage">
                @if ($pokemon)
                    <img src="{{ $pokemon['sprites']['other']['official-artwork']['front_default'] }}"
                        alt="Who's that Pokémon?" class="pokemon-img {{ $isCorrect ? 'revealed' : 'silhouette' }}">
                @endif
            </div>

            <div class="flex flex-col items-center gap-4">
                @if (!$isCorrect)
                    <form wire:submit.prevent="checkGuess" class="flex flex-col items-center gap-4">
                        <input autofocus type="text" wire:model="guess" placeholder="NAME IT!"
                            class="game-input uppercase" autocomplete="off">
                        <button type="submit" class="guess-button" wire:loading.attr="disabled">
                            GUESS!
                        </button>
                    </form>
                @else
                    <div class="text-center">
                        <p class="text-white font-black text-3xl mb-4 italic tracking-widest drop-shadow-lg">
                            IT'S {{ strtoupper($pokemon['name']) }}!
                        </p>
                        <button wire:click="loadNewPokemon" class="next-button shadow-lg animate-pulse">
                            NEXT POKÉMON
                        </button>
                    </div>
                @endif
            </div>

            <div class="mt-12 w-32 h-32 opacity-80">
                <div class="bg-slate-800 p-4 rounded-full shadow-2xl border-4 border-blue-500">
                    <svg viewBox="0 0 100 100" class="w-full h-full">
                        <circle cx="50" cy="50" r="45" fill="white" stroke="#1e293b" stroke-width="2" />
                        <path d="M5,50 a1,1 0 0,1 90,0" fill="#f87171" stroke="#1e293b" stroke-width="2" />
                        <line x1="5" y1="50" x2="95" y2="50" stroke="#1e293b"
                            stroke-width="5" />
                        <circle cx="50" cy="50" r="12" fill="white" stroke="#1e293b" stroke-width="5" />
                        <circle cx="50" cy="50" r="6" fill="white" stroke="#1e293b" stroke-width="2" />
                    </svg>
                </div>
            </div>
        </div>
    @endif
</div>
