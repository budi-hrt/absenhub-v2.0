# AbsenHub v2.0 — Agent Guide

Laravel 12 attendance app with face recognition. Tight monolith — all pages are Livewire SFCs.

## Quick start

```bash
composer install
npm install
cp .env.example .env             # then edit DB_* for your MySQL
php artisan key:generate
php artisan storage:link          # employee photos
php artisan migrate
php artisan db:seed --class=RoleAndUserSeeder
# Face models → put face-api.js models in public/models/
composer run dev                  # runs serve + queue + logs + vite concurrently
```

## Architecture

| Layer | What | Where |
|-------|------|-------|
| **Pages** | Livewire SFC (anonymous class in blade) | `resources/views/pages/{area}/` |
| **Class components** | Multi-file Livewire | `app/Livewire/` |
| **Sidebar** | Livewire SFC | `resources/views/components/⚡sidebar-menu.blade.php` |
| **Layouts** | `layouts::app` (default), `layouts::empty` (login) | `resources/views/layouts/` |
| **Routes** | `Route::livewire()` only, group-per-role | `routes/web.php` |
| **Models** | 10 Eloquent models, `$guarded = []` pattern | `app/Models/` |
| **RBAC** | Spatie Permission, 5 roles | `config/permission.php` |

## Roles & route guards

- `karyawan` → `/dashboard` only
- `admin|super-admin|operator|manager` → `/users`, `/karyawan` CRUD
- `super-admin` → `/roles`, `/permissions`
- Login redirect: karyawan → `/dashboard`, others → `/`

## UI stack

- **Tailwind CSS v4** + **daisyUI 5** + **maryUI 2** — tailwind config is in CSS, not JS
- Page layout uses `<x-header>`, `<x-card>`, `<x-table>`, `<x-modal>`, `<x-form>`, `<x-button>` (maryUI)
- Theme toggle (emerald/dark) in sidebar, stored in localStorage
- SPA navigation via `wire:navigate` on all internal links

## Livewire conventions

- All SFC pages use the pattern: `<?php ... new #[Layout('layouts.app')] class extends Component { ... }; ?>` then blade HTML
- `#[Computed]` for database queries (e.g. `karyawans()` method), values returned via `with()`
- `use Toast, WithPagination;` common traits
- Filter properties use `wire:model.live` + `updatingX()` → `$this->resetPage()`
- `Route::livewire('/path', 'pages::name')` naming convention matches `resources/views/pages/`

## Databases

- **Main**: MySQL `absen_v2` (DB_CONNECTION=mysql)
- **Legacy**: MySQL `db_absensi` via `mysql_lama` connection (for migration only)
- Cache + queue + session use `database` driver (unless overridden in .env)

## Key commands

```bash
composer run dev                  # dev server + queue + logs + vite (concurrent)
php artisan app:migrate-data      # pull data from legacy CI3 db_absensi
php artisan db:seed --class=RoleAndUserSeeder  # admin@mail.com / password
```

## Testing

```bash
php artisan test                  # PHPUnit (Unit + Feature)
```

Tests use in-memory SQLite by default (DB_CONNECTION=sqlite in phpunit.xml, but currently commented out). No test fixtures or factories for domain models yet.

## Code style

- **Laravel Pint**: custom rules + 4-space indent (`.pint.json`)
- **Blade**: `shufo.vscode-blade-formatter`, 4-space indent, 100 char width (`.bladeformatterrc.json`)
- **PHP**: `bmewburn.vscode-intelephense-client`, formatting off (save handled by blade formatter)
- All models use `protected $guarded = []` (no fillable whitelist)
- Face descriptor stored as JSON cast (`'face_descriptor' => 'array'`)
- Indices: sort by `nama_karyawan` ascending

## Face recognition

- `face-api.js` loads models from `/public/models/` (tinyFaceDetector + faceLandmark68TinyNet + faceRecognitionNet)
- Exported as `window.initFaceModels` and `window.getDescriptorFromBlob`
- Descriptor stored on `User` model as array, used for face match at attendance

## Gotchas

- SFCs use `⚡` emoji prefix for some files — stick to the namespace convention (e.g. `pages::users.⚡karyawan`)
- Nonaktif flow: creates `Nonaktif` record then sets `is_active=false, status_id=3`
- Migrations use specific dates (2026_07_11_*), not generic timestamps
- `composer run dev` sets `Composer\Config::disableProcessTimeout` — may block terminal until stopped
- `npm run build` before deployment for Vite assets
