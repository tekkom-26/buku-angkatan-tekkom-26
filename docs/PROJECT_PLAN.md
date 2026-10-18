# Backend Development & Implementation Roadmap

Project plan & checklist pengerjaan backend **Web Buku Angkatan**.

---

## Roadmap Overview

```
Milestone 0: Infra  ➔  A: Public  ➔  B: Passcode (student)  ➔  C: Admin (OTP)  ➔  D-1: CSV Ingestion (16–18 Okt)  ➔  D-2: Deploy (24–25 Okt)
```

Model akses: **3 role** — `guest` (landing saja), `student` (passcode → directory/galeri/detail), `admin` (whitelist email + OTP → panel Filament + data darurat).

Stack: **Laravel 13 + Filament v5 + Sanctum + PostgreSQL (Supabase) + Cloudflare R2 + Resend + Vercel**.

---

## Checklist Implementasi

### Milestone 0: Infrastructure, Schema & Security Baseline
- [ ] Buat dokumen perencanaan (`docs/` folder & `README.md`).
- [ ] Setup Project Supabase (PostgreSQL DB).
- [ ] Setup Bucket Cloudflare R2 (`buku-angkatan-assets`) & Custom Domain CDN / Public R2 URL.
- [ ] Konfigurasi CORS pada Cloudflare R2 bucket untuk presigned upload URL.
- [ ] Setup Resend (API key + verifikasi domain pengirim).
- [ ] Init Laravel 13: `composer create-project laravel/laravel .`
- [ ] Install Filament v5: `composer require filament/filament` + `php artisan filament:install --panels`.
- [ ] Install Laravel Sanctum: `composer require laravel/sanctum`.
- [ ] Install Resend driver: `composer require resend/resend-laravel`.
- [ ] Buat file `.env.example` dan `.env`.
- [ ] Config `config/database.php` untuk Supabase PostgreSQL.
- [ ] Config `config/filesystems.php` untuk Cloudflare R2 (S3 driver).
- [ ] Config `config/sanctum.php` untuk stateful domains + cookie settings.
- [ ] Definisikan semua Laravel Migrations: `students` (tabel tunggal), `app_config`, `admin_whitelist`, `admin_otp`, `audit_logs`.
- [ ] Jalankan `php artisan migrate` ke Supabase menggunakan direct connection.
- [ ] Jalankan SQL Script RLS (Section 3 ARCHITECTURE.md) — tolak akses `anon`/`authenticated`.
- [ ] Buat PHP Enums (`ClassEnum`, `ReligionEnum`) + semua Eloquent Models dengan relationships.
- [ ] Tulis Form Request classes di `app/Http/Requests/`.
- [ ] Buat helper `app/Services/AuditService.php` (tulis `audit_logs`).
- [ ] Buat helper `app/Services/AppConfigService.php` (baca/tulis `app_config`: passcode & `auth_epoch`).

---

### Milestone A: Public Access (guest)
- [ ] Controller `PublicController@index` (GET / : query statistik agregat → kirim ke `landing.blade.php`).
- [ ] Controller `PasscodeController@showForm` (GET /unlock : tampilkan form passcode).
- [ ] Blade view `landing.blade.php` (hero section, statistik total & per kelas, pengenalan singkat).
- [ ] Blade view `unlock.blade.php` (form input passcode, submit via JS fetch → POST /api/auth/passcode).
- [ ] Pastikan **tidak ada** data personal yang bocor di halaman public.

---

