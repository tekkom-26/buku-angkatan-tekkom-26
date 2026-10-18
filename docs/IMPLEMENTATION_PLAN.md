# Implementation Plan — Backend Task Breakdown

Breakdown task implementasi backend **Web Buku Angkatan**, dikelompokkan per **Epic (fitur besar)** dan dipecah menjadi task-task kecil yang dapat di-track.

**Legend status:** `[ ]` todo · `[~]` in progress · `[x]` done
**Legend prioritas:** 🔴 blocker · 🟡 penting · 🟢 nice-to-have

---

## Urutan Pengerjaan (Dependency)

```
EPIC 1 → 2 → 3 → {4, 5, 6} → {7, 8, 9} → {10, 11} → 12 → 13
```

---

## EPIC 1 — Foundation & Infrastructure 🔴
*Scaffold Laravel 13 + Filament v5, DB, env, tooling.*

- [ ] **1.1** Init project Laravel 13 via `composer create-project laravel/laravel .`
- [ ] **1.2** Install Filament v5 via `composer require filament/filament` + `php artisan filament:install --panels`
- [ ] **1.3** Buat panel Filament `admin` (`php artisan make:filament-panel admin`) di path `/admin`
- [ ] **1.4** Config `config/database.php` untuk Supabase PostgreSQL (pooler port 6543)
- [ ] **1.5** Config `config/filesystems.php` untuk Cloudflare R2 (S3 driver)
- [ ] **1.6** Install + config Laravel Sanctum (`composer require laravel/sanctum`)
- [ ] **1.7** Config `config/sanctum.php` (stateful domains, cookie settings)
- [ ] **1.8** Install Resend mail driver (`composer require resend/resend-laravel`) + config `config/mail.php`
- [ ] **1.9** Buat `.env.example` + `.env` dengan semua required variables
- [ ] **1.10** Setup Supabase project + copy credentials ke `.env`
- [ ] **1.11** Setup Laravel Pint untuk code style (`composer require laravel/pint --dev`)
- [ ] **1.12** Setup `vercel.json` + `api/index.php` untuk Vercel deployment

## EPIC 2 — Database Schema & Migration 🔴
*Definisi seluruh tabel via Laravel Migration.*

- [ ] **2.1** Migration `students` (bigint PK auto-increment, nim unique, semua kolom mahasiswa dalam 1 tabel)
- [ ] **2.2** Migration `app_config` (key unique, value, updated_by)
- [ ] **2.3** Migration `admin_whitelist` (email unique lowercase, is_active)
- [ ] **2.4** Migration `admin_otp` (email indexed, otp_hash, expires_at, attempts)
- [ ] **2.5** Migration `audit_logs` (action indexed, metadata JSON)
- [ ] **2.6** Jalankan migration `php artisan migrate` ke Supabase
- [ ] **2.7** Tulis & jalankan SQL RLS (tolak `anon`/`authenticated`)

## EPIC 3 — Eloquent Models, Enums & Relationships 🔴
*Model Laravel + casts + enums.*

- [ ] **3.1** PHP Enum `ClassEnum` (A, B, C, D) + `ReligionEnum` (7 nilai)
- [ ] **3.2** Model `Student` (tabel tunggal: semua kolom mahasiswa, casts enum + date + json + boolean)
- [ ] **3.3** Model `AppConfig` (helper getValue/setValue)
- [ ] **3.4** Model `AdminWhitelist` (scope active)
- [ ] **3.5** Model `AdminOtp` (scope valid/unused)
- [ ] **3.6** Model `AuditLog` (append-only, disable updated_at)
- [ ] **3.7** Model `User` (admin auth untuk Filament, atau pakai custom guard)

## EPIC 4 — Validation Layer (Form Requests) 🔴
*Form Request classes + sanitizer helpers.*

- [ ] **4.1** Helper sanitizers di `app/Helpers/sanitizers.php`: `sanitizeWhatsApp`, `sanitizeInstagram`, `parseBirthDate` + autoload via composer `files`
- [ ] **4.2** `StoreStudentRequest` (semua kolom student: nim, name, nickname, gender, class, religion, birth, kontak, sosmed, darurat)
- [ ] **4.3** Auth requests: `PasscodeRequest`, `AdminEmailRequest`, `VerifyOtpRequest`
- [ ] **4.4** Sanitasi via `prepareForValidation()` di masing-masing request

## EPIC 5 — Auth: Angkatan Passcode (Role student) 🔴
*Gerbang akses internal angkatan via Sanctum.*

