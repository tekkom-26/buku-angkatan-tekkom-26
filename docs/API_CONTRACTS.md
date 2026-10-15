# API Contracts & Specification

Dokumen API Contracts untuk backend **Web Buku Angkatan** (Laravel 13 + Filament v5).

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

### Success Response
```json
{
  "success": true,
  "data": {},
  "meta": { "page": 1, "limit": 20, "total": 120, "total_pages": 6 }
}
```
*(`meta` opsional, hanya pada endpoint koleksi/paginated)*

### Error Response
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Validation failed",
    "details": {
      "whatsapp_number": ["Invalid WhatsApp number format. Must start with 628"]
    }
  }
}
```

> **Laravel validation error** (422) di-transform ke format di atas via `bootstrap/app.php` exception handler.

### Guard Error Responses
```json
// 401 — passcode belum dimasukkan / sesi kedaluwarsa / dicabut
{ "success": false, "error": { "code": "UNAUTHORIZED", "message": "Angkatan passcode required" } }

// 403 — role kurang (student akses admin-only)
{ "success": false, "error": { "code": "FORBIDDEN", "message": "Admin access required" } }
```

---

## 3. Auth Endpoints

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
- **Response `200 OK`** (SELALU 200 generik — anti email-enumeration, baik email terdaftar maupun tidak):
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
Logout admin — revoke Sanctum token + clear cookie (aman dipanggil tanpa sesi aktif).

#### 🔓 `GET /api/auth/session`
Status sesi aktif (untuk UI). Sesi dengan `epoch` lama dianggap invalid.

- **Response `200 OK`**:
```json
{ "success": true, "data": { "role": "guest" } }
{ "success": true, "data": { "role": "student" } }
{ "success": true, "data": { "role": "admin", "email": "pengurus@gmail.com" } }
```

---

## 4. Public Endpoints (Landing Page)

#### 🔓 `GET /api/public/stats`
Statistik agregat angkatan. **Tanpa data personal.**

- **Response `200 OK`**:
```json
{
  "success": true,
  "data": {
    "total_students": 122,
    "total_classes": 4,
    "hero_title": "Buku Angkatan 2022",
    "hero_subtitle": "Kenalan lebih dekat dengan angkatan kita."
  }
}
```

#### 🔓 `GET /api/classes`
Rekapitulasi jumlah mahasiswa per kelas.

- **Response `200 OK`**:
```json
{
  "success": true,
  "data": [
    { "class_name": "A", "total_students": 32 },
    { "class_name": "B", "total_students": 30 },
    { "class_name": "C", "total_students": 29 },
    { "class_name": "D", "total_students": 31 }
  ]
}
```

---

## 5. Protected Endpoints (Angkatan Passcode)

#### 🔐 `GET /api/students`
Direktori mahasiswa dengan pagination, filter kelas, dan pencarian.

- **Query Parameters**: `page` (default 1), `limit` (default 20), `class` (`A|B|C|D`), `search` (nama/NIM)
- **Response `200 OK`**:
```json
{
  "success": true,
  "data": [
    {
      "id": "c7b8e1a0-4f2b-4d3a-8f1e-9a0b1c2d3e4f",
      "nim": "21000123",
      "full_name": "Ahmad Subekti",
      "nickname": "Bekti",
      "class_name": "A",
      "origin_city": "Semarang",
      "instagram_handle": "ahmadbekti",
      "profile": {
        "formal_photo_url": "https://cdn.bukuangkatan.com/formal/21000123.jpg",
        "quote": "Belajar terus sampai faham.",
        "hobbies": ["Coding", "Fotografi"]
      }
    }
  ],
  "meta": { "page": 1, "limit": 20, "total": 122, "total_pages": 7 }
}
```

#### 🔐 `GET /api/students/{nim}`
Detail profil mahasiswa (data untuk `student` — **tanpa WA pribadi & alamat kost**).

- **Response `200 OK`**:
```json
{
  "success": true,
  "data": {
    "id": "c7b8e1a0-4f2b-4d3a-8f1e-9a0b1c2d3e4f",
    "nim": "21000123",
    "full_name": "Ahmad Subekti",
    "nickname": "Bekti",
    "class_name": "A",
    "religion": "ISLAM",
    "birth_place": "Semarang",
    "birth_date": "2002-08-17",
    "origin_city": "Semarang",
    "instagram_handle": "ahmadbekti",
    "profile": {
      "hobbies": ["Coding", "Fotografi", "Basket"],
      "quote": "Belajar terus sampai faham.",
      "favorite_food_place": "Warung Bu Siti Bojongsoang",
      "spotify_track_url": "https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT",
      "formal_photo_url": "https://cdn.bukuangkatan.com/formal/21000123.jpg",
      "informal_photo_url": "https://cdn.bukuangkatan.com/informal/21000123.jpg"
    }
  }
}
```
> ⚠️ **Tidak ada** `whatsapp_number` dan `boarding_address` — keduanya admin-only.

- **Response `404 Not Found`**:
```json
{ "success": false, "error": { "code": "NOT_FOUND", "message": "Student with NIM 21000123 was not found" } }
```

#### 🔐 `GET /api/gallery`
Galeri foto seluruh angkatan (formal & non-formal).

- **Query Parameters**: `page`, `limit`, `class` (opsional)
- **Response `200 OK`**:
```json
{
  "success": true,
  "data": [
    {
      "nim": "21000123",
      "full_name": "Ahmad Subekti",
      "formal_photo_url": "https://cdn.bukuangkatan.com/formal/21000123.jpg",
      "informal_photo_url": "https://cdn.bukuangkatan.com/informal/21000123.jpg"
    }
  ],
  "meta": { "page": 1, "limit": 24, "total": 122, "total_pages": 6 }
}
```

---

## 6. Admin Endpoints (Restricted)

> Semua endpoint 🛡️ memerlukan Sanctum token dengan ability `admin` yang valid (epoch cocok). **Semua mutasi data & upload hanya admin.**

#### 🛡️ `POST /api/upload`
Presigned PUT URL Cloudflare R2 (upload/ganti foto).

- **Request Body**: `{ "filename": "21000123-formal.jpg", "file_type": "image/jpeg", "folder": "formal" }`
- **Response `200 OK`**:
```json
{
  "success": true,
  "data": {
    "upload_url": "https://<account_id>.r2.cloudflarestorage.com/buku-angkatan-assets/formal/21000123-formal.jpg?X-Amz-Algorithm=...",
    "public_url": "https://cdn.bukuangkatan.com/formal/21000123-formal.jpg",
    "key": "formal/21000123-formal.jpg"
  }
}
```
> Presigned URL expired dalam 5 menit.

#### 🛡️ `GET /api/admin/emergencies`
Data darurat (kontak ortu & ibu/bapak kost). **Satu-satunya endpoint yang mengembalikan `student_emergencies`.**

- **Query Parameters**: `page`, `limit`, `class`, `search`
- **Response `200 OK`**:
```json
{
  "success": true,
  "data": [
    {
      "nim": "21000123",
      "full_name": "Ahmad Subekti",
      "parent_whatsapp": "6281200000001",
      "landlord_whatsapp": "6281200000002"
    }
  ],
  "meta": { "page": 1, "limit": 20, "total": 122, "total_pages": 7 }
}
```
> Audit: `READ_EMERGENCIES` dicatat tiap akses.

#### 🛡️ `GET /api/admin/students` · `POST /api/admin/students`
Kelola data siswa (list lengkap termasuk WA pribadi & alamat kost, serta create).

**GET response** sama seperti `GET /api/students` tapi include `whatsapp_number` dan `boarding_address`.

**POST request body**:
```json
{
  "nim": "21000125",
  "full_name": "Citra Dewi",
  "nickname": "Citra",
  "class_name": "B",
  "religion": "ISLAM",
  "birth_place": "Bandung",
  "birth_date": "2002-05-10",
  "origin_city": "Bandung",
  "contact": {
    "whatsapp_number": "628123456790",
    "instagram_handle": "citradewi",
    "boarding_address": "Jl. Cibaduyut No. 5"
  },
  "profile": {
    "hobbies": ["Membaca", "Travelling"],
    "quote": "Hidup adalah petualangan",
    "favorite_food_place": "Warung Nasi Ibu Ani",
    "spotify_track_url": "",
    "formal_photo_url": "https://cdn.bukuangkatan.com/formal/21000125.jpg",
    "informal_photo_url": "https://cdn.bukuangkatan.com/informal/21000125.jpg"
  },
  "emergency": {
    "parent_whatsapp": "628987654321",
    "landlord_whatsapp": "628111222333"
  }
}
```

**POST response `201 Created`**:
```json
{ "success": true, "data": { "id": "uuid", "nim": "21000125" } }
```

> Audit: `MUTATE_STUDENT` dicatat untuk create/update/delete.

#### 🛡️ `GET /api/admin/students/{nim}` · `PATCH` · `DELETE`
Detail/edit/hapus data siswa (full field, termasuk kontak pribadi).

**PATCH request** sama seperti POST tapi semua field opsional.

**DELETE response `200 OK`**:
```json
{ "success": true, "data": { "message": "Student deleted successfully" } }
```

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

#### 🛡️ `GET /api/admin/audit-logs`
Jejak audit (pagination + filter `action`, `actor_email`, rentang tanggal).

- **Query Parameters**: `page`, `limit`, `action`, `actor_email`, `date_from`, `date_to`
- **Response `200 OK`**:
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "actor_email": "pengurus@gmail.com",
      "actor_role": "admin",
      "action": "READ_EMERGENCIES",
      "target": null,
      "ip": "103.x.x.x",
      "created_at": "2026-10-15T10:00:00Z"
    }
  ],
  "meta": { "page": 1, "limit": 50, "total": 320, "total_pages": 7 }
}
```