### Milestone B: Passcode Access (student)
Fokus: passcode gate + halaman internal (Blade SSR). Controller query DB → kirim data ke View. Tidak ada API GET untuk data mahasiswa.
- [ ] Seed passcode awal + `auth_epoch` ke `app_config` via `AuthSeeder` (bcrypt hash).
- [ ] Service `AppConfigService` untuk baca/tulis config + cache.
- [ ] Middleware `CheckAuthEpoch` (validasi token epoch vs current epoch).
- [ ] Middleware `EnsureStudentRole` (cek Sanctum token ability = student).
- [ ] Controller `PasscodeController@verify` (POST /api/auth/passcode: verify bcrypt + create Sanctum token) + **rate limiting**.
- [ ] Controller `PasscodeController@logout` (DELETE /api/auth/passcode: revoke token).
- [ ] Controller `PasscodeController@session` (GET /api/auth/session: return current role JSON — satu-satunya GET API).
- [ ] Registrasi middleware alias di `bootstrap/app.php`.
- [ ] Controller `DirectoryController@index` (GET /directory : query DB → kirim ke `directory.blade.php`, pagination, filter, search, select kolom publik saja).
- [ ] Controller `StudentController@show` (GET /students/{nim} : query detail → kirim ke `students/show.blade.php`, tanpa WA/alamat/darurat).
- [ ] Controller `GalleryController@index` (GET /gallery : query foto → kirim ke `gallery.blade.php`, grid avatar).
- [ ] Eloquent local scope `scopePublic()` pada Model Student (exclude kolom sensitif otomatis).
- [ ] Blade view `unlock.blade.php` (form passcode).
- [ ] Blade view `directory.blade.php` (direktori mahasiswa).
- [ ] Blade view `students/show.blade.php` (detail profil).
- [ ] Blade view `gallery.blade.php` (grid foto).

---

### Milestone C: Admin Access (Whitelist + OTP)
Fokus: login passwordless + panel Filament + data darurat. **Semua mutasi data & upload hanya admin. CRUD mahasiswa dikelola sepenuhnya oleh Filament (tidak perlu API endpoint terpisah).**
- [ ] Buat tabel `admin_whitelist` + seed email pengurus via `AuthSeeder` atau SQL Supabase langsung.
- [ ] Service `OtpService` (generate/hash/verify OTP 6-digit).
- [ ] Mailable `OtpMail` untuk kirim OTP via Resend.
- [ ] Controller `AdminOtpController@requestOtp` (POST /api/auth/admin/request-otp: anti-enumeration, rate-limit, selalu 200).
- [ ] Controller `AdminOtpController@verifyOtp` (POST /api/auth/admin/verify-otp: validasi, create Sanctum token, audit `ADMIN_LOGIN`).
- [ ] Controller `AdminOtpController@logout` (POST /api/auth/admin/logout: revoke token).
- [ ] Middleware `EnsureAdminRole` (cek Sanctum token ability = admin).
- [ ] Integrasi auth Filament panel (custom `authenticateUsing()` / Login page flow OTP).
- [ ] Storage helper (generate presigned URL R2 via `Storage::disk('r2')->temporaryUrl()`).
- [ ] Controller `UploadController@generatePresignedUrl` (POST /api/upload: R2 Presigned PUT URL — **admin-only**).
- [ ] Filament Resource `StudentResource` (CRUD semua kolom student dalam 1 form, audit `MUTATE_STUDENT`).
- [ ] Filament Page `EmergencyContacts` (view-only kontak darurat, audit `READ_EMERGENCIES`).
- [ ] Filament Page `ConfigManagement` (ganti passcode + increment `auth_epoch`, audit `ROTATE_PASSCODE`).
- [ ] Filament Resource `AuditLogResource` (view-only audit logs, filter by action/actor).
- [ ] Filament Resource `AdminWhitelistResource` (manage whitelist, audit `WHITELIST_CHANGE`).
- [ ] Filament Dashboard widgets (statistik angkatan, recent audit).
- [ ] Export action di `StudentResource` (CSV/XLSX termasuk data darurat).
- [ ] Uji alur cabut akses: `is_active=false` + increment epoch → sesi langsung mati.
- [ ] Scheduled command cleanup OTP expired.

---

### Milestone D-1: CSV Ingestion & Data Migration (Fase 1: 16–18 Okt)
> Strategi Fase 1: Isi data Supabase dulu → pengembangan Filament/UI jadi lebih cepat karena datanya nyata.

#### 🎫 Ticket 1: Setup Library & Script Command — Deadline **16 Okt (Malam)**
- [ ] Pastikan `league/csv` terinstall.
- [ ] Buat Artisan Command `SeedFromCsv` via `php artisan make:command SeedFromCsv`.
- [ ] Konfigurasi signature command: `seed:from-csv {file?}`.
- [ ] Tambahkan validasi: cek file CSV ada di folder project.
- [ ] Baca file CSV dan tampilkan jumlah baris di terminal.