- [ ] **5.1** Service `AppConfigService` (baca/tulis `app_config` + cache)
- [ ] **5.2** Middleware `CheckAuthEpoch` (validasi token epoch vs current epoch)
- [ ] **5.3** Middleware `EnsureStudentRole` (cek Sanctum ability = student)
- [ ] **5.4** Rate limiter config (5x/15m/IP) via `RateLimiter::for()` di `AppServiceProvider`
- [ ] **5.5** `PasscodeController@verify` (POST /api/auth/passcode: verify bcrypt + create token + set cookie)
- [ ] **5.6** `PasscodeController@logout` (DELETE /api/auth/passcode: revoke token + clear cookie)
- [ ] **5.7** `PasscodeController@session` (GET /api/auth/session: return current role)
- [ ] **5.8** Registrasi middleware alias di `bootstrap/app.php`
- [ ] **5.9** Blade view `unlock.blade.php` (form passcode) + layout

## EPIC 6 — Auth: Admin Whitelist + OTP (Role admin) 🔴
*Login passwordless admin via email OTP (Resend).*

- [ ] **6.1** Service `OtpService` (generate 6-digit + hash + verify + expiry)
- [ ] **6.2** Mailable `OtpMail` (template blade) untuk kirim OTP via Resend
- [ ] **6.3** `AdminOtpController@requestOtp` (POST /api/auth/admin/request-otp: cek whitelist, rate-limit, selalu 200)
- [ ] **6.4** `AdminOtpController@verifyOtp` (POST /api/auth/admin/verify-otp: validasi, create token, audit ADMIN_LOGIN)
- [ ] **6.5** `AdminOtpController@logout` (POST /api/auth/admin/logout: revoke token)
- [ ] **6.6** Middleware `EnsureAdminRole` (cek Sanctum ability = admin)
- [ ] **6.7** Integrasi Filament panel auth: custom `authenticateUsing()` atau Login page untuk flow OTP
- [ ] **6.8** Scheduled command cleanup OTP expired (`php artisan make:command CleanupExpiredOtps`)

## EPIC 7 — Audit & Security Helpers 🟡
*Jejak audit + hardening keamanan.*

- [ ] **7.1** Service `AuditService` (tulis `audit_logs` dengan ip + user_agent)
- [ ] **7.2** Helper ekstrak `ip` + `user_agent` dari request
- [ ] **7.3** Implementasi Token Epoch check di middleware `CheckAuthEpoch`
- [ ] **7.4** API Resource classes untuk scoping field visibility (StudentResource, StudentDetailResource)
- [ ] **7.5** Policies: `StudentPolicy`, `AdminWhitelistPolicy` untuk Filament authorization

## EPIC 8 — Public Pages & API (Landing Page) 🟡
*Data agregat tanpa personal info.*

- [ ] **8.1** `PublicController@stats` (GET /api/public/stats: total students, per class)
- [ ] **8.2** `PublicController@classes` (GET /api/classes: breakdown per class)
- [ ] **8.3** Blade view `landing.blade.php` (hero, stats agregat) + layout
- [ ] **8.4** Standard API response trait (`ApiResponse`: success/error/meta)
- [ ] **8.5** Global exception handler transform (ValidationException → 422 format)

## EPIC 9 — Protected Pages & API (Directory, Detail, Gallery) 🟡
*Konten internal untuk student (middleware `student`).*

- [ ] **9.1** `DirectoryController@index` (GET /api/students: pagination + filter class + search)
- [ ] **9.2** `StudentController@show` (GET /api/students/{nim}: detail — **tanpa** WA pribadi & alamat kost)
- [ ] **9.3** `GalleryController@index` (GET /api/gallery: foto formal + informal)
- [ ] **9.4** Blade view `directory.blade.php` (direktori dengan filter & search)
- [ ] **9.5** Blade view `students/show.blade.php` (detail profil)
- [ ] **9.6** Blade view `gallery.blade.php` (grid galeri foto)
- [ ] **9.7** Response shaping per role via API Resources

## EPIC 10 — Admin Panel Filament v5 (CRUD, Config, Audit) 🟡
*Kontrol penuh admin via Filament Resources & Pages.*

- [ ] **10.1** Filament Resource `StudentResource` (`--generate --view`): CRUD semua kolom student dalam 1 form/tab
- [ ] **10.2** Filament Page `EmergencyContacts` (view-only: WA darurat & alamat kost, audit READ_EMERGENCIES)
- [ ] **10.3** Filament Page `ConfigManagement` (ganti passcode + bump epoch, audit ROTATE_PASSCODE)
- [ ] **10.4** Filament Resource `AuditLogResource` (view-only, filter action/actor/tanggal)
- [ ] **10.5** Filament Resource `AdminWhitelistResource` (manage whitelist, audit WHITELIST_CHANGE)
- [ ] **10.6** Filament Dashboard widgets (statistik angkatan, recent audit)
- [ ] **10.7** Export action di `StudentResource` (CSV/XLSX termasuk data darurat)
- [ ] **10.8** Policy wiring untuk semua resource (viewAny/create/update/delete)

## EPIC 11 — Storage: Cloudflare R2 Upload 🟡
*Upload/ganti foto (admin-only) via presigned URL atau Filament FileUpload.*

