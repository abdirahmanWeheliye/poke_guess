<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>POKÉQUIZ - Home</title>
<!-- Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=JetBrains+Mono:wght@700&family=Plus+Jakarta+Sans:wght@500;600&display=swap" rel="stylesheet">
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "on-surface-variant": "#4e4632",
                    "surface-container-highest": "#e4e2e1",
                    "primary": "#745b00",
                    "on-primary-fixed-variant": "#584400",
                    "on-error-container": "#93000a",
                    "on-tertiary": "#ffffff",
                    "on-primary-container": "#6f5700",
                    "surface": "#fbf9f8",
                    "secondary-fixed-dim": "#a0caff",
                    "inverse-primary": "#f2c000",
                    "inverse-surface": "#303030",
                    "tertiary-fixed-dim": "#ffb4a9",
                    "background": "#fbf9f8",
                    "secondary-container": "#73b4ff",
                    "secondary-fixed": "#d2e4ff",
                    "on-background": "#1b1c1c",
                    "surface-container-high": "#eae8e7",
                    "primary-fixed-dim": "#f2c000",
                    "primary-fixed": "#ffe08d",
                    "inverse-on-surface": "#f3f0f0",
                    "outline-variant": "#d2c5ab",
                    "on-primary-fixed": "#241a00",
                    "secondary": "#0061a5",
                    "on-primary": "#ffffff",
                    "on-secondary": "#ffffff",
                    "on-secondary-fixed-variant": "#00497e",
                    "surface-container-lowest": "#ffffff",
                    "on-error": "#ffffff",
                    "surface-tint": "#745b00",
                    "outline": "#80765f",
                    "on-surface": "#1b1c1c",
                    "surface-container": "#f0eded",
                    "on-tertiary-fixed-variant": "#930004",
                    "on-secondary-container": "#004579",
                    "surface-container-low": "#f5f3f2",
                    "tertiary-container": "#ffc3bb",
                    "error": "#ba1a1a",
                    "on-tertiary-fixed": "#410001",
                    "on-secondary-fixed": "#001c37",
                    "tertiary-fixed": "#ffdad5",
                    "tertiary": "#c00008",
                    "surface-dim": "#dcd9d9",
                    "primary-container": "#ffcb05",
                    "error-container": "#ffdad6",
                    "surface-variant": "#e4e2e1",
                    "surface-bright": "#fbf9f8",
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
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .poke-text-shadow { text-shadow: 4px 4px 0px #000; }
    .difficulty-selected {
        border-color: #1b1c1c !important;
        transform: translate(2px, 2px);
        box-shadow: none !important;
    }
</style>
</head>
<body class="bg-background text-on-background font-body-md overflow-hidden">

<!-- Top App Bar -->
<header class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-md py-sm bg-primary dark:bg-primary-fixed border-b-4 border-on-background shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
    <div class="flex items-center gap-base">
        <span class="material-symbols-outlined text-on-primary dark:text-on-primary-fixed" style="font-size: 32px;">videogame_asset</span>
        <h1 class="font-headline-md text-headline-md font-black tracking-tighter text-on-primary dark:text-on-primary-fixed">POKÉQUIZ</h1>
    </div>
</header>

<!-- Main Game Canvas -->
<main class="relative flex flex-col items-center justify-center min-h-screen w-full max-w-[600px] mx-auto overflow-hidden bg-cover bg-center shadow-2xl"
      style="background-image: url('https://archives.bulbagarden.net/media/upload/thumb/2/21/FRLG_EN_boxart.png/250px-FRLG_EN_boxart.png'); background-size: cover; background-position: center;">

    {{-- Dark overlay for readability --}}
    <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>

    <!-- Highscore Overlay (Top Left inside Canvas) -->
    <div class="absolute top-24 left-md z-10">
        <div class="bg-primary-container border-4 border-on-background p-sm shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center gap-xs">
            <span class="font-label-mono text-label-mono text-on-primary-container">HIGHSCORE:</span>
            <span class="font-headline-sm text-headline-sm text-on-primary-container" id="highscore-display">
                {{ $highscores['easy'] ?? 0 }}
            </span>
        </div>
    </div>

    <!-- Center Content: Title + Mode Buttons -->
    <div class="flex flex-col items-center justify-center z-10 px-md text-center mt-xl">
        <div class="mb-lg animate-bounce">
            <h2 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-primary-container stroke-on-background poke-text-shadow tracking-widest italic"
                style="-webkit-text-stroke: 3px #1b1c1c;">
                POKÉQUIZ
            </h2>
        </div>

        <!-- Difficulty Modes -->
        <div class="w-full space-y-md mt-md">
            <div class="grid grid-cols-1 gap-sm w-full">

                <!-- EASY -->
                <button class="group relative bg-surface border-border-width border-on-background rounded-lg p-md flex flex-col items-start transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-surface-container-low"
                        id="mode-easy" onclick="selectMode('easy', this)">
                    <div class="flex items-center justify-between w-full">
                        <span class="font-headline-sm text-headline-sm text-secondary">EASY</span>
                        <span class="material-symbols-outlined text-secondary">eco</span>
                    </div>
                    <div class="flex items-center justify-between w-full mt-xs">
                        <span class="font-label-mono text-label-mono text-on-surface-variant">(Gen 1 — #001–#151)</span>
                        <span class="font-label-mono text-label-mono text-on-surface-variant">
                            BEST: {{ $highscores['easy'] ?? 0 }}
                        </span>
                    </div>
                </button>

                <!-- MEDIUM -->
                <button class="group relative bg-surface border-border-width border-on-background rounded-lg p-md flex flex-col items-start transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-surface-container-low"
                        id="mode-medium" onclick="selectMode('medium', this)">
                    <div class="flex items-center justify-between w-full">
                        <span class="font-headline-sm text-headline-sm text-primary">MEDIUM</span>
                        <span class="material-symbols-outlined text-primary">bolt</span>
                    </div>
                    <div class="flex items-center justify-between w-full mt-xs">
                        <span class="font-label-mono text-label-mono text-on-surface-variant">(New Gens — #152+)</span>
                        <span class="font-label-mono text-label-mono text-on-surface-variant">
                            BEST: {{ $highscores['medium'] ?? 0 }}
                        </span>
                    </div>
                </button>

                <!-- HARD -->
                <button class="group relative bg-surface border-border-width border-on-background rounded-lg p-md flex flex-col items-start transition-all shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:bg-surface-container-low"
                        id="mode-hard" onclick="selectMode('hard', this)">
                    <div class="flex items-center justify-between w-full">
                        <span class="font-headline-sm text-headline-sm text-tertiary">HARD</span>
                        <span class="material-symbols-outlined text-tertiary">local_fire_department</span>
                    </div>
                    <div class="flex items-center justify-between w-full mt-xs">
                        <span class="font-label-mono text-label-mono text-on-surface-variant">(All Pokémon)</span>
                        <span class="font-label-mono text-label-mono text-on-surface-variant">
                            BEST: {{ $highscores['hard'] ?? 0 }}
                        </span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Play Button -->
        <div class="mt-xl w-full">
            <button class="w-full bg-surface-variant text-outline border-border-width border-outline rounded-xl py-lg px-xl font-display-lg-mobile text-display-lg-mobile transition-all shadow-[0px_4px_0px_0px_rgba(0,0,0,0.2)] flex items-center justify-center gap-md cursor-not-allowed"
                    disabled id="play-button">
                <span class="material-symbols-outlined" style="font-size: 40px;">play_arrow</span>
                PLAY
            </button>
        </div>
    </div>

    <!-- Vignette -->
    <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-background/40 to-transparent"></div>
</main>

<script>
    let selectedMode = null;

    // Update the top-left highscore badge when a mode is picked
    const modeScores = {
        easy:   {{ $highscores['easy'] ?? 0 }},
        medium: {{ $highscores['medium'] ?? 0 }},
        hard:   {{ $highscores['hard'] ?? 0 }},
    };

    function selectMode(mode, element) {
        document.querySelectorAll('[id^="mode-"]').forEach(btn => {
            btn.classList.remove('difficulty-selected', 'bg-primary-container', 'bg-secondary-container', 'bg-tertiary-container');
        });

        selectedMode = mode;
        element.classList.add('difficulty-selected');

        if (mode === 'easy')   element.classList.add('bg-secondary-container');
        if (mode === 'medium') element.classList.add('bg-primary-container');
        if (mode === 'hard')   element.classList.add('bg-tertiary-container');

        // Update highscore badge
        document.getElementById('highscore-display').textContent = modeScores[mode];

        // Enable Play button
        const playBtn = document.getElementById('play-button');
        playBtn.disabled = false;
        playBtn.classList.remove('bg-surface-variant', 'text-outline', 'border-outline', 'cursor-not-allowed');
        playBtn.classList.add('bg-primary-container', 'text-on-primary-container', 'border-on-background',
            'shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]',
            'hover:translate-x-1', 'hover:translate-y-1', 'hover:shadow-none', 'active:scale-95');

        // Wire up click → navigate with mode
        playBtn.onclick = () => {
            window.location.href = `{{ route('game.play') }}?mode=${mode}`;
        };
    }
</script>
</body>
</html>