#### 🛡️ `GET /api/admin/export`
Ekspor seluruh data (termasuk kontak darurat) ke CSV/XLSX.

- **Query Parameters**: `format` (`csv` atau `xlsx`)
- **Response `200 OK`**: File download dengan header `Content-Disposition: attachment; filename="students_export_2026-10-15.csv"`

---

## 7. Privacy & Restricted Data Policy

🔒 **STRICT GUARANTEE**:

1. Tabel `student_emergencies` (`parent_whatsapp`, `landlord_whatsapp`) **HANYA** dapat diakses via `GET /api/admin/emergencies`, `GET /api/admin/export`, dan Filament Page `EmergencyContacts` — semuanya ber-guard 🛡️ Admin Only.

2. Field **No WhatsApp Pribadi** (`student_contacts.whatsapp_number`) dan **Alamat Kost** (`student_contacts.boarding_address`) **tidak pernah** muncul di endpoint 🔓 Public maupun 🔐 Protected. Hanya endpoint 🛡️ Admin yang mengembalikannya.

3. Endpoint 🔓 Public (`GET /api/public/stats`, `GET /api/classes`) hanya mengembalikan **data agregat** — tanpa identitas personal.

4. Angkatan Passcode disimpan sebagai **bcrypt hash** di `app_config`; OTP admin disimpan sebagai **hash** di `admin_otp`. Keduanya tidak pernah plaintext dan tidak pernah dikirim ke client.

