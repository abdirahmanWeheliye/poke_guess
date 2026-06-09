<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>POKÉQUIZ – Who's That Pokémon?</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=JetBrains+Mono:wght@700&family=Plus+Jakarta+Sans:wght@500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "on-surface-variant": "#4e4632", "surface-container-highest": "#e4e2e1",
                    "primary": "#745b00", "on-primary-fixed-variant": "#584400",
                    "on-error-container": "#93000a", "on-tertiary": "#ffffff",
                    "on-primary-container": "#6f5700", "surface": "#fbf9f8",
                    "secondary-fixed-dim": "#a0caff", "inverse-primary": "#f2c000",
                    "inverse-surface": "#303030", "tertiary-fixed-dim": "#ffb4a9",
                    "background": "#fbf9f8", "secondary-container": "#73b4ff",
                    "secondary-fixed": "#d2e4ff", "on-background": "#1b1c1c",
                    "surface-container-high": "#eae8e7", "primary-fixed-dim": "#f2c000",
                    "primary-fixed": "#ffe08d", "inverse-on-surface": "#f3f0f0",
                    "outline-variant": "#d2c5ab", "on-primary-fixed": "#241a00",
                    "secondary": "#0061a5", "on-primary": "#ffffff", "on-secondary": "#ffffff",
                    "on-secondary-fixed-variant": "#00497e", "surface-container-lowest": "#ffffff",
                    "on-error": "#ffffff", "surface-tint": "#745b00", "outline": "#80765f",
                    "on-surface": "#1b1c1c", "surface-container": "#f0eded",
                    "on-tertiary-fixed-variant": "#930004", "on-secondary-container": "#004579",
                    "surface-container-low": "#f5f3f2", "tertiary-container": "#ffc3bb",
                    "error": "#ba1a1a", "on-tertiary-fixed": "#410001",
                    "on-secondary-fixed": "#001c37", "tertiary-fixed": "#ffdad5",
                    "tertiary": "#c00008", "surface-dim": "#dcd9d9",
                    "primary-container": "#ffcb05", "error-container": "#ffdad6",
                    "surface-variant": "#e4e2e1", "surface-bright": "#fbf9f8",
                    "on-tertiary-container": "#b70007"
                },
                "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                "spacing": { "xl": "64px", "border-width": "4px", "lg": "40px", "sm": "12px", "xs": "4px", "base": "8px", "md": "24px" },
                "fontFamily": {
                    "headline-sm": ["Bricolage Grotesque"], "display-lg-mobile": ["Bricolage Grotesque"],
                    "label-mono": ["JetBrains Mono"], "display-lg": ["Bricolage Grotesque"],
                    "headline-md": ["Bricolage Grotesque"], "body-lg": ["Plus Jakarta Sans"], "body-md": ["Plus Jakarta Sans"]
                },
                "fontSize": {
                    "headline-sm": ["24px", {"lineHeight": "1.2", "fontWeight": "700"}],
                    "display-lg-mobile": ["36px", {"lineHeight": "1.1", "fontWeight": "800"}],
                    "label-mono": ["14px", {"lineHeight": "1.0", "fontWeight": "700"}],
                    "display-lg": ["48px", {"lineHeight": "1.1", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                    "headline-md": ["32px", {"lineHeight": "1.2", "fontWeight": "700"}],
                    "body-lg": ["18px", {"lineHeight": "1.5", "fontWeight": "600"}],
                    "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "500"}]
                }
            },
        },
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .sunburst-bg { background: linear-gradient(135deg, #ba1a1a 50%, #0061a5 50%); position: relative; overflow: hidden; }
    .sunburst-lines {
        position: absolute; top: 50%; left: 50%; width: 200%; height: 200%;
        background: repeating-conic-gradient(from 0deg, rgba(255,255,255,0.1) 0deg 15deg, transparent 15deg 30deg);
        transform: translate(-50%, -50%);
        animation: rotate 60s linear infinite; pointer-events: none;
    }
    @keyframes rotate {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to   { transform: translate(-50%, -50%) rotate(360deg); }
    }
    /* Fix #2: no yellow tint — reveal shows the clean sprite only */
    .silhouette { filter: brightness(0); transition: filter 0.6s ease-in-out; }
    .revealed   { filter: brightness(1); }
    .press-effect:active { transform: translate(4px, 4px); box-shadow: none !important; }
    .shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
    #poke-sprite { transition: opacity 0.3s ease; }
    /* Disabled reveal button style */
    .reveal-disabled { opacity: 0.4; cursor: not-allowed; }
</style>
</head>
<body class="bg-background text-on-background font-body-md overflow-x-hidden">

<!-- TOP NAV -->
<nav class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-md py-sm bg-primary border-b-4 border-on-background shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
    <div class="flex items-center gap-sm">
        <a href="{{ route('home') }}"
           class="bg-surface p-xs rounded-lg border-2 border-on-background shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all press-effect">
            <span class="material-symbols-outlined text-on-background">arrow_back</span>
        </a>
        <span class="font-headline-md text-headline-md font-black tracking-tighter text-on-primary">POKÉQUIZ</span>
    </div>
    <div class="bg-surface-container border-2 border-on-background px-md py-xs rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center gap-base">
        <span class="font-label-mono text-label-mono text-on-surface-variant uppercase">SCORE:</span>
        <span class="font-headline-sm text-headline-sm text-primary" id="score-counter">{{ $score }}</span>
        <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">stars</span>
    </div>
</nav>

<!-- MAIN -->
<main class="min-h-screen pt-[100px] pb-xl sunburst-bg flex flex-col items-center">
    <div class="sunburst-lines"></div>

    <div class="relative z-10 w-full max-w-[800px] px-md flex flex-col gap-lg items-center">

        <!-- Title Card -->
        <div class="bg-surface-container-lowest border-4 border-on-background p-md rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] text-center w-full transform -rotate-1">
            <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-background italic tracking-tighter leading-none mb-xs">
                WHO'S THAT <br class="md:hidden"> <span class="text-tertiary" id="title-highlight">POKÉMON?</span>
            </h1>
            <p class="font-label-mono text-label-mono text-on-surface-variant uppercase">
                MODE: {{ strtoupper($mode) }}
            </p>
        </div>

        <!-- Gameplay Grid -->
        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-lg items-start">

            <!-- Silhouette + NEXT button below -->
            <div class="flex flex-col gap-md">
                <div class="bg-surface border-4 border-on-background p-lg rounded-3xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]
                            flex justify-center items-center relative aspect-square overflow-hidden bg-white"
                     id="poke-container">
                    <div class="absolute inset-0 opacity-10"
                         style="background-color:#fff;background-image:radial-gradient(rgb(229,229,229) 2px,transparent 2px);background-size:20px 20px;"></div>

                    <img alt="Pokemon Silhouette"
                         class="silhouette w-full h-full object-contain relative z-10 transform scale-110"
                         id="poke-sprite"
                         src="{{ $pokemon->sprite_url }}">

                    <!-- Name reveal banner — replaces the yellow overlay (fix #2) -->
                    <div class="hidden" id="name-banner"><span id="banner-name"></span></div>
                </div>

                <button id="next-btn"
                        onclick="handleNext()"
                        class="hidden self-center bg-primary-container text-on-primary-container font-label-mono text-label-mono
               px-md py-sm border-4 border-on-background rounded-lg
               shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none
               hover:translate-x-1 hover:translate-y-1 transition-all press-effect
               flex items-center gap-xs">
                    <span class="material-symbols-outlined" style="font-size:18px;">skip_next</span>
                    NEXT
                </button>
            </div>

            <!-- Control Panel -->
            <div class="flex flex-col gap-md">
                <div class="bg-white border-4 border-on-background p-md rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                    <label class="block font-label-mono text-label-mono text-on-surface-variant mb-base uppercase">
                        Enter Pokémon Name
                    </label>
                    <input class="w-full p-md font-headline-sm text-headline-sm bg-surface-container border-4
                                  border-on-background rounded-lg focus:outline-none focus:border-primary-container
                                  transition-colors placeholder:opacity-30"
                           id="poke-input"
                           placeholder="Gotta guess 'em all…"
                           type="text"
                           autocomplete="off">

                    <!-- Lives -->
                    <div class="flex gap-base mt-md items-center" id="lives-row">
                        @for($i = 0; $i < 3; $i++)
                            <span class="material-symbols-outlined life-icon text-tertiary"
                                  style="font-variation-settings: 'FILL' {{ $i < $chancesLeft ? '1' : '0' }}, 'wght' 400, 'GRAD' 0, 'opsz' 24;">
                                {{ $i < $chancesLeft ? 'favorite' : 'favorite_border' }}
                            </span>
                        @endfor
                        <span class="ml-auto font-label-mono text-label-mono text-on-surface-variant" id="chances-label">
                            CHANCES: {{ $chancesLeft }}/3
                        </span>
                    </div>
                </div>

                <!-- Guess / Reveal buttons -->
                <div class="grid grid-cols-2 gap-md">
                    <button class="bg-primary-container text-on-primary-container font-headline-sm text-headline-sm
                                   py-md border-4 border-on-background rounded-lg
                                   shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none
                                   hover:translate-x-1 hover:translate-y-1 transition-all press-effect"
                            id="guess-btn">
                        GUESS
                    </button>

                    {{-- Reveal button: label changes per mode --}}
                    @if($revealLimit === 0)
                        {{-- Hard mode: button shown but permanently disabled --}}
                        <button class="reveal-disabled bg-surface-variant text-on-surface-variant font-headline-sm text-headline-sm
                                       py-md border-4 border-on-background rounded-lg" disabled id="reveal-btn">
                            NO REVEAL
                        </button>
                    @else
                        <button class="bg-secondary text-on-secondary font-headline-sm text-headline-sm
                                       py-md border-4 border-on-background rounded-lg
                                       shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none
                                       hover:translate-x-1 hover:translate-y-1 transition-all press-effect"
                                id="reveal-btn">
                            @if($revealLimit === -1)
                                REVEAL
                            @else
                                REVEAL ({{ $revealsLeft }})
                            @endif
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</main>

<!-- GAME OVER MODAL -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 hidden" id="game-over-modal">
    <div class="bg-surface border-4 border-on-background rounded-xl p-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]
                max-w-sm w-full mx-md text-center transform -rotate-1">
        <span class="material-symbols-outlined text-tertiary" style="font-size:64px;font-variation-settings:'FILL' 1,'wght' 700,'GRAD' 0,'opsz' 48;">
            sentiment_very_dissatisfied
        </span>
        <h2 class="font-display-lg-mobile text-display-lg-mobile text-on-background mt-md">GAME OVER!</h2>
        <p class="font-label-mono text-label-mono text-on-surface-variant mt-base">FINAL SCORE</p>
        <p class="font-display-lg text-display-lg text-primary mt-xs" id="final-score">0</p>
        <div class="mt-lg flex flex-col gap-sm">
            <button onclick="retryGame()"
                    class="w-full bg-primary-container text-on-primary-container font-headline-sm text-headline-sm
                           py-md border-4 border-on-background rounded-lg
                           shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none
                           hover:translate-x-1 hover:translate-y-1 transition-all">
                TRY AGAIN
            </button>
            <a href="{{ route('home') }}"
               class="w-full bg-surface text-on-background font-headline-sm text-headline-sm
                      py-md border-4 border-on-background rounded-lg
                      shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none
                      hover:translate-x-1 hover:translate-y-1 transition-all text-center block">
                HOME
            </a>
        </div>
    </div>
</div>

<!-- JAVASCRIPT -->
<script>
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const MODE       = "{{ $mode }}";
    const REVEAL_LIMIT = {{ $revealLimit }}; // -1=unlimited, 0=none, 3=medium
    const GUESS_URL  = "{{ route('game.guess') }}";
    const REVEAL_URL = "{{ route('game.reveal') }}";

    const pokeSprite    = document.getElementById('poke-sprite');
    const nameBanner    = document.getElementById('name-banner');
    const bannerName    = document.getElementById('banner-name');
    const titleHighlight = document.getElementById('title-highlight');
    const scoreCounter  = document.getElementById('score-counter');
    const chancesLabel  = document.getElementById('chances-label');
    const livesRow      = document.getElementById('lives-row');
    const pokeInput     = document.getElementById('poke-input');
    const guessBtn      = document.getElementById('guess-btn');
    const revealBtn     = document.getElementById('reveal-btn');
    const nextBtn       = document.getElementById('next-btn');
    const gameOverModal = document.getElementById('game-over-modal');
    const finalScore    = document.getElementById('final-score');

    let chancesLeft  = {{ $chancesLeft }};
    let revealsLeft  = {{ $revealsLeft }};
    let currentScore = {{ $score }};
    let roundActive  = true;
    let pendingNext  = null; // sprite_url for the next Pokémon, set when round ends

    // ── Audio (fix #4) ────────────────────────────────────────────────────
    // Uses the Web Speech API to say "Who's that Pokémon?" — no audio file needed
    function speakWhosThat() {
        if (!window.speechSynthesis) return;
        const utter = new SpeechSynthesisUtterance("Who's that Pokémon?");
        utter.rate   = 0.9;
        utter.pitch  = 1.1;
        utter.volume = 0.8;
        window.speechSynthesis.cancel(); // stop any previous
        window.speechSynthesis.speak(utter);
    }

    // Speak on page load
    window.addEventListener('load', () => {
        setTimeout(speakWhosThat, 600);
    });

    // ── Helpers ───────────────────────────────────────────────────────────

    function postJSON(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        }).then(r => r.json());
    }

    function updateLives(n) {
        chancesLeft = n;
        const icons = livesRow.querySelectorAll('.life-icon');
        icons.forEach((icon, i) => {
            if (i < n) {
                icon.textContent = 'favorite';
                icon.style.fontVariationSettings = "'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24";
            } else {
                icon.textContent = 'favorite_border';
                icon.style.fontVariationSettings = "'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24";
            }
        });
        chancesLabel.textContent = `CHANCES: ${n}/3`;
    }

    function updateRevealButton(n) {
        revealsLeft = n;
        if (!revealBtn || REVEAL_LIMIT === 0) return;
        if (REVEAL_LIMIT === -1) return; // unlimited — label stays "REVEAL"
        revealBtn.textContent = `REVEAL (${n})`;
        if (n <= 0) {
            revealBtn.disabled = true;
            revealBtn.classList.add('reveal-disabled');
            revealBtn.classList.remove('bg-secondary', 'text-on-secondary');
            revealBtn.classList.add('bg-surface-variant', 'text-on-surface-variant');
        }
    }

    // Fix #2: show clean revealed sprite + name banner, then show NEXT button
    function showRevealedState(displayName, xpText) {
        pokeSprite.classList.remove('silhouette');
        pokeSprite.classList.add('revealed');

        bannerName.textContent = `IT'S ${displayName.toUpperCase()}!`;
        nameBanner.style.opacity = '1';

        titleHighlight.textContent = displayName.toUpperCase() + '!';

        setInputsEnabled(false);

        // Show the NEXT button so player controls when to advance
        nextBtn.classList.remove('hidden');
        nextBtn.classList.add('flex');
    }

    // Fix #2: player presses NEXT — this triggers the actual round transition
    function handleNext() {
        if (!pendingNext) {
            showGameOver(currentScore);
            return;
        }

        // Hide NEXT button immediately
        nextBtn.classList.add('hidden');
        nextBtn.classList.remove('flex');

        // Fade sprite out, swap, fade in
        pokeSprite.style.opacity = '0';
        setTimeout(() => {
            pokeSprite.src = pendingNext.sprite_url;
            pokeSprite.classList.add('silhouette');
            pokeSprite.classList.remove('revealed');
            pokeSprite.style.opacity = '1';
            pendingNext = null;
        }, 300);

        // Hide name banner
        nameBanner.style.opacity = '0';

        // Reset title
        titleHighlight.textContent = 'POKÉMON?';

        // Re-enable inputs and reset lives to current chancesLeft
        pokeInput.value = '';
        setInputsEnabled(true);
        roundActive = true;

        // Fix #1: lives reset to 3 only on correct guess — handled server-side
        // Here we just sync whatever the server says chances_left is
        updateLives(chancesLeft);

        // Announce new Pokémon
        speakWhosThat();
    }

    function showGameOver(score) {
        finalScore.textContent = score;
        gameOverModal.classList.remove('hidden');
    }

    function setInputsEnabled(enabled) {
        pokeInput.disabled = !enabled;
        guessBtn.disabled  = !enabled;
        // Only re-enable reveal if reveals are still available
        if (revealBtn && REVEAL_LIMIT !== 0) {
            revealBtn.disabled = !enabled || (REVEAL_LIMIT > 0 && revealsLeft <= 0);
        }
        roundActive = enabled;
    }

    function retryGame() {
        window.location.href = `{{ route('game.play') }}?mode=${MODE}`;
    }

    // ── Guess ─────────────────────────────────────────────────────────────
    async function handleGuess() {
        if (!roundActive) return;
        const val = pokeInput.value.trim();
        if (!val) return;

        const data = await postJSON(GUESS_URL, { guess: val });
        if (data.error) { console.error(data.error); return; }

        if (data.correct) {
            currentScore = data.score;
            scoreCounter.textContent = currentScore;
            // Fix #1: correct guess resets lives to 3
            updateLives(data.chancesLeft);
            pendingNext = data.next;
            showRevealedState(data.revealed.display_name, '+100 XP');

        } else if (data.game_over) {
            pendingNext = null;
            showRevealedState(data.revealed.display_name, 'WRONG!');
            // Replace NEXT button with game-over trigger
            nextBtn.textContent = '';
            nextBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:28px;">close</span> GAME OVER';
            nextBtn.onclick = () => showGameOver(data.score);

        } else {
            // Fix #1: lives reduced, stays reduced even if reveal is pressed later
            updateLives(data.chancesLeft);
            pokeInput.classList.add('border-error', 'shake');
            setTimeout(() => {
                pokeInput.classList.remove('border-error', 'shake');
                pokeInput.value = '';
            }, 500);
        }
    }

    // ── Reveal ────────────────────────────────────────────────────────────
    async function handleReveal() {
        if (!roundActive || REVEAL_LIMIT === 0) return;

        const data = await postJSON(REVEAL_URL, {});
        if (data.error) { console.error(data.error); return; }

        // Fix #1: do NOT reset lives — server no longer resets them either
        // Just update the reveal counter
        updateRevealButton(data.revealsLeft);

        pendingNext = data.next;
        showRevealedState(data.revealed.display_name, 'NO POINTS');
    }

    // ── Event listeners ───────────────────────────────────────────────────
    guessBtn.addEventListener('click', handleGuess);
    if (revealBtn && REVEAL_LIMIT !== 0) {
        revealBtn.addEventListener('click', handleReveal);
    }
    pokeInput.addEventListener('keypress', e => { if (e.key === 'Enter') handleGuess(); });
</script>
</body>
</html>
