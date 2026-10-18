# API Contracts & Specification

Dokumen kontrak API untuk backend **Web Buku Angkatan** (Laravel 13 + Filament v5).

> **Prinsip arsitektur**: Karena menggunakan **Filament v5** untuk admin dan **Blade Templates** untuk publik/student, komunikasi via API JSON **diminimalkan**. Request **GET** mengembalikan **halaman HTML (Blade View)** langsung dari server. Hanya request **mutasi (POST / PUT / PATCH / DELETE)** dan **endpoint autentikasi** yang mengembalikan **response JSON**.

---

## 1. Access Level Legend

| Simbol | Level | Deskripsi |
| :---: | :--- | :--- |
| 🔓 | **Public** | Tanpa autentikasi (`guest`) |
| 🔐 | **Protected** | Butuh Angkatan Passcode (`role = student` atau `admin`) |
| 🛡️ | **Admin Only** | Butuh sesi admin (whitelist email + OTP) |

Hierarki: `guest < student < admin`. Admin otomatis lolos guard 🔐.

**Status code guard:**
- `401 Unauthorized` — belum terautentikasi / sesi dicabut (epoch mismatch).
- `403 Forbidden` — terautentikasi tapi role kurang.
- `429 Too Many Requests` — rate limit.

---

## 2. Global JSON Response Format

> Format ini **hanya berlaku untuk endpoint API yang mengembalikan JSON** (auth, mutasi, upload). Request GET ke route web mengembalikan HTML Blade.

### Success Response
```json
{
  "success": true,
  "data": {}
}
```

### Error Response
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "whatsapp_no": ["Invalid WhatsApp number format. Must start with 628"]
    }
  }
}
```

> **Laravel validation error** (422) di-transform ke format di atas via exception handler. Berlaku untuk request API saja; request web menampilkan error blade bawaan Laravel.

### Guard Error Responses
```json
// 401 — passcode belum dimasukkan / sesi kedaluwarsa / dicabut
{ "success": false, "error": { "code": "UNAUTHORIZED", "message": "Angkatan passcode required" } }

