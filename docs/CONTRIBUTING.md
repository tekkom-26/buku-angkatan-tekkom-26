# Developer Contributing Guide

Panduan Git Flow, konvensi commit, dan Vibe Coding rules untuk tim dev & AI Assistant di project **Web Buku Angkatan** (Laravel 13 + Filament v5).

> 📖 **Butuh panduan setup & menjalankan project secara lokal?** Lihat [README.md](../README.md) di root project.

---

## ⚡ Strict Constraints & Guardrails (Mandatory)

Seluruh developer dan AI agents wajib mematuhi aturan baku berikut sebelum membuka Pull Request:
1. **Sync dengan `main` Terbaru:** Selalu jalankan `git fetch origin main` dan pastikan branch berbasis `origin/main` terbaru sebelum mulai pengkodean.
2. **Tanpa Arbitrary HEX di Tailwind:** Dilarang menggunakan `bg-[#...]` atau `text-[#...]`. Wajib memakai semantic tokens (`primary-*`, `secondary-*`, `highlight-*`, `surface-*`) sesuai [DOCS_COLOR_CONVENTION.md](DOCS_COLOR_CONVENTION.md).
3. **Stack Terkunci:** Wajib menggunakan Laravel + Livewire + Blade + Alpine.js (Dilarang keras menggunakan React/Vue/TSX).
4. **Pemisahan Interaktivitas UI:**
   - **Alpine.js**: Untuk *client-side micro-interactions* (toggle foto formal/casual, visual feedback, drawer/modal lokal).
   - **Livewire**: Untuk *server state management*, form handling, mutasi data, dan dynamic backend rendering.

Detail lengkap ada di [`AGENTS.md`](../AGENTS.md) dan [`README.md`](../README.md).

---

## 1. Vibe Coding Rules (AI & Developer Guidelines)

1. **Plan Mode First**:
   - Selalu diskusikan perubahan arsitektur atau skema database terlebih dahulu sebelum melakukan eksekusi file/kode.
2. **Type Safety & Strictness**:
   - Wajib pakai `declare(strict_types=1);` di semua file PHP baru.
   - Selalu definisikan return type & parameter type pada method. Hindari tipe campuran tanpa alasan.
   - Gunakan **PHP Enums** (bukan konstanta string tersebar) untuk nilai tetap seperti `ClassEnum`, `ReligionEnum`.
3. **Privacy First**:
   - Dilarang menampilkan atau mengekspos field sensitif (`whatsapp_no`, `address`, `emergency_contact_phone`) di komponen UI, Blade view, Filament Resource, maupun response API non-admin.
   - Endpoint non-admin / Blade view wajib memilih kolom secara eksplisit via `select()` atau Eloquent local scope (dilarang select * yang include field sensitif).
4. **Auth & Secrets**:
   - Sesi memakai **Laravel Sanctum** (token di HttpOnly cookie). Jangan pernah menaruh secret/passcode/hash di kode atau log.
   - Perubahan passcode atau pencabutan sesi wajib lewat `app_config` + increment `auth_epoch`.
5. **Audit Everything Sensitive**:
   - Setiap akses data darurat, mutasi data, login, dan rotasi passcode wajib menulis `audit_logs` (via `AuditService`).
6. **Clean Code**:
   - Tulis kode modular, readable, dan ringkas.
   - Hindari boilerplate yang tidak perlu; manfaatkan fitur framework (Form Requests, Policies, Eloquent Scopes).

---

## 2. Directory & File Conventions

Struktur folder project (Laravel 13 + Filament v5, standard Laravel layout):

