# Livewire 4 Demo — Product Catalog

Demo application showcasing Livewire 4, FluxUI, and Blaze compiler in a TALL stack.

## Stack

- **Laravel** 13.15
- **Livewire** 4.3 + Volt (single-file components)
- **FluxUI** 2.14
- **Blaze** 1.0 (Blade compiler replacement, 20× faster rendering)
- **SQLite** — zero-config local database
- **Pest** — tests

## Features demonstrated

| Feature | Implementation |
|---|---|
| Reactive state | `$search`, `$categoryId` — `wire:model.live` |
| Computed properties | `#[Computed]` — `totalCount`, `filteredCount`, `hasMore`, `categories` |
| Event dispatch | Click card → `dispatch('product-selected')` |
| Event listener | `#[On('product-selected')]` in `ProductDetail` component |
| `wire:intersect` | Sentinel div triggers `loadMore()` on scroll |
| FluxUI components | `flux:card`, `flux:button`, `flux:modal`, `flux:input`, `flux:select` |
| Search & filter | Live search + category filter, resets list on change |
| Lazy loading | 6 products per batch, skeleton cards while loading |

## Running locally

```bash
composer install
npm ci && npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
composer run dev        # starts PHP + Vite dev server
```

Open [http://localhost:8000](http://localhost:8000) — log in with `test@example.com` / `password`.

## Running with Docker

```bash
docker compose up -d
docker compose exec app php artisan migrate --seed
```

Open [http://localhost:8080](http://localhost:8080).

## Tests

```bash
php artisan test
# or inside Docker:
docker compose exec app php artisan test tests/Feature/ProductListTest.php tests/Feature/ProductDetailTest.php
```