// 403 — role kurang (student akses admin-only)
{ "success": false, "error": { "code": "FORBIDDEN", "message": "Admin access required" } }
```

---

## 3. Endpoint API (JSON Response)

> Hanya endpoint **mutasi & autentikasi** yang mengembalikan JSON. Semua endpoint di bawah terdaftar di `routes/api.php`.

### 3.1 Student — Angkatan Passcode

#### 🔓 `POST /api/auth/passcode`
Verifikasi Angkatan Passcode, create Sanctum token + HttpOnly cookie.

- **Request Body**: `{ "passcode": "angkatan2022secret" }`
- **Response `200 OK`**:
```json
{ "success": true, "data": { "role": "student", "expires_in": 604800 } }
```
- **Response `401 Unauthorized`**:
```json
{ "success": false, "error": { "code": "INVALID_PASSCODE", "message": "Passcode salah" } }
```
- **Response `429 Too Many Requests`** (5x / 15 menit / IP):
```json
{ "success": false, "error": { "code": "RATE_LIMITED", "message": "Terlalu banyak percobaan, coba lagi nanti" } }
```
> Set-Cookie: `angkatan_session=<token>; HttpOnly; Secure; SameSite=Lax; Path=/; Max-Age=604800`
> Audit: `PASSCODE_FAIL` saat gagal.

#### 🔓 `DELETE /api/auth/passcode`
Logout student — revoke Sanctum token + clear cookie.

- **Response `200 OK`**: `{ "success": true, "data": { "message": "Logged out" } }`

### 3.2 Admin — Whitelist Email + OTP

#### 🔓 `POST /api/auth/admin/request-otp`
Kirim OTP 6 digit ke email admin (harus terdaftar di `admin_whitelist`).

- **Request Body**: `{ "email": "pengurus@gmail.com" }`
- **Response `200 OK`** (SELALU 200 generik — anti email-enumeration):
```json
{ "success": true, "data": { "message": "Jika email terdaftar, kode OTP telah dikirim." } }
```
- **Response `429 Too Many Requests`**:
```json
{ "success": false, "error": { "code": "RATE_LIMITED", "message": "Terlalu banyak permintaan OTP" } }
```
> OTP: 6 digit, expiry 5 menit, single-use. Dikirim via Resend (Laravel Mail).

#### 🔓 `POST /api/auth/admin/verify-otp`
Verifikasi OTP dan create Sanctum token + HttpOnly cookie.

- **Request Body**: `{ "email": "pengurus@gmail.com", "otp": "123456" }`
- **Response `200 OK`**:
```json
{ "success": true, "data": { "role": "admin", "email": "pengurus@gmail.com" } }
```
- **Response `401 Unauthorized`**:
```json
{ "success": false, "error": { "code": "INVALID_OTP", "message": "OTP salah atau kedaluwarsa" } }
```
> Set-Cookie: `admin_session=<token>; HttpOnly; Secure; SameSite=Strict; Path=/; Max-Age=28800`
> Audit: `ADMIN_LOGIN` (sukses) / `ADMIN_LOGIN_FAIL` (gagal).

#### 🔓 `POST /api/auth/admin/logout`
Logout admin — revoke Sanctum token + clear cookie.

- **Response `200 OK`**: `{ "success": true, "data": { "message": "Logged out" } }`

### 3.3 Session Check (untuk JavaScript frontend)

#### 🔓 `GET /api/auth/session`
Cek status sesi aktif (dipakai oleh JavaScript untuk toggle UI). **Satu-satunya GET endpoint API.**

- **Response `200 OK`**:
```json
{ "success": true, "data": { "role": "guest" } }
{ "success": true, "data": { "role": "student" } }
{ "success": true, "data": { "role": "admin", "email": "pengurus@gmail.com" } }
```

### 3.4 File Upload (Admin Only)

#### 🛡️ `POST /api/upload`
Presigned PUT URL Cloudflare R2 (upload/ganti foto profil).

- **Request Body**: `{ "filename": "21000123.jpg", "file_type": "image/jpeg" }`
- **Response `200 OK`**:
```json
{
  "success": true,
  "data": {
    "upload_url": "https://<account>.r2.cloudflarestorage.com/...?X-Amz-Algorithm=...",
    "public_url": "https://cdn.bukuangkatan.com/avatars/21000123.jpg",
    "key": "avatars/21000123.jpg"
  }
}
```
> Presigned URL expired dalam 5 menit. Alternatif: upload langsung via Filament FileUpload component (tanpa API).

### 3.5 Configuration Management (Admin Only)

#### 🛡️ `PATCH /api/admin/config`
Ganti Angkatan Passcode dan/atau increment `auth_epoch`.

- **Request Body**:
```json
{ "key": "angkatan_passcode", "new_passcode": "newSecret2024" }
```
- **Response `200 OK`**:
```json
{ "success": true, "data": { "updated_at": "2026-10-15T10:00:00Z", "epoch": 6 } }
```
> Ganti passcode otomatis menaikkan `auth_epoch` → seluruh sesi student lama langsung mati.
> Audit: `ROTATE_PASSCODE`.
> *Catatan: config management juga bisa dilakukan langsung via Filament Page `ConfigManagement` tanpa memanggil API ini.*

---

## 4. Route Web (Blade Views — Server-Side Rendered)

> Semua route di bawah terdaftar di `routes/web.php`. Mengembalikan **HTML lengkap** (Blade template), bukan JSON. Data di-query langsung oleh Controller lalu dikirim ke View via **Laravel's standard view binding** (`return view('page', $data)`).

### 4.1 Public Pages (🔓 Guest)

#### `GET /` — Landing Page
Halaman utama publik. Menampilkan hero section dan **statistik agregat** (total student, per kelas). **Tanpa data personal apa pun.**

- **Controller**: `PublicController@index`
- **View**: `landing.blade.php`
- **Data yang dikirim ke view**:
  - `total_students` (integer)
  - `per_class` (array: `[{class_name: 'A', total: 32}, ...]`)
  - `hero_title`, `hero_subtitle` (string)

#### `GET /unlock` — Form Angkatan Passcode
Form input passcode. Submit ke `POST /api/auth/passcode` (JavaScript fetch).

- **Controller**: `PasscodeController@showForm`
- **View**: `unlock.blade.php`

### 4.2 Protected Pages (🔐 Student / Admin)

> Semua route di bawah dipasangi **middleware `student`** (cek Sanctum token ability = student atau admin).

#### `GET /directory` — Direktori Angkatan
Daftar seluruh mahasiswa dengan pagination, filter kelas, dan pencarian. **Tidak menampilkan WA pribadi & alamat kost.**

- **Controller**: `DirectoryController@index`
- **View**: `directory.blade.php`
- **Data yang dikirim ke view**:
  - `students` (paginated Eloquent collection, field: nim, name, nickname, class_name, origin_city, instagram_username, avatar_url, bio_quote)
  - `filters` (array: kelas aktif untuk dropdown filter)
  - Query params: `?page=1&class=A&search=...`

#### `GET /students/{nim}` — Detail Profil Mahasiswa
Profil lengkap satu mahasiswa. **Tidak menampilkan WA pribadi, alamat kost, dan kontak darurat.**

- **Controller**: `StudentController@show`
- **View**: `students/show.blade.php`
- **Data yang dikirim ke view**:
  - `student` (Eloquent model, field publik: nim, name, nickname, song_url, class_name, religion, birth_place, birth_date, origin_city, email, instagram_username, line_id, avatar_url, bio_quote, hobbies)

#### `GET /gallery` — Galeri Foto
Grid foto profil seluruh angkatan.

- **Controller**: `GalleryController@index`
- **View**: `gallery.blade.php`
- **Data yang dikirim ke view**:
  - `photos` (paginated: nim, name, avatar_url)
  - Query params: `?page=1&class=A`

### 4.3 Admin Panel (🛡️ Filament)

> Route admin **dikelola sepenuhnya oleh Filament v5**. Tidak perlu manual controller/view untuk CRUD.

#### `GET /admin` — Filament Panel Dashboard
Redirect ke login jika belum autentikasi admin.

#### `GET /admin/students` — StudentResource (Filament)
CRUD lengkap data mahasiswa (semua kolom termasuk sensitif: whatsapp_no, address, emergency_contact_*). Upload foto via FileUpload component.

#### `GET /admin/emergencies` — EmergencyContacts Page (Filament)
View-only: daftar kontak darurat (emergency_contact_name, emergency_contact_phone). Audit `READ_EMERGENCIES` tiap akses.

#### `GET /admin/config` — ConfigManagement Page (Filament)
Ganti passcode + bump epoch. Audit `ROTATE_PASSCODE`.

#### `GET /admin/audit-logs` — AuditLogResource (Filament)
View-only log audit dengan filter action/actor/tanggal.

#### `GET /admin/admin-whitelists` — AdminWhitelistResource (Filament)
Kelola whitelist email admin. Audit `WHITELIST_CHANGE`.

---

## 5. Daftar Lengkap Endpoint API (JSON)

| Method | Endpoint | Auth | Deskripsi |
| :--- | :--- | :---: | :--- |
| `POST` | `/api/auth/passcode` | 🔓 | Verifikasi passcode angkatan → set cookie student |
| `DELETE` | `/api/auth/passcode` | 🔐 | Logout student → clear cookie |
| `POST` | `/api/auth/admin/request-otp` | 🔓 | Kirim OTP ke email admin |
| `POST` | `/api/auth/admin/verify-otp` | 🔓 | Verifikasi OTP → set cookie admin |
| `POST` | `/api/auth/admin/logout` | 🛡️ | Logout admin → clear cookie |
| `GET` | `/api/auth/session` | 🔓 | Cek role sesi aktif (JSON) |
| `POST` | `/api/upload` | 🛡️ | Generate presigned URL R2 |
| `PATCH` | `/api/admin/config` | 🛡️ | Ganti passcode / bump epoch |

## 6. Daftar Lengkap Route Web (Blade HTML)

| Method | Route | Auth | View | Deskripsi |
| :--- | :--- | :---: | :--- | :--- |
| `GET` | `/` | 🔓 | `landing.blade.php` | Landing page + statistik agregat |
| `GET` | `/unlock` | 🔓 | `unlock.blade.php` | Form input passcode |
| `GET` | `/directory` | 🔐 | `directory.blade.php` | Direktori mahasiswa (paginated + filter) |
| `GET` | `/students/{nim}` | 🔐 | `students/show.blade.php` | Detail profil mahasiswa |
| `GET` | `/gallery` | 🔐 | `gallery.blade.php` | Galeri foto profil |
| `GET` | `/admin/*` | 🛡️ | Filament | Panel admin (CRUD, config, audit) |

---

## 7. Privacy & Restricted Data Policy

🔒 **STRICT GUARANTEE**:

1. Field **Kontak Darurat** (`emergency_contact_name`, `emergency_contact_phone`) **HANYA** tampil di **Filament Panel Admin** (`/admin/emergencies` dan `/admin/students`) — ber-guard 🛡️ Admin Only.

2. Field **No WhatsApp Pribadi** (`whatsapp_no`) dan **Alamat Kost** (`address`) **tidak pernah** muncul di halaman 🔓 Public maupun 🔐 Protected. Hanya 🛡️ Admin (Filament) yang melihatnya.

3. Halaman 🔓 Public (`/`) hanya menampilkan **data agregat** (jumlah total, per kelas) — tanpa identitas personal.

4. Angkatan Passcode disimpan sebagai **bcrypt hash** di `app_config`; OTP admin disimpan sebagai **hash** di `admin_otp`. Keduanya tidak pernah plaintext dan tidak pernah dikirim ke client.

5. Seluruh akses data sensitif dicatat di `audit_logs`.

6. **Query scoping di Controller**: Controller untuk halaman 🔐 (`DirectoryController`, `StudentController`, `GalleryController`) **wajib** memilih kolom eksplisit (tidak boleh `select(*)` atau lazy-load field sensitif). Gunakan Eloquent query scoping atau `select()` spesifik.

---

## 8. Implementation Notes (High-Level)

### 8.1 API Response Wrapper (hanya untuk endpoint JSON)

Endpoint API (auth, upload, config) mengembalikan format seragam (lihat Section 2):

- Sediakan **satu mekanisme bersama** (trait/helper) untuk response `success` dan `error`.
- Response sukses: `{ success, data }`.
- Response error: `{ success, error: { code, message, details? } }`.

### 8.2 Exception Handling

- **Request API** (`api/*`): `ValidationException` di-transform ke JSON error format (Section 2), HTTP 422.
- **Request Web** (`web/*`): error validasi ditampilkan sebagai **blade error page** bawaan Laravel (tidak perlu transform JSON).

### 8.3 Data Passing ke Blade View (Server-Side Rendering)

Controller untuk route web mengirim data ke view via mekanisme standar Laravel:

1. Controller query database (Eloquent).
2. Terapkan **kolom eksplisit** (jangan `select(*)` untuk halaman non-admin).
3. Return data ke view: `return view('directory', compact('students', 'filters'))`.
4. Blade template render HTML lengkap dengan data tersebut.
5. Tidak ada pemanggilan API dari frontend — data sudah ada saat HTML dikirim.

### 8.4 Catatan Filament v5

Admin panel dikelola sepenuhnya oleh Filament v5. Konvensi:

| Aspek | Konvensi v5 |
| :--- | :--- |
| Definisi form | Memakai `Filament\Schemas\Schema` |
| Struktur resource | Resource + subfolder `Pages/`, `Schemas/`, `Tables/` |
| Aksi per baris tabel | `->recordActions([...])` |
| Aksi toolbar/bulk | `->toolbarActions([...])` |
| File upload | `Forms\Components\FileUpload::make('avatar_url')->disk('r2')` |
| Export | `ExportAction` atau `Filament\Actions\ExportAction` |
| Requirement | Livewire v4 + Tailwind CSS v4 |

> **CRUD, export, dan upload di admin tidak memerlukan API endpoint terpisah** — semua ditangani oleh Filament secara internal (Livewire component → controller → DB).