#### 🎫 Ticket 2: Parser & Sanitizer Data — Deadline **17 Okt (Siang)**
- [ ] Buat helper file `app/Helpers/sanitizers.php`.
- [ ] Daftarkan ke autoload `composer.json` (`autoload.files`).
- [ ] Implementasi `sanitizeWhatsApp()`: buang non-digit, normalisasi `08...`/`+628...` → `628...`.
- [ ] Implementasi `sanitizeInstagram()`: buang `@`, URL domain, trailing slash.
- [ ] Implementasi `parseBirthDate()`: konversi berbagai format → `YYYY-MM-DD`.
- [ ] Hubungkan sanitizer ke loop pembacaan CSV di command.

#### 🎫 Ticket 3: Image Transfer Handler (Google Drive → R2) — Deadline **17 Okt (Malam)**
- [ ] Pastikan disk `r2` di `config/filesystems.php` sudah benar.
- [ ] Buat fungsi konversi URL Google Drive View/Share → Direct Download Link.
- [ ] Gunakan HTTP Client Laravel untuk download file foto.
- [ ] Upload hasil download ke R2 via `Storage::disk('r2')->put()`.
- [ ] Simpan URL publik R2 ke kolom `avatar_url`.

#### 🎫 Ticket 4: Dry-run & Real Execution — Deadline **18 Okt (Malam)**
- [ ] Tambahkan option `--dry-run` pada command.
- [ ] Jika dry-run aktif: jalankan parser + download tapi wrap dalam `DB::transaction` + `rollBack()`.
- [ ] Tampilkan progress bar di terminal.
- [ ] Uji `--dry-run` dengan data sampel CSV lokal.
- [ ] Eksekusi riil ke Supabase PostgreSQL (tanpa flag).
- [ ] Jalankan `AuthSeeder` untuk seed passcode awal + epoch + admin whitelist.

---

### Milestone D-2: Deployment & Infrastructure (Terpisah)
> Urusan deployment, env, Vercel, dan Supabase pooler dipisah agar tidak mengganggu fokus ingestion.

- [ ] Konfigurasi `vercel.json` + `api/index.php` untuk Vercel PHP deployment.
- [ ] Setup Environment Variables di Vercel (`APP_KEY`, `RESEND_API_KEY`, `R2_*`, `DATABASE_URL`).
- [ ] Test deployment & verifikasi konektivitas Supabase Transaction Pooler (port 6543).
- [ ] Validasi API Contracts & guard (401/403/429) menggunakan Postman / Bruno / cURL.
- [ ] Siapkan mock response data untuk memudahkan tim UI (jika ada).

---

## Milestone Ringkas

| Milestone | Role | Deliverable Utama | Deadline |
| :--- | :--- | :--- | :--- |
| **0** | — | Laravel 13 + Filament v5 setup, DB migrations (5 tabel), Eloquent models, RLS, env | — |
| **A** | guest | Landing Page + stats publik | — |
| **B** | student | Passcode gate, directory, galeri, detail profil | — |
| **C** | admin | Whitelist + OTP (Resend), panel Filament, data darurat, upload, audit log | — |
| **D-1** | — | **CSV Ingestion (4 Tickets): Command → Sanitizer → R2 Upload → Dry-run → Eksekusi riil** | **16–18 Okt** |
| **D-2** | — | Deployment Vercel, env production, validasi API | 24–25 Okt |

---

## Security Hardening Checklist
- [ ] Token `epoch` di Sanctum token metadata dibandingkan dengan `app_config.auth_epoch`.
- [ ] Rate limiting: passcode (5x/15m/IP via Laravel throttle), request-otp (per email & IP), verify-otp (max 5/OTP).
- [ ] Anti email-enumeration pada `request-otp` (selalu 200 generik).
- [ ] Cookie HttpOnly + Secure + SameSite (student=Lax, admin=Strict).
- [ ] Query scoping: WA pribadi, alamat kost, & kontak darurat hanya di Blade view / Filament admin.
- [ ] Model `$hidden` untuk `otp_hash` dan field sensitif.
- [ ] Audit log untuk: login, gagal login, rotasi passcode, baca emergencies, mutasi data.
- [ ] Rotasi `APP_KEY` terdokumentasi sebagai prosedur darurat (bump epoch lebih diutamakan).
