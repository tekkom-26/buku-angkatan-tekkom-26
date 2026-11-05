# Web Buku Angkatan

Web buku angkatan digital untuk mengenal seluruh mahasiswa satu angkatan — profil, galeri foto, dan statistik angkatan — dengan pengaturan privasi berlapis (guest / student / admin).

## Tech Stack

| Layer | Teknologi |
| :--- | :--- |
| Framework | Laravel 13 |
| Admin Panel | Filament v5 (Livewire v4 + Tailwind v4) |
| Database | PostgreSQL (Supabase) — *SQLite untuk dev lokal* |
| ORM | Eloquent |
| Storage | Cloudflare R2 (S3-compatible) |
| Email | Resend |
| Auth | Laravel Sanctum |
| Frontend | Blade Templates + Livewire + Alpine.js + Tailwind CSS v4 |
| Deployment | Vercel (FrankenPHP Container) |

## Strict Development Constraints & Guidelines

Seluruh developer dan AI coding agents **WAJIB** mematuhi 4 aturan mutlak berikut:

1. **Wajib Sync dengan Branch `main` Terbaru Sebelum Mulai Coding:**
   - Selalu tarik dan sinkronkan branch lokal dengan `origin/main` (`git fetch origin main && git checkout -b feature/... origin/main`) sebelum memulai pekerjaan untuk menghindari lockfile desync dan conflict.
2. **Dilarang Keras Memakai Arbitrary Hex (`#...`) di Class Tailwind:**
   - Dilarang menuliskan warna heksadesimal langsung di markup/class Tailwind (misal: `bg-[#80bab2]`, `text-[#514051]`).
   - Wajib gunakan **Semantic Tokens** yang bersumber dari `:root` CSS variables sesuai [`docs/DOCS_COLOR_CONVENTION.md`](docs/DOCS_COLOR_CONVENTION.md):
     * **Primary Family:** `text-primary-900`, `text-primary-800`, `text-primary-700`, `text-primary-500`, `bg-primary-900`.
     * **Secondary Family:** `bg-secondary-500`, `text-secondary-900`, `text-secondary-700`, `border-secondary-300`.
     * **Highlight Family:** `bg-highlight-500`, `text-highlight-900`, `bg-highlight-100`.
     * **Surface Family:** `bg-surface-canvas`, `bg-surface-card`, `bg-surface-subtle`, `border-surface-border`.
3. **Stack Terkunci pada Laravel + Livewire + Blade + Alpine.js:**
   - Dilarang membuat atau mengimpor komponen React / Vue / TSX / JSX. Seluruh arsitektur frontend berbasis file Blade (`.blade.php`), Livewire Components, dan Alpine.js.
4. **Pemisahan Peran UI: Alpine.js vs Livewire:**
   - **Alpine.js**: Digunakan untuk *client-side micro-interactions* murni tanpa request server (contoh: toggle switch foto formal vs casual, copy-to-clipboard status, modal/drawer toggle).
   - **Livewire**: Digunakan untuk *server state management*, fetch/search data dinamis, mutasi backend, dan interaksi formulir.

> Detail panduan agen AI dan otomatisasi dapat dilihat di [`AGENTS.md`](AGENTS.md).

## Dokumentasi

Source of Truth ada di folder [`docs/`](docs/):

| Dokumen | Isi |
| :--- | :--- |
| [`AGENTS.md`](AGENTS.md) | Aturan, strict constraints, dan pedoman kerja untuk AI agent & developer |
| [`DOCS_COLOR_CONVENTION.md`](docs/DOCS_COLOR_CONVENTION.md) | Pedoman semantic design tokens & konvensi warna Tailwind |
| [`ARCHITECTURE.md`](docs/ARCHITECTURE.md) | Arsitektur, ERD, RBAC, auth flow, storage |
| [`API_CONTRACTS.md`](docs/API_CONTRACTS.md) | Spesifikasi endpoint + JSON contract |
| [`SEEDING_GUIDE.md`](docs/SEEDING_GUIDE.md) | Migrasi data dari CSV Google Form |
| [`IMPLEMENTATION_PLAN.md`](docs/IMPLEMENTATION_PLAN.md) | 13 Epic + task checklist |
| [`PROJECT_PLAN.md`](docs/PROJECT_PLAN.md) | Roadmap milestone |
| [`CONTRIBUTING.md`](docs/CONTRIBUTING.md) | Git flow & konvensi |
| [`MEETING_PLAN.md`](docs/MEETING_PLAN.md) | Agenda rapat perdana |

## Setup Lokal

### Prasyarat

- PHP 8.3+
- Composer
- Node.js 20+ (untuk build asset Filament bila perlu)

### Langkah

```bash
# 1. Install dependency
composer install

# 2. Salin env & generate key
cp .env.example .env
php artisan key:generate

# 3. Siapkan database (dev cepat pakai SQLite)
touch database/database.sqlite
php artisan migrate

# 4. Jalankan server
php artisan serve
```

Buka `http://localhost:8000` untuk situs publik, dan `http://localhost:8000/admin` untuk panel Filament.

### Konfigurasi PostgreSQL (Supabase)

Untuk terhubung ke Supabase, isi variabel berikut di `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=<host-supabase>
DB_PORT=6543            # Transaction Pooler (runtime)
DB_DATABASE=postgres
DB_USERNAME=<user>
DB_PASSWORD=<password>
DB_SSLMODE=require
```

> ⚠️ Koneksi PostgreSQL membutuhkan ekstensi PHP `pdo_pgsql`. Install via:
> ```bash
> sudo apt install php8.3-pgsql php8.3-bcmath
> ```

### Konfigurasi Cloudflare R2

Isi `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_ENDPOINT`, dan `R2_PUBLIC_DOMAIN` di `.env`.

### Konfigurasi Resend

Isi `RESEND_API_KEY` dan set `MAIL_MAILER=resend` di `.env`.

## Struktur Project

```
app/
├── Enums/          # ClassEnum, ReligionEnum
├── Filament/       # Filament v5 Resources & Pages
├── Http/           # Controllers, Middleware, Requests, Resources
├── Models/         # Eloquent models
├── Policies/       # Authorization
├── Services/       # AppConfigService, OtpService, AuditService
├── Mail/           # OtpMail
└── Helpers/        # sanitizers.php

docs/               # Dokumentasi (Source of Truth)
database/
├── migrations/
├── seeders/
└── factories/
resources/views/    # Blade views
routes/             # web.php, api.php, console.php
```

## Kontribusi

Lihat [`docs/CONTRIBUTING.md`](docs/CONTRIBUTING.md) untuk Git flow, konvensi commit, dan aturan penulisan kode.