```
bukang-filament/
├── docs/                          # Documentation (Source of Truth)
│   ├── ARCHITECTURE.md
│   ├── API_CONTRACTS.md
│   ├── SEEDING_GUIDE.md
│   ├── CONTRIBUTING.md
│   ├── PROJECT_PLAN.md
│   ├── IMPLEMENTATION_PLAN.md
│   └── MEETING_PLAN.md
├── app/
│   ├── Enums/                     # ClassEnum, ReligionEnum
│   ├── Filament/                  # Filament v5 Resources & Pages
│   │   ├── Resources/
│   │   └── Pages/
│   ├── Http/
│   │   ├── Controllers/           # Auth, Public, Directory, Student, Gallery, Upload
│   │   ├── Middleware/            # EnsureStudentRole, EnsureAdminRole, CheckAuthEpoch
│   │   └── Requests/              # Form Request validation (untuk endpoint API)
│   ├── Models/                    # Eloquent models
│   ├── Policies/                  # Authorization policies
│   ├── Services/                  # AppConfigService, OtpService, AuditService
│   ├── Mail/                      # OtpMail
│   ├── Jobs/                      # Queue jobs (image upload, dll)
│   └── Helpers/                   # sanitizers.php (autoload via composer files)
├── bootstrap/app.php              # Middleware & exception registration (Laravel 11+)
├── config/                        # database, filesystems, sanctum, mail
├── database/
│   ├── migrations/
│   ├── seeders/                   # DatabaseSeeder, AuthSeeder, StudentSeeder
│   └── factories/
├── resources/views/               # Blade views (landing, unlock, directory, gallery, admin)
├── routes/
│   ├── web.php                    # Blade routes
│   ├── api.php                    # JSON API routes
│   └── console.php
├── api/index.php                  # Vercel entrypoint
├── vercel.json                    # Vercel deployment config
├── .env.example                   # Template Environment Variables
└── README.md
```

> ℹ️ **Catatan**: Project memakai struktur standar Laravel (bukan `src/`). Helper function global di `app/Helpers/` di-autoload melalui `composer.json` bagian `autoload.files`.

---

## 3. Git Flow & Branching Strategy

### Branch Utama
- `main` — branch produksi. **Hanya** menerima merge dari PR yang sudah direview & lolos.
- `develop` — branch integrasi untuk pengembangan harian.

### Branch Kerja (feature/fix)
Buat branch baru dari `develop` untuk setiap pekerjaan:

| Tipe | Pola Nama | Contoh |
| :--- | :--- | :--- |
| Fitur baru | `feat/<deskripsi-singkat>` | `feat/student-directory-controller` |
| Perbaikan bug | `fix/<deskripsi-singkat>` | `fix/whatsapp-sanitizer` |
| Dokumentasi | `docs/<deskripsi-singkat>` | `docs/api-contracts-update` |
| Maintenance | `chore/<deskripsi-singkat>` | `chore/update-deps` |

### Alur Kerja Standar
1. `git checkout develop` → `git pull` (pastikan branch terbaru).
2. `git checkout -b feat/nama-fitur`.
3. Kerjakan perubahan, lalu commit (lihat konvensi di bawah).
4. `git push origin feat/nama-fitur`.
5. Buka **Pull Request** ke `develop`, minta review minimal 1 orang.
6. Setelah di-approve & CI lolos → merge.

### Aturan Pull Request
- Judul PR mengikuti format commit (mis. `feat: add directory controller`).
- Sertakan deskripsi singkat: apa yang diubah & kenapa.
- Pastikan `php artisan test` dan `./vendor/bin/pint --test` lolos sebelum minta review.
- Jangan merge PR ke `main` langsung tanpa lewat `develop` (kecuali hotfix darurat).

---

## 4. Git & Commit Guidelines

Gunakan format **Conventional Commits**:

- `feat:` Fitur baru (misal: `feat: add directory controller with search`)
- `fix:` Bug fix (misal: `fix: sanitize whatsapp starting with 8`)
- `docs:` Pembaruan dokumentasi
- `chore:` Maintenance config / dependency update
- `refactor:` Perubahan kode tanpa mengubah perilaku
- `test:` Menambah / memperbaiki test

**Contoh commit yang baik:**
```
feat: add passcode authentication controller
fix: handle whatsapp number starting with 8
docs: update RBAC section in architecture
```

---

## 5. Code Style & Testing

| Perintah | Fungsi |
| :--- | :--- |
| `./vendor/bin/pint` | Format kode (Laravel Pint) |
| `./vendor/bin/pint --test` | Cek style tanpa mengubah file (CI) |
| `php artisan test` | Jalankan test suite (Pest / PHPUnit) |
| `php artisan migrate --pretend` | Preview SQL migration tanpa eksekusi |
| `php artisan optimize:clear` | Bersihkan cache config/route/view |

**Aturan test:**
- Setiap controller/service baru sebaiknya disertai feature test minimal untuk happy path.
- Test guard (401/403/429) untuk endpoint sensitif wajib ada.
- Jangan commit test yang bergantung pada koneksi DB production.
