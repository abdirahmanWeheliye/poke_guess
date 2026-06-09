<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>POKÉQUIZ – Pokédex</title>
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
                    "on-tertiary-container": "#b70007", "surface": "#fbf9f8", "on-surface": "#1b1c1c",
                    "surface-container": "#f0eded", "surface-container-low": "#f5f3f2",
                    "surface-container-high": "#eae8e7", "surface-container-lowest": "#ffffff",
                    "on-surface-variant": "#4e4632", "outline": "#80765f", "outline-variant": "#d2c5ab",
                    "background": "#fbf9f8", "on-background": "#1b1c1c", "inverse-primary": "#f2c000",
                    "surface-variant": "#e4e2e1", "error": "#ba1a1a",
                },
                fontFamily: {
                    "headline-sm": ["Bricolage Grotesque"], "display-lg-mobile": ["Bricolage Grotesque"],
                    "label-mono": ["JetBrains Mono"], "headline-md": ["Bricolage Grotesque"],
                    "body-md": ["Plus Jakarta Sans"],
                },
                fontSize: {
                    "headline-sm": ["24px", {"lineHeight":"1.2","fontWeight":"700"}],
                    "display-lg-mobile": ["36px", {"lineHeight":"1.1","fontWeight":"800"}],
                    "label-mono": ["14px", {"lineHeight":"1.0","fontWeight":"700"}],
                    "headline-md": ["32px", {"lineHeight":"1.2","fontWeight":"700"}],
                    "body-md": ["16px", {"lineHeight":"1.5","fontWeight":"500"}],
                },
                spacing: { "xl":"64px","lg":"40px","md":"24px","sm":"12px","xs":"4px","base":"8px","border-width":"4px" },
                borderRadius: { "DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px" },
            }
        }
    }
</script>
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .type-badge { display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; font-family: 'JetBrains Mono', monospace; text-transform: uppercase; border: 2px solid #1b1c1c; }
    .card-hover { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .card-hover:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0px 0px rgba(0,0,0,1); }

    /* Type colors */
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
    .type-shadow   { background:#604E82; color:#fff; }
</style>
</head>
<body class="bg-background text-on-background font-body-md">

<!-- Top Nav -->
<nav class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-md py-sm bg-primary border-b-4 border-on-background shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
    <div class="flex items-center gap-sm">
        <a href="{{ route('home') }}"
           class="bg-surface p-xs rounded-lg border-2 border-on-background shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-0.5 hover:translate-y-0.5 hover:shadow-none transition-all">
            <span class="material-symbols-outlined text-on-background">arrow_back</span>
        </a>
        <span class="font-headline-md text-headline-md font-black tracking-tighter text-on-primary">POKÉDEX</span>
    </div>
    <div class="bg-primary-container border-2 border-on-background px-md py-xs rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
        <span class="font-label-mono text-label-mono text-on-primary-container">{{ $pokemons->count() }} POKÉMON</span>
    </div>
</nav>

<!-- Main -->
<main class="pt-[80px] pb-xl px-md max-w-[1200px] mx-auto">

    <!-- Search & Filter Bar -->
    <div class="sticky top-[72px] z-40 bg-background pt-md pb-sm">
        <form method="GET" action="{{ route('pokedex.index') }}" class="flex flex-col md:flex-row gap-sm">
            <!-- Search input -->
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Search by name or number…"
                       class="w-full pl-10 pr-md py-sm font-label-mono text-label-mono bg-surface border-4 border-on-background rounded-lg focus:outline-none focus:border-primary-container transition-colors">
            </div>

            <!-- Generation filter -->
            <select name="gen"
                    class="px-md py-sm font-label-mono text-label-mono bg-surface border-4 border-on-background rounded-lg focus:outline-none focus:border-primary-container cursor-pointer">
                <option value="">ALL GENS</option>
                @for($g = 1; $g <= 9; $g++)
                    <option value="{{ $g }}" {{ $gen == $g ? 'selected' : '' }}>GEN {{ $g }}</option>
                @endfor
            </select>

            <button type="submit"
                    class="bg-primary-container text-on-primary-container font-label-mono text-label-mono px-md py-sm border-4 border-on-background rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all">
                SEARCH
            </button>

            @if($search || $gen)
                <a href="{{ route('pokedex.index') }}"
                   class="bg-surface text-on-surface-variant font-label-mono text-label-mono px-md py-sm border-4 border-on-background rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:shadow-none hover:translate-x-1 hover:translate-y-1 transition-all text-center">
                    CLEAR
                </a>
            @endif
        </form>
    </div>

    <!-- Results count -->
    @if($search || $gen)
        <p class="font-label-mono text-label-mono text-on-surface-variant mt-sm mb-md">
            {{ $pokemons->count() }} results
            @if($search) for "{{ $search }}" @endif
            @if($gen) in Gen {{ $gen }} @endif
        </p>
    @else
        <div class="mt-md mb-md"></div>
    @endif

    <!-- Pokémon Grid -->
    @if($pokemons->isEmpty())
        <div class="flex flex-col items-center justify-center py-xl text-center">
            <span class="material-symbols-outlined text-on-surface-variant" style="font-size:64px;">search_off</span>
            <p class="font-headline-sm text-headline-sm text-on-surface-variant mt-md">No Pokémon found</p>
            <a href="{{ route('pokedex.index') }}" class="mt-md font-label-mono text-label-mono text-secondary underline">Clear search</a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-sm">
            @foreach($pokemons as $pokemon)
                <a href="{{ route('pokedex.show', $pokemon->pokedex_id) }}"
                   class="card-hover bg-surface border-4 border-on-background rounded-xl p-sm flex flex-col items-center gap-xs shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] cursor-pointer">

                    <!-- Sprite -->
                    <div class="w-full aspect-square bg-surface-container rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="{{ $pokemon->sprite_url }}"
                             alt="{{ $pokemon->display_name }}"
                             loading="lazy"
                             class="w-full h-full object-contain p-xs">
                    </div>

                    <!-- Dex number -->
                    <span class="font-label-mono text-label-mono text-on-surface-variant">
                        #{{ str_pad($pokemon->pokedex_id, 3, '0', STR_PAD_LEFT) }}
                    </span>

                    <!-- Name -->
                    <span class="font-label-mono text-label-mono text-on-background text-center leading-tight">
                        {{ $pokemon->display_name }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif

</main>

<!-- Live search (instant filter without page reload) -->
<script>
    const searchInput = document.querySelector('input[name="search"]');
    const cards = document.querySelectorAll('a[href*="/pokedex/"]');

    // Instant client-side filter as you type (in addition to the server-side search)
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase().trim();
        cards.forEach(card => {
            const name = card.querySelector('.font-label-mono:last-child')?.textContent.toLowerCase() ?? '';
            const num  = card.querySelector('.font-label-mono:nth-child(2)')?.textContent.toLowerCase() ?? '';
            card.style.display = (!q || name.includes(q) || num.includes(q)) ? '' : 'none';
        });
    });
</script>

</body>
</html>
