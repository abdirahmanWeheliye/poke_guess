# 🎮 PokéQuiz – Laravel Backend

A "Who's That Pokémon?" quiz game built in Laravel.  
Players choose Easy (Gen 1), Medium (Gen 2+), or Hard (All) and guess Pokémon silhouettes.

---

## 📁 File Structure

```
app/
  Http/Controllers/
    HomeController.php        ← Home page (mode select + highscores)
    GameController.php        ← Game session, /game/guess, /game/reveal
  Models/
    Pokemon.php               ← Pokémon model + scopeForMode()
    Highscore.php             ← Highscore model + upsert logic

database/
  migrations/
    ..._create_pokemons_table.php
    ..._create_highscores_table.php
  seeders/
    PokemonSeeder.php         ← Fetches ~1025 Pokémon from PokéAPI
    DatabaseSeeder.php

resources/views/
  home.blade.php              ← Mode selection screen
  game/
    play.blade.php            ← Quiz screen (silhouette + input)

routes/
  web.php                     ← GET /, GET /game, POST /game/guess, POST /game/reveal
```

---

## 🚀 Setup

### 1. Install Laravel (if starting fresh)

```bash
composer create-project laravel/laravel pokequiz
cd pokequiz
```

### 2. Copy the files from this package

Copy all files from this package into your Laravel project, preserving the directory structure shown above.

### 3. Configure your database

Edit `.env`:
```env
DB_CONNECTION=mysql        # or sqlite, pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pokequiz
DB_USERNAME=root
DB_PASSWORD=your_password
```

For a quick SQLite setup:
```env
DB_CONNECTION=sqlite
# DB_DATABASE= (leave blank, it defaults to database/database.sqlite)
```
```bash
touch database/database.sqlite
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Seed the Pokémon database

This fetches all ~1 025 Pokémon from the free PokéAPI (no key needed).  
It takes about 10–20 seconds.

```bash
php artisan db:seed --class=PokemonSeeder
```

You should see: `Seeded 1025 Pokémon successfully!`

### 6. Start the development server

```bash
php artisan serve
```

Visit **http://localhost:8000** and start playing!

---

## 🎮 How It Works

### Routes

| Method | URL           | Description                              |
|--------|---------------|------------------------------------------|
| GET    | `/`           | Home – mode selection, highscores        |
| GET    | `/game?mode=` | Start a new game (easy / medium / hard)  |
| POST   | `/game/guess` | Submit a guess (JSON API)                |
| POST   | `/game/reveal`| Skip & reveal the Pokémon (JSON API)     |

### Game Session (stored in Laravel session)

```php
session('game') = [
    'mode'         => 'easy',       // easy | medium | hard
    'score'        => 0,
    'chances_left' => 3,
    'used_ids'     => [1, 45, 7],   // Pokémon already shown this run
    'current_id'   => 25,           // current Pokémon's DB id
]
```

### Scoring

- ✅ Correct guess → **+100 points**, 3 chances reset, next Pokémon loads
- ❌ Wrong guess → chances decrease; at 0 → **game over**
- 👁 Reveal → no points, next Pokémon loads (chances reset)

### Difficulty Modes

| Mode   | Pokédex range  | DB filter              |
|--------|----------------|------------------------|
| Easy   | #001 – #151    | `generation = 1`       |
| Medium | #152 – #1025   | `generation > 1`       |
| Hard   | All            | no filter              |

### Guess Normalization

The controller normalizes both the player's input and the Pokémon's name:
- Lowercase
- Remove accents (é → e, etc.)
- Strip hyphens, spaces, apostrophes

So "Farfetch'd", "farfetchd", and "FARFETCH D" all count as correct.

---

## 🖼 Pokémon Sprites

Sprites are served directly from the PokéAPI's public GitHub sprites repo:
```
https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/{id}.png
```
No API key or rate-limiting needed.

---

## 📦 Optional: Re-seed or update

To clear and re-seed all Pokémon:
```bash
php artisan db:seed --class=PokemonSeeder
```

To wipe everything and start fresh:
```bash
php artisan migrate:fresh --seed
```