5. Seluruh akses data sensitif dicatat di `audit_logs`.

---

## 8. Implementation Notes (High-Level)

Section ini menjelaskan **pendekatan** yang dipakai agar kontrak di atas terpenuhi. Detail penulisan kode diserahkan ke fase implementasi.

### 8.1 API Response Wrapper

Semua endpoint JSON mengembalikan format seragam (lihat Section 2). Pendekatan:

- Sediakan **satu mekanisme bersama** (mis. trait atau helper) untuk membentuk response `success` dan `error`.
- Response sukses: `{ success, data, meta? }`.
- Response error: `{ success, error: { code, message, details? } }`.
- `meta` hanya disertakan pada endpoint koleksi/paginated.

**Alur pembentukan response sukses (pseudocode):**
1. Terima `data` dan (opsional) `meta`.
2. Bentuk payload `{ success: true, data }`.
3. Jika `meta` ada → tambahkan ke payload.
4. Kembalikan sebagai JSON dengan HTTP status yang sesuai.

**Alur pembentukan response error (pseudocode):**
1. Terima `code`, `message`, (opsional) `details`, dan `status`.
2. Bentuk payload `{ success: false, error: { code, message } }`.
3. Jika `details` ada → tambahkan ke dalam `error`.
4. Kembalikan sebagai JSON dengan HTTP status yang sesuai.

### 8.2 Exception Handling (Validasi & Error Umum)

- Laravel melempar `ValidationException` (HTTP 422) saat validasi Form Request gagal.
- **Global exception handler** men-transform exception tersebut ke format error seragam di Section 2.
- Berlaku khusus untuk request yang menargetkan `api/*`; request web (Blade) tetap menampilkan error bawaan.

**Alur transform error (pseudocode):**
1. Tangkap `ValidationException`.
2. Jika request menuju `api/*`:
   - Susun payload error dengan `code = VALIDATION_ERROR`, `message` umum, dan `details` = daftar error per field.
   - Kembalikan HTTP `422`.
3. Selain itu → biarkan handler bawaan berjalan.

### 8.3 Field Scoping via API Resource

- Endpoint non-admin **wajib** memakai API Resource untuk memilih kolom yang boleh keluar.
- Field sensitif (`whatsapp_number`, `boarding_address`, `student_emergencies`) hanya disertakan bila pemanggil punya ability `admin`.

**Alur shaping response student (pseudocode):**
1. Susun field publik: identitas, kelas, kota asal, Instagram, ringkasan profil (foto, quote, hobi).
2. Cek ability token pemanggil.
3. Jika pemanggil `admin` → tambahkan `whatsapp_number` dan `boarding_address`.
4. Jika bukan admin → jangan sertakan field tersebut sama sekali (bukan di-null-kan, tetapi tidak ada di payload).
5. Kembalikan data hasil shaping.

### 8.4 Catatan Filament v5

Beberapa konvensi API Filament v5 yang perlu diperhatikan saat membangun panel admin (menggantikan gaya Filament v3):

| Aspek | Konvensi v5 |
| :--- | :--- |
| Definisi form | Memakai `Filament\Schemas\Schema` (bukan `Form`) |
| Struktur resource | Resource + subfolder `Pages/`, `Schemas/`, `Tables/` |
| Aksi per baris tabel | `->recordActions([...])` |
| Aksi toolbar/bulk | `->toolbarActions([...])` |
| Requirement | Livewire v4 + Tailwind CSS v4 |

> Nama class dan method final dapat menyesuaikan versi Filament minor terbaru; yang penting adalah pemisahan Resource/Pages/Schemas/Tables dan penempatan aksi pada grup yang benar.
