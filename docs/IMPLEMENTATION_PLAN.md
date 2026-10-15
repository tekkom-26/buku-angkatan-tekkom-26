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

- [ ] **2.1** Migration `students` (uuid PK, nim unique, class_name indexed, timestamps)
- [ ] **2.2** Migration `student_contacts` (FK student_id unique, cascade delete)
- [ ] **2.3** Migration `student_profiles` (hobbies JSON, foto URLs)
- [ ] **2.4** Migration `student_emergencies` (FK student_id unique, cascade delete)
- [ ] **2.5** Migration `app_config` (key unique, value, updated_by)
- [ ] **2.6** Migration `admin_whitelist` (email unique lowercase, is_active)
- [ ] **2.7** Migration `admin_otp` (email indexed, otp_hash, expires_at, attempts)
- [ ] **2.8** Migration `audit_logs` (action indexed, metadata JSON)
- [ ] **2.9** Jalankan migration `php artisan migrate` ke Supabase
- [ ] **2.10** Tulis & jalankan SQL RLS (tolak `anon`/`authenticated`)

## EPIC 3 — Eloquent Models, Enums & Relationships 🔴
*Model Laravel + relationships + casts + enums.*

- [ ] **3.1** PHP Enum `ClassEnum` (A, B, C, D) + `ReligionEnum` (7 nilai)
- [ ] **3.2** Model `Student` (hasOne contacts/profile/emergency, casts enum + date)
- [ ] **3.3** Model `StudentContact` (belongsTo student, $hidden untuk field sensitif jika perlu)
- [ ] **3.4** Model `StudentProfile` (belongsTo student, cast hobbies as array)
- [ ] **3.5** Model `StudentEmergency` (belongsTo student)
- [ ] **3.6** Model `AppConfig` (helper getValue/setValue)
- [ ] **3.7** Model `AdminWhitelist` (scope active)
- [ ] **3.8** Model `AdminOtp` (scope valid/unused)
- [ ] **3.9** Model `AuditLog` (append-only, disable updated_at)
- [ ] **3.10** Model `User` (admin auth untuk Filament, atau pakai custom guard)

## EPIC 4 — Validation Layer (Form Requests) 🔴
*Form Request classes + sanitizer helpers.*

- [ ] **4.1** Helper sanitizers di `app/Helpers/sanitizers.php`: `sanitizeWhatsApp`, `sanitizeInstagram`, `parseBirthDate` + autoload via composer `files`
- [ ] **4.2** `StoreStudentRequest` (nim, full_name, nickname, class, religion, birth fields)
- [ ] **4.3** `StoreContactRequest` + `StoreProfileRequest` + `StoreEmergencyRequest`
- [ ] **4.4** Auth requests: `PasscodeRequest`, `AdminEmailRequest`, `VerifyOtpRequest`
- [ ] **4.5** Sanitasi via `prepareForValidation()` di masing-masing request

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

- [ ] **10.1** Filament Resource `StudentResource` (`--generate --view`): CRUD + nested form contacts/profile/emergency
- [ ] **10.2** Filament Page `EmergencyContacts` (view-only kontak darurat, audit READ_EMERGENCIES)
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
- [ ] **11.5** Integrasi `FileUpload` Filament di StudentResource (disk r2, directory formal/informal)

## EPIC 12 — CSV Ingestion & Seeder 🟢
*Migrasi data Google Form via Artisan Command.*

- [ ] **12.1** Install `league/csv` (`composer require league/csv`)
- [ ] **12.2** Artisan Command `app/Console/Commands/SeedFromCsv.php`
- [ ] **12.3** Pipeline sanitasi (sanitizeWhatsApp, sanitizeInstagram, parseBirthDate)
- [ ] **12.4** Downloader image (Google Drive → R2 via Storage facade)
- [ ] **12.5** Batch insert transaksional (`DB::transaction`) untuk students/contacts/profiles/emergencies
- [ ] **12.6** CLI command `php artisan seed:from-csv {file} --dry-run`
- [ ] **12.7** Seeder `AuthSeeder` untuk seed passcode awal + epoch + admin whitelist
- [ ] **12.8** Seeder `StudentSeeder` + Factory untuk dummy data dev

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
| 2 | Database Schema & Migration | 🔴 | 1 |
| 3 | Eloquent Models & Enums | 🔴 | 2 |
| 4 | Validation Layer (Form Requests) | 🔴 | 3 |
| 5 | Auth Passcode (student) | 🔴 | 3, 4 |
| 6 | Auth Admin (Whitelist + OTP) | 🔴 | 3, 4 |
| 7 | Audit & Security Helpers | 🟡 | 5, 6 |
| 8 | Public Pages & API | 🟡 | 4 |
| 9 | Protected Pages & API | 🟡 | 5, 7 |
| 10 | Admin Panel Filament v5 | 🟡 | 6, 7 |
| 11 | Storage R2 Upload | 🟡 | 6, 7 |
| 12 | CSV Ingestion & Seeder | 🟢 | 2, 4, 11 |
| 13 | Deployment & Production | 🟢 | semua |
