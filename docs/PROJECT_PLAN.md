# Backend Development & Implementation Roadmap

Project plan & checklist pengerjaan backend **Web Buku Angkatan**.

---

## Roadmap Overview

```
Milestone 0: Infra  ➔  A: Public  ➔  B: Passcode (student)  ➔  C: Admin (OTP)  ➔  D: Ingestion & Deploy
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
- [ ] Definisikan semua Laravel Migrations: `students`, `student_contacts`, `student_profiles`, `student_emergencies`, `app_config`, `admin_whitelist`, `admin_otp`, `audit_logs`.
- [ ] Jalankan `php artisan migrate` ke Supabase menggunakan direct connection.
- [ ] Jalankan SQL Script RLS (Section 3 ARCHITECTURE.md) — tolak akses `anon`/`authenticated`.
- [ ] Buat PHP Enums (`ClassEnum`, `ReligionEnum`) + semua Eloquent Models dengan relationships.
- [ ] Tulis Form Request classes di `app/Http/Requests/`.
- [ ] Buat helper `app/Services/AuditService.php` (tulis `audit_logs`).
- [ ] Buat helper `app/Services/AppConfigService.php` (baca/tulis `app_config`: passcode & `auth_epoch`).

---

### Milestone A: Public Access (guest)
- [ ] Controller `PublicController@stats` (GET /api/public/stats: statistik agregat — tanpa data personal).
- [ ] Controller `PublicController@classes` (GET /api/classes: rekapitulasi per kelas).
- [ ] Blade view `landing.blade.php` (hero section, statistik, pengenalan singkat).
- [ ] Pastikan **tidak ada** data personal yang bocor di endpoint public.

---

### Milestone B: Passcode Access (student)
- [ ] Seed passcode awal + `auth_epoch` ke `app_config` via `AuthSeeder` (bcrypt hash).
- [ ] Service `AppConfigService` untuk baca/tulis config + cache.
- [ ] Middleware `CheckAuthEpoch` (validasi token epoch vs current epoch).
- [ ] Middleware `EnsureStudentRole` (cek Sanctum token ability = student).
- [ ] Controller `PasscodeController@verify` (POST /api/auth/passcode: verify bcrypt + create Sanctum token) + **rate limiting**.
- [ ] Controller `PasscodeController@logout` (DELETE /api/auth/passcode: revoke token).
- [ ] Controller `PasscodeController@session` (GET /api/auth/session: return current role).
- [ ] Registrasi middleware alias di `bootstrap/app.php`.
- [ ] Controller `DirectoryController@index` (GET /api/students: direktori, pagination, filter, search).
- [ ] Controller `StudentController@show` (GET /api/students/{nim}: detail — **tanpa** WA pribadi & alamat kost).
- [ ] Controller `GalleryController@index` (GET /api/gallery: galeri foto).
- [ ] Blade view `unlock.blade.php` (form passcode).
- [ ] Blade view `directory.blade.php` (direktori mahasiswa).
- [ ] Blade view `students/show.blade.php` (detail profil).
- [ ] Blade view `gallery.blade.php` (grid foto).

---

### Milestone C: Admin Access (Whitelist + OTP)
Fokus: login passwordless + panel Filament + data darurat. **Semua mutasi data & upload hanya admin.**
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
- [ ] Filament Resource `StudentResource` (CRUD students + nested form contacts/profile/emergency, audit `MUTATE_STUDENT`).
- [ ] Filament Page `EmergencyContacts` (view-only kontak darurat, audit `READ_EMERGENCIES`).
- [ ] Filament Page `ConfigManagement` (ganti passcode + increment `auth_epoch`, audit `ROTATE_PASSCODE`).
- [ ] Filament Resource `AuditLogResource` (view-only audit logs, filter by action/actor).
- [ ] Filament Resource `AdminWhitelistResource` (manage whitelist, audit `WHITELIST_CHANGE`).
- [ ] Filament Dashboard widgets (statistik angkatan, recent audit).
- [ ] Export action di `StudentResource` (CSV/XLSX termasuk data darurat).
- [ ] Uji alur cabut akses: `is_active=false` + increment epoch → sesi langsung mati.
- [ ] Scheduled command cleanup OTP expired.

---

### Milestone D: CSV Ingestion & Deployment
- [ ] Install `league/csv`: `composer require league/csv`.
- [ ] Artisan Command `app/Console/Commands/SeedFromCsv.php`.
- [ ] Utility parser CSV & helper sanitasi (`sanitizeWhatsApp`, `sanitizeInstagram`, `parseBirthDate`).
- [ ] Implementasi downloader image Google Drive → Cloudflare R2 uploader via `Storage::disk('r2')`.
- [ ] CLI command `php artisan seed:from-csv {file} --dry-run`.
- [ ] Uji coba import data dummy / real CSV hasil ekspor Google Form.
- [ ] Konfigurasi `vercel.json` + `api/index.php` untuk Vercel PHP deployment.
- [ ] Setup Environment Variables di Vercel (termasuk `APP_KEY`, `RESEND_API_KEY`, `R2_*`).
- [ ] Test deployment & verifikasi konektivitas Supabase Transaction Pooler (`DATABASE_URL`).
- [ ] Validasi API Contracts & guard (401/403/429) menggunakan Postman / Bruno / cURL.
- [ ] Siapkan mock response data untuk memudahkan tim UI (jika ada).

---

## Milestone Ringkas

| Milestone | Role | Deliverable Utama |
| :--- | :--- | :--- |
| **0** | — | Laravel 13 + Filament v5 setup, DB migrations, Eloquent models, RLS, env |
| **A** | guest | Landing Page + stats publik |
| **B** | student | Passcode gate, directory, galeri, detail profil |
| **C** | admin | Whitelist + OTP (Resend), panel Filament, data darurat, upload, audit log |
| **D** | — | Seeder CSV (Artisan command), deploy Vercel, kontrak API siap UI |

---

## Security Hardening Checklist
- [ ] Token `epoch` di Sanctum token metadata dibandingkan dengan `app_config.auth_epoch`.
- [ ] Rate limiting: passcode (5x/15m/IP via Laravel throttle), request-otp (per email & IP), verify-otp (max 5/OTP).
- [ ] Anti email-enumeration pada `request-otp` (selalu 200 generik).
- [ ] Cookie HttpOnly + Secure + SameSite (student=Lax, admin=Strict).
- [ ] Query scoping: WA pribadi, alamat kost, & `student_emergencies` hanya di endpoint/resource admin.
- [ ] Model `$hidden` untuk `otp_hash` dan field sensitif.
- [ ] Audit log untuk: login, gagal login, rotasi passcode, baca emergencies, mutasi data.
- [ ] Rotasi `APP_KEY` terdokumentasi sebagai prosedur darurat (bump epoch lebih diutamakan).