- [ ] **11.1** Storage helper (generate presigned URL via `Storage::disk('r2')->temporaryUrl()`)
- [ ] **11.2** Setup bucket R2 + CORS + custom domain CDN
- [ ] **11.3** `UploadController@generatePresignedUrl` (POST /api/upload: presigned PUT, expired 5 menit, admin-only)
- [ ] **11.4** Validasi tipe/ukuran file + naming convention (`formal/{nim}.jpg`, `informal/{nim}.jpg`)
- [ ] **11.5** Integrasi `FileUpload` Filament di StudentResource (disk r2, directory avatars)

## EPIC 12 — CSV Ingestion & Data Migration (4 Task Tickets) 🟢
*Migrasi data Google Form ke Supabase via Artisan Command. Fase 1: 16–18 Okt.*

### Ticket 1: Setup Library & Script Command Ingestion — Deadline: **16 Okt (Malam)**
- [ ] **12.1** Pastikan `league/csv` terinstall (`composer require league/csv`)
- [ ] **12.2** Buat Artisan Command: `php artisan make:command SeedFromCsv`
- [ ] **12.3** Konfigurasi signature: `seed:from-csv {file?}` + validasi file ada
- [ ] **12.4** Baca file CSV dan tampilkan jumlah baris di terminal

### Ticket 2: Parser & Sanitizer Data — Deadline: **17 Okt (Siang)**
- [ ] **12.5** Buat helper `app/Helpers/sanitizers.php` + autoload composer
- [ ] **12.6** Implementasi `sanitizeWhatsApp()` → normalisasi ke format `628...`
- [ ] **12.7** Implementasi `sanitizeInstagram()` → bersihkan @, URL, trailing slash
- [ ] **12.8** Implementasi `parseBirthDate()` → konversi ke `YYYY-MM-DD`
- [ ] **12.9** Hubungkan sanitizer ke loop pembacaan CSV

### Ticket 3: Image Transfer Handler (Google Drive → R2) — Deadline: **17 Okt (Malam)**
- [ ] **12.10** Konfirmasi disk `r2` di `config/filesystems.php` sudah terkonfigurasi
- [ ] **12.11** Fungsi konversi URL Google Drive View/Share → Direct Download Link
- [ ] **12.12** Download file foto via HTTP Client Laravel
- [ ] **12.13** Upload ke R2 via `Storage::disk('r2')->put()` + simpan URL publik

### Ticket 4: Dry-run & Real Execution — Deadline: **18 Okt (Malam)**
- [ ] **12.14** Tambahkan option `--dry-run` pada command (simulasi tanpa tulis DB)
- [ ] **12.15** Tampilkan progress bar di terminal saat proses seeding
- [ ] **12.16** Uji `--dry-run` dengan data sampel CSV lokal
- [ ] **12.17** Eksekusi riil ke Supabase PostgreSQL (tanpa flag dry-run)
- [ ] **12.18** Seeder `AuthSeeder` untuk seed passcode awal + epoch + admin whitelist

## EPIC 13 — Deployment & Production Readiness 🟢
*Go-live Vercel & production hardening.*

- [ ] **13.1** Set semua env di Vercel Dashboard (Environment Variables)
- [ ] **13.2** Build & deploy staging ke Vercel
- [ ] **13.3** Verifikasi koneksi pooling Supabase di production (port 6543)
- [ ] **13.4** Uji end-to-end guard (401/403/429) via Postman/Bruno
- [ ] **13.5** Uji Filament admin panel di production (login OTP flow)
- [ ] **13.6** Setup scheduled tasks (OTP cleanup) via Vercel Cron atau manual `php artisan schedule:work`
- [ ] **13.7** `php artisan optimize` (config/route/view cache) + verifikasi di Vercel

---

## Ringkasan Epic

| Epic | Fokus | Prioritas | Bergantung pada |
| :--- | :--- | :---: | :--- |
| 1 | Foundation & Infrastructure | 🔴 | — |
| 2 | Database Schema & Migration (5 tabel) | 🔴 | 1 |
| 3 | Eloquent Models & Enums (1 model Student) | 🔴 | 2 |
| 4 | Validation Layer (Form Requests) | 🔴 | 3 |
| 5 | Auth Passcode (student) | 🔴 | 3, 4 |
| 6 | Auth Admin (Whitelist + OTP) | 🔴 | 3, 4 |
| 7 | Audit & Security Helpers | 🟡 | 5, 6 |
| 8 | Public Pages & API | 🟡 | 4 |
| 9 | Protected Pages & API | 🟡 | 5, 7 |
| 10 | Admin Panel Filament v5 | 🟡 | 6, 7 |
| 11 | Storage R2 Upload | 🟡 | 6, 7 |
| 12 | CSV Ingestion (4 Tickets: 16–18 Okt) | 🔴* | 2, 4, 11 |
| 13 | Deployment & Production | 🟢 | semua |

> *🔴 EPIC 12 diprioritaskan tinggi karena data harus ada di Supabase sebelum pengembangan UI/Filament (Fase 1 strategy).*
