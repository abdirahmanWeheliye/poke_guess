<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>POKÉQUIZ – {{ $pokemon->display_name }}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@700;800&family=JetBrains+Mono:wght@700&family=Plus+Jakarta+Sans:wght@500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#745b00", "on-primary": "#ffffff", "primary-container": "#ffcb05",
                    "on-primary-container": "#6f5700", "secondary": "#0061a5", "on-secondary": "#ffffff",
                    "secondary-container": "#73b4ff", "on-secondary-container": "#004579",
                    "tertiary": "#c00008", "on-tertiary": "#ffffff", "tertiary-container": "#ffc3bb",
                    "surface": "#fbf9f8", "on-surface": "#1b1c1c", "surface-container": "#f0eded",
                    "surface-container-low": "#f5f3f2", "surface-container-lowest": "#ffffff",
                    "on-surface-variant": "#4e4632", "outline": "#80765f", "outline-variant": "#d2c5ab",
                    "background": "#fbf9f8", "on-background": "#1b1c1c", "error": "#ba1a1a",
                },
                fontFamily: {
                    "headline-sm": ["Bricolage Grotesque"], "display-lg-mobile": ["Bricolage Grotesque"],
                    "label-mono": ["JetBrains Mono"], "headline-md": ["Bricolage Grotesque"],
                    "body-md": ["Plus Jakarta Sans"],
                },
                fontSize: {
                    "headline-sm": ["24px",{"lineHeight":"1.2","fontWeight":"700"}],
                    "display-lg-mobile": ["36px",{"lineHeight":"1.1","fontWeight":"800"}],
                    "label-mono": ["14px",{"lineHeight":"1.0","fontWeight":"700"}],
                    "headline-md": ["32px",{"lineHeight":"1.2","fontWeight":"700"}],
                    "body-md": ["16px",{"lineHeight":"1.5","fontWeight":"500"}],
                },
                spacing: { "xl":"64px","lg":"40px","md":"24px","sm":"12px","xs":"4px","base":"8px","border-width":"4px" },
                borderRadius: { "DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px" },
            }
        }
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

    /* Type colors */
    .type-badge { display:inline-block; padding:3px 14px; border-radius:9999px; font-size:12px; font-weight:700; font-family:'JetBrains Mono',monospace; text-transform:uppercase; border:2px solid #1b1c1c; }
    .type-fire     { background:#FF9741; color:#fff; }
    .type-water    { background:#3692DC; color:#fff; }
    .type-grass    { background:#38BF4B; color:#fff; }
    .type-electric { background:#FBD100; color:#1b1c1c; }
    .type-psychic  { background:#FF6675; color:#fff; }
    .type-ice      { background:#4CD1C0; color:#fff; }
    .type-dragon   { background:#006FC9; color:#fff; }
    .type-dark     { background:#5B5466; color:#fff; }
    .type-fairy    { background:#FB89EB; color:#1b1c1c; }
    .type-normal   { background:#9FA19F; color:#fff; }
    .type-fighting { background:#FF6821; color:#fff; }
    .type-flying   { background:#89AAE3; color:#fff; }
    .type-poison   { background:#B34DA3; color:#fff; }
    .type-ground   { background:#E87236; color:#fff; }
    .type-rock     { background:#C9BB8A; color:#1b1c1c; }
    .type-bug      { background:#83C300; color:#fff; }
    .type-ghost    { background:#4C6AB2; color:#fff; }
    .type-steel    { background:#60A1B8; color:#fff; }
    .type-unknown  { background:#68A090; color:#fff; }

    /* Stat bar animation */
    .stat-bar-fill { transition: width 1s ease-out; }

    /* Section tabs */
    .tab-btn { transition: all 0.15s ease; }
    .tab-btn.active {
        background: #ffcb05;
        color: #6f5700;
        box-shadow: none;
        transform: translate(2px, 2px);
    }
</style>
</head>
<body class="bg-background text-on-background font-body-md">

<!-- Top Nav -->
<nav class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-md py-sm bg-primary border-b-4 border-on-background shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
    <div class="flex items-center gap-sm">
        <a href="{{ route('pokedex.index') }}"
           class="bg-surface p-xs rounded-lg border-2 border-on-background shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            <span class="material-symbols-outlined text-on-background">arrow_back</span>
        </a>
        <span class="font-headline-md text-headline-md font-black tracking-tighter text-on-primary">POKÉDEX</span>
    </div>
    <!-- Prev / Next navigation -->
    <div class="flex gap-sm">
        @if($pokemon->pokedex_id > 1)
            <a href="{{ route('pokedex.show', $pokemon->pokedex_id - 1) }}"
               class="bg-surface p-xs rounded-lg border-2 border-on-background shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                <span class="material-symbols-outlined text-on-background">chevron_left</span>
            </a>
        @endif
        @if($pokemon->pokedex_id < 1025)
            <a href="{{ route('pokedex.show', $pokemon->pokedex_id + 1) }}"
               class="bg-surface p-xs rounded-lg border-2 border-on-background shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
                <span class="material-symbols-outlined text-on-background">chevron_right</span>
            </a>
        @endif
    </div>
</nav>

<!-- Main -->
<main class="pt-[80px] pb-xl px-md max-w-[900px] mx-auto">

    <!-- Hero Card -->
    <div class="mt-md bg-surface-container-lowest border-4 border-on-background rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">

            <!-- Sprite side -->
            <div class="flex flex-col items-center justify-center p-lg bg-surface-container relative overflow-hidden">
                <!-- Decorative dot pattern -->
                <div class="absolute inset-0 opacity-20"
                     style="background-image:radial-gradient(#1b1c1c 1px,transparent 1px);background-size:20px 20px;"></div>
                <img src="{{ $pokemon->sprite_url }}"
                     alt="{{ $pokemon->display_name }}"
                     class="relative z-10 w-48 h-48 md:w-64 md:h-64 object-contain drop-shadow-2xl">
            </div>

            <!-- Info side -->
            <div class="p-md flex flex-col gap-sm justify-center">
                <div class="flex items-center gap-sm">
                    <span class="font-label-mono text-label-mono text-on-surface-variant">
                        #{{ str_pad($pokemon->pokedex_id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="font-label-mono text-label-mono text-on-surface-variant">
                        GEN {{ $pokemon->generation }}
                    </span>
                </div>

                <h1 class="font-display-lg-mobile text-display-lg-mobile text-on-background leading-none">
                    {{ $pokemon->display_name }}
                </h1>

                <!-- Types -->
                <div class="flex gap-xs flex-wrap">
                    @foreach($data['types'] as $type)
                        <span class="type-badge type-{{ strtolower($type) }}">{{ $type }}</span>
                    @endforeach
                </div>

                <!-- Flavour text -->
                <p class="font-body-md text-body-md text-on-surface-variant italic leading-relaxed">
                    "{{ $data['flavour_text'] }}"
                </p>

                <!-- Height / Weight / Abilities -->
                <div class="grid grid-cols-3 gap-sm mt-xs">
                    <div class="bg-surface-container rounded-lg p-sm text-center border-2 border-outline-variant">
                        <p class="font-label-mono text-label-mono text-on-surface-variant">HEIGHT</p>
                        <p class="font-headline-sm text-headline-sm text-on-background">{{ $data['height'] }}m</p>
                    </div>
                    <div class="bg-surface-container rounded-lg p-sm text-center border-2 border-outline-variant">
                        <p class="font-label-mono text-label-mono text-on-surface-variant">WEIGHT</p>
                        <p class="font-headline-sm text-headline-sm text-on-background">{{ $data['weight'] }}kg</p>
                    </div>
                    <div class="bg-surface-container rounded-lg p-sm text-center border-2 border-outline-variant">
                        <p class="font-label-mono text-label-mono text-on-surface-variant">ABILITY</p>
                        <p class="font-label-mono text-label-mono text-on-background leading-tight">{{ $data['abilities'][0] ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-sm mt-md flex-wrap">
        <button onclick="showTab('stats')"   class="tab-btn active font-label-mono text-label-mono px-md py-sm border-4 border-on-background rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]" id="tab-stats">BASE STATS</button>
        <button onclick="showTab('evo')"     class="tab-btn font-label-mono text-label-mono px-md py-sm border-4 border-on-background rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]" id="tab-evo">EVOLUTION</button>
        <button onclick="showTab('moves')"   class="tab-btn font-label-mono text-label-mono px-md py-sm border-4 border-on-background rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]" id="tab-moves">MOVES</button>
    </div>

    <!-- ── Tab: Base Stats ── -->
    <div id="panel-stats" class="mt-md bg-surface-container-lowest border-4 border-on-background rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] p-md">
        <h2 class="font-headline-sm text-headline-sm text-on-background mb-md">Base Stats</h2>
        @php $maxStat = 255; @endphp
        @foreach($data['stats'] as $stat)
            @php
                $pct = min(100, round(($stat['value'] / $maxStat) * 100));
                $color = match(true) {
                    $stat['value'] >= 100 => '#38BF4B',
                    $stat['value'] >= 70  => '#FBD100',
                    default               => '#FF6675',
                };
            @endphp
            <div class="flex items-center gap-sm mb-sm">
                <span class="font-label-mono text-label-mono text-on-surface-variant w-20 shrink-0">{{ $stat['name'] }}</span>
                <span class="font-label-mono text-label-mono text-on-background w-10 text-right shrink-0">{{ $stat['value'] }}</span>
                <div class="flex-1 bg-surface-container rounded-full h-4 border-2 border-on-background overflow-hidden">
                    <div class="stat-bar-fill h-full rounded-full border-r-2 border-on-background"
                         style="width: 0%; background-color: {{ $color }};"
                         data-target="{{ $pct }}"></div>
                </div>
            </div>
        @endforeach

        @if(!empty($data['stats']))
            @php $total = array_sum(array_column($data['stats'], 'value')); @endphp
            <div class="flex items-center gap-sm mt-md pt-md border-t-2 border-outline-variant">
                <span class="font-label-mono text-label-mono text-on-surface-variant w-20 shrink-0">TOTAL</span>
                <span class="font-headline-sm text-headline-sm text-on-background">{{ $total }}</span>
            </div>
        @endif
    </div>

    <!-- ── Tab: Evolution Chain ── -->
    <div id="panel-evo" class="mt-md bg-surface-container-lowest border-4 border-on-background rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] p-md hidden">
        <h2 class="font-headline-sm text-headline-sm text-on-background mb-md">Evolution Chain</h2>

        @if(empty($data['evolution_chain']))
            <p class="font-body-md text-on-surface-variant">No evolution data available.</p>
        @elseif(count($data['evolution_chain']) === 1)
            <p class="font-body-md text-on-surface-variant">{{ $pokemon->display_name }} does not evolve.</p>
        @else
            <div class="flex flex-wrap items-center gap-md justify-center">
                @foreach($data['evolution_chain'] as $index => $evo)
                    @if($index > 0)
                        <div class="flex flex-col items-center">
                            <span class="material-symbols-outlined text-on-surface-variant" style="font-size:32px;">arrow_forward</span>
                        </div>
                    @endif
                    <a href="{{ $evo['pokedex_id'] ? route('pokedex.show', $evo['pokedex_id']) : '#' }}"
                       class="flex flex-col items-center gap-xs bg-surface-container border-4 border-on-background rounded-xl p-md
                              shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all
                              {{ $evo['name'] === $pokemon->display_name ? 'bg-primary-container' : '' }}">
                        @if($evo['sprite_url'])
                            <img src="{{ $evo['sprite_url'] }}" alt="{{ $evo['name'] }}" class="w-20 h-20 object-contain">
                        @endif
                        <span class="font-label-mono text-label-mono text-on-background">{{ $evo['name'] }}</span>
                        @if($evo['pokedex_id'])
                            <span class="font-label-mono text-label-mono text-on-surface-variant">#{{ str_pad($evo['pokedex_id'], 3, '0', STR_PAD_LEFT) }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ── Tab: Moves ── -->
    <div id="panel-moves" class="mt-md bg-surface-container-lowest border-4 border-on-background rounded-xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] p-md hidden">
        <h2 class="font-headline-sm text-headline-sm text-on-background mb-md">Level-Up Moves</h2>

        @if(empty($data['moves']))
            <p class="font-body-md text-on-surface-variant">No move data available.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-sm">
                @foreach($data['moves'] as $move)
                    <div class="bg-surface-container border-2 border-outline-variant rounded-lg px-sm py-xs">
                        <span class="font-label-mono text-label-mono text-on-background">{{ $move }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</main>

<script>
    // ── Tab switching ──────────────────────────────────────────────────────
    function showTab(name) {
        ['stats', 'evo', 'moves'].forEach(t => {
            document.getElementById('panel-' + t).classList.add('hidden');
            document.getElementById('tab-' + t).classList.remove('active');
        });
        document.getElementById('panel-' + name).classList.remove('hidden');
        document.getElementById('tab-' + name).classList.add('active');
    }

    // ── Animate stat bars on load ──────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('.stat-bar-fill').forEach(bar => {
                bar.style.width = bar.dataset.target + '%';
            });
        }, 200);
    });
</script>

</body>
</html>
