# Cloudflare R2 Storage — Setup & Usage

Dokumen ini menjelaskan fitur file manager Cloudflare R2 (private/admin via
Filament + public via API passcode) yang ditambahkan di luar
`docs/ARCHITECTURE.md` / `docs/API_CONTRACTS.md` (dokumen aslinya fokus ke
upload foto profil mahasiswa; dokumen ini menambah general-purpose file
manager).

## 1. Komponen

| Komponen | File |
| :--- | :--- |
| Migration tabel `storage_files` | `database/migrations/2026_10_19_112306_create_storage_files_table.php` |
| Model `StorageFile` | `app/Models/StorageFile.php` |
| Service `R2FileManagerService` | `app/Services/R2FileManagerService.php` |
| Middleware `EnsureAngkatanPasscode` (alias `angkatan.passcode`) | `app/Http/Middleware/EnsureAngkatanPasscode.php` |
| Config | `config/angkatan.php` |
| API Publik | `routes/api/public-storage.php` → `App\Http\Controllers\Api\PublicStorageController` |
| Filament Page (admin) | `app/Filament/Pages/FileManager.php` (`/admin/file-manager`) |

## 2. Model Data

Setiap file/direktori pada disk `r2` punya 1 baris di tabel `storage_files`:

- `path` — object key lengkap di bucket (unik), contoh: `public/profile-aasokdaok/avatar.jpg`.
- `prefix` — segmen pertama dari path, dipakai untuk "scanning"/grouping (contoh: `profile-aasokdaok`).
- `alias` — slug opsional & unik untuk akses yang lebih ramah (contoh: `profile-aasokdaok-avatar`).
- `visibility` — `public` atau `private`. Menentukan siapa yang bisa mengakses & jenis URL yang di-generate (`url()` vs `temporaryUrl()`).
- `is_directory` — placeholder direktori (ditulis sebagai object `.keep` kosong di R2, karena S3/R2 tidak punya konsep direktori asli).
- `uploaded_by` — email admin (via Filament) atau `'public'` (via API publik).

## 3. Private (Admin / Filament)

Buka **Admin Panel → File Manager** (`/admin/file-manager`). Fitur:

- Browse folder (klik folder untuk masuk, tombol "Up" untuk naik).
- **New Folder** — buat direktori baru (pilih visibility public/private).
- **Upload File** — upload file ke folder yang sedang dibuka (pilih visibility, alias opsional).
- **Rename/Move** — ubah path (pindah folder/nama) & alias.
- **Delete** — hapus file atau direktori (rekursif untuk direktori).

Admin bisa melihat & mengelola **seluruh** file (public maupun private).

## 4. Public API (Passcode Protected)

Controller: `App\Http\Controllers\Api\PublicStorageController`. Route file:
`routes/api/public-storage.php`. Semua endpoint berada di bawah prefix
`/api/storage/public` dan **wajib** menyertakan Angkatan Passcode melalui
salah satu dari (dicek oleh `EnsureAngkatanPasscode` secara berurutan):

1. Header `X-Angkatan-Passcode: <passcode>`
2. Header `Authorization: Bearer <passcode>`
3. Field body/query `passcode=<passcode>`

Passcode dibaca dari `config('angkatan.passcode')` (env `ANGKATAN_PASSCODE`).
Jika passcode tidak dikirim atau salah, respons `401` dengan
`error.code = UNAUTHORIZED`. Rate limit: 60 request/menit/IP
(`throttle:60,1`) → respons `429` jika terlampaui.

Semua path yang dikirim client **otomatis di-prefix** dengan
`config('angkatan.public_storage.root_prefix')` (default: `public`), jadi API
ini **tidak bisa** menyentuh file `private` sama sekali — baik lewat listing,
maupun lewat tebak-tebak ID record (endpoint `update`/`destroy` akan `404`
jika record yang dituju bukan `visibility = public`).

Semua respons sukses berbentuk `{ "success": true, "data": ... }`, semua
respons gagal berbentuk `{ "success": false, "error": { "code", "message" } }`
— lihat `docs/API_CONTRACTS.md` §2 dan `ApiResponses` trait
(`app/Http/Controllers/Concerns/ApiResponses.php`).

| Method | Endpoint | Body/Query | Deskripsi |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/storage/public?path=profile-aasokdaok` | `path?` (query) | List isi folder (relatif terhadap root publik), non-rekursif |
| `POST` | `/api/storage/public/directories` | `{ "path": "profile-aasokdaok/sub" }` | Buat folder |
| `POST` | `/api/storage/public/files` (multipart) | `file`, `path`, `alias?`, `overwrite?` | Upload file |
| `PATCH` | `/api/storage/public/files/{id}` | `{ "path"?, "alias"? }` | Rename/move/ubah alias |
| `DELETE` | `/api/storage/public/files/{id}` | — | Hapus file/folder (rekursif untuk folder) |

`{id}` adalah UUID primary key dari `storage_files` (kolom `id`), bukan path
atau alias.

### 4.1 `GET /api/storage/public` — List folder

Request:

```sh
curl "https://your-app.test/api/storage/public?path=profile-21000123" \
  -H "X-Angkatan-Passcode: Passcode"
```

`path` kosong/absen berarti root publik (`public/`). List bersifat **non-rekursif**
— hanya item langsung di dalam folder tersebut.

Response `200`:

```json
{
  "success": true,
  "data": {
    "path": "profile-21000123",
    "items": [
      {
        "id": "0199c1b0-...-uuid",
        "path": "public/profile-21000123/avatar.jpg",
        "relative_path": "profile-21000123/avatar.jpg",
        "alias": "profile-21000123-avatar",
        "prefix": "profile-21000123",
        "is_directory": false,
        "mime_type": "image/jpeg",
        "size": 184320,
        "human_size": "180.00 KB",
        "url": "https://pub-....r2.dev/public/profile-21000123/avatar.jpg",
        "created_at": "2026-10-19T12:00:00+00:00",
        "updated_at": "2026-10-19T12:00:00+00:00"
      }
    ]
  }
}
```

`url` adalah URL publik langsung (disk `url()`) karena item ini
`visibility = public`. Untuk direktori, `url` selalu `null`.

### 4.2 `POST /api/storage/public/directories` — Buat folder

```sh
curl -X POST https://your-app.test/api/storage/public/directories \
  -H "X-Angkatan-Passcode: Passcode" \
  -H "Content-Type: application/json" \
  -d '{"path": "profile-21000123/sub-folder"}'
```

Validasi: `path` wajib, string, maks 1024 karakter
(`StoreDirectoryRequest`). Jika folder sudah ada → `422`
(`error.code = DIRECTORY_EXISTS`).

### 4.3 `POST /api/storage/public/files` — Upload file

Wajib `multipart/form-data` (bukan JSON) karena mengirim file biner.

```sh
curl -X POST https://your-app.test/api/storage/public/files \
  -H "X-Angkatan-Passcode: Passcode" \
  -F "file=@avatar.jpg" \
  -F "path=profile-21000123" \
  -F "alias=profile-21000123-avatar"
```

Field (`StoreUploadRequest`):

| Field | Wajib | Catatan |
| :--- | :--- | :--- |
| `file` | ya | Max size dari `config('angkatan.public_storage.max_upload_size_kb')`, default 10 MB |
| `path` | ya | Folder tujuan (contoh `profile-21000123`) **atau** full filename jika diakhiri ekstensi (contoh `profile-21000123/avatar.jpg`). Lihat §9 soal aturan ini. |
| `alias` | tidak | Harus unik di seluruh tabel (public + private), format `alpha_dash` |
| `overwrite` | tidak | `true`/`false`. Default `false` — jika file di path yang sama sudah ada & `overwrite` bukan `true`, respons `422` (`UPLOAD_CONFLICT`) |

File otomatis disimpan dengan `visibility = public` dan di bawah root prefix
publik. Response `201` dengan bentuk `data` yang sama seperti item pada §4.1.

### 4.4 `PATCH /api/storage/public/files/{id}` — Rename / move / ubah alias

```sh
curl -X PATCH https://your-app.test/api/storage/public/files/0199c1b0-...-uuid \
  -H "X-Angkatan-Passcode: Passcode" \
  -H "Content-Type: application/json" \
  -d '{"path": "profile-21000123/avatar-new.jpg", "alias": "profile-21000123-avatar-v2"}'
```

Kedua field opsional (`UpdateStorageFileRequest`, `sometimes`) — kirim salah
satu atau keduanya. `path` baru tetap di-prefix otomatis dengan root publik.
Destinasi yang sudah terisi → `422` (`MOVE_CONFLICT`). Alias yang sudah
dipakai record lain → `422` dengan pesan validasi standar Laravel pada field
`alias`.

### 4.5 `DELETE /api/storage/public/files/{id}` — Hapus

```sh
curl -X DELETE https://your-app.test/api/storage/public/files/0199c1b0-...-uuid \
  -H "X-Angkatan-Passcode: Passcode"
```

Jika `id` adalah folder, semua isi di dalamnya (rekursif) ikut terhapus dari
R2 **dan** dari tabel `storage_files`. Response `200`:
`{ "success": true, "data": { "message": "Deleted" } }`.

### 4.6 Kode Error yang Mungkin Muncul

| HTTP | `error.code` | Kapan terjadi |
| :--- | :--- | :--- |
| 401 | `UNAUTHORIZED` | Passcode tidak dikirim, salah, atau tidak dikonfigurasi di server |
| 404 | — (abort biasa) | `{id}` tidak ditemukan, atau ditemukan tapi bukan `visibility = public` |
| 422 | `DIRECTORY_EXISTS` | Membuat folder yang sudah ada |
| 422 | `UPLOAD_CONFLICT` | Upload ke path yang sudah terisi tanpa `overwrite=true` |
| 422 | `MOVE_CONFLICT` | Rename/move ke path tujuan yang sudah terisi |
| 422 | (validation errors standar Laravel) | Field tidak lolos validasi (`path`/`alias`/`file` dsb) |
| 429 | — | Rate limit (60/menit/IP) terlampaui |

## 5. Pakai `R2FileManagerService` Langsung dari Kode (Tanpa Lewat API)

Dari kode backend (controller lain, job, command, seeder, dsb.) kamu bisa
langsung memanggil `App\Services\R2FileManagerService` tanpa lewat HTTP —
tidak ada passcode, tidak ada rate limit, dan **tidak otomatis di-prefix**
dengan root publik (itu hanya aturan di `PublicStorageController`, bukan di
service-nya). Service ini bekerja pada path absolut relatif terhadap root
disk `r2`.

Resolve lewat container (bukan `new R2FileManagerService()` manual, supaya
disk binding & dependency lain tetap konsisten):

```php
use App\Services\R2FileManagerService;

$manager = app(R2FileManagerService::class);
// atau type-hint di constructor/method manapun yang di-resolve oleh container
```

### 5.1 Upload file

```php
use App\Models\StorageFile;

// $uploadedFile bisa dari request (Illuminate\Http\UploadedFile) atau
// dibuat manual dari file lokal (lihat §5.6).
$record = $manager->upload(
    file: $uploadedFile,
    desiredPath: 'profile-21000123/avatar.jpg', // folder ATAU full filename — lihat §9
    visibility: StorageFile::VISIBILITY_PUBLIC,  // atau VISIBILITY_PRIVATE
    alias: 'profile-21000123-avatar',            // opsional, null jika tidak perlu
    prefix: null,                                 // opsional, auto-derive dari segmen pertama path jika null
    uploadedBy: auth()->user()?->email,           // opsional, buat audit trail
    overwrite: false,                              // true untuk replace file yang sudah ada di path sama
);

// $record adalah instance StorageFile (sudah tersimpan di DB + R2)
$record->path;   // "profile-21000123/avatar.jpg"
$record->url();  // presigned (private) atau URL publik langsung (public)
```

Lempar `InvalidArgumentException` jika:
- path tujuan sudah terisi file lain dan `overwrite: false` (default), atau
- `alias` yang diminta sudah dipakai record lain.

### 5.2 Buat folder

```php
$folder = $manager->createDirectory(
    path: 'profile-21000123/documents',
    visibility: StorageFile::VISIBILITY_PRIVATE,
    prefix: null,
    createdBy: auth()->user()?->email,
);
```

Lempar `InvalidArgumentException` jika path sudah ada.

### 5.3 List isi folder (non-rekursif)

```php
// Semua visibility (public + private) di dalam folder:
$items = $manager->listDirectory('profile-21000123');

// Hanya yang public:
$items = $manager->listDirectory('profile-21000123', StorageFile::VISIBILITY_PUBLIC);

// Root disk:
$items = $manager->listDirectory(); // atau listDirectory(null)
```

Mengembalikan `Illuminate\Support\Collection<StorageFile>`, folder duluan
lalu file, diurutkan berdasarkan `path`.

### 5.4 Rename / Move

```php
$record = StorageFile::where('path', 'profile-21000123/avatar.jpg')->firstOrFail();

$manager->move($record, 'profile-21000123/avatar-v2.jpg');
```

Untuk folder, semua descendant (file & subfolder di dalamnya) ikut
dipindahkan secara rekursif, termasuk placeholder `.keep`-nya. Lempar
`InvalidArgumentException` jika path tujuan sudah terisi.

### 5.5 Hapus

```php
$manager->delete($record); // file tunggal, atau folder + seluruh isinya (rekursif)
```

### 5.6 Upload dari file lokal (bukan dari HTTP request)

Jika sumber file bukan dari `$request->file(...)` (misalnya generate PDF,
hasil proses image, file hasil `Storage::disk('local')`), bungkus dulu
sebagai `Illuminate\Http\UploadedFile` dengan flag "test mode" (`true` di
parameter ke-5) supaya tidak divalidasi sebagai upload HTTP asli:

```php
use Illuminate\Http\UploadedFile;

$localPath = storage_path('app/private/generated/report.pdf');

$uploadedFile = new UploadedFile(
    path: $localPath,
    originalName: 'report.pdf',
    mimeType: 'application/pdf',
    error: null,
    test: true, // wajib true untuk file yang bukan hasil upload HTTP
);

$manager->upload(
    file: $uploadedFile,
    desiredPath: 'reports/report.pdf',
    visibility: StorageFile::VISIBILITY_PRIVATE,
);
```

### 5.7 Presigned upload URL (browser → R2 langsung, tanpa lewat server)

Disediakan untuk kasus upload file besar tanpa membebani server, **tapi
belum dipakai di mana pun** (baik Filament page maupun API publik saat ini
upload selalu lewat server — lihat §8.1). Butuh CORS dikonfigurasi di bucket
R2 kalau mau benar-benar dipakai dari browser:

```php
$result = $manager->presignedUploadUrl(
    desiredPath: 'profile-21000123/avatar.jpg',
    expiresInMinutes: 5,
);

// $result = ['upload_url' => ..., 'headers' => [...], 'key' => ..., 'public_url' => ...]
```

### 5.8 Resolve URL

```php
$record->url();                  // default: presigned 5 menit (private) / URL publik langsung (public)
$record->url(temporaryUrlMinutes: 30); // presigned 30 menit untuk file private

// Setara, lewat service:
$manager->urlFor($record, temporaryUrlMinutes: 30);
```

## 6. Konvensi Penamaan Prefix

Gunakan segmen pertama path sebagai "prefix" scan yang konsisten, misalnya
`profile-<nim/slug>` untuk foto profil mahasiswa individu:

```
public/profile-21000123/avatar.jpg
public/profile-21000123/informal.jpg
```

Query by prefix: `StorageFile::query()->byPrefix('profile-21000123')->get()`.

## 7. Environment Variables

| Variable | Catatan |
| :--- | :--- |
| `ANGKATAN_PASSCODE` | **Diganti nama** dari typo lama `ANGGATAN_PASSCODE`. Passcode bootstrap untuk API publik. |
| `ANGKATAN_PUBLIC_PREFIX` | Opsional, default `public`. Root folder untuk API publik. |
| `ANGKATAN_PUBLIC_MAX_UPLOAD_KB` | Opsional, default `10240` (10 MB). |
| `R2_*` | Kredensial & endpoint Cloudflare R2 (lihat `.env.example`). **Perbaikan**: `R2_ENDPOINT` sebelumnya menyertakan nama bucket sebagai suffix path (`.../buku-angkatan-assets`), yang salah untuk virtual-hosted S3 addressing — sudah dihapus. |

## 8. Dependency Tambahan

- `league/flysystem-aws-s3-v3` — wajib untuk disk `s3`/`r2` berfungsi (sebelumnya belum terpasang meski `config/filesystems.php` sudah mengacu ke driver `s3`).

## 8.1 Upload Selalu Lewat Server (Bukan Langsung Browser → R2)

Semua upload pada fitur ini (API publik maupun Filament admin) mengalir
`browser → Laravel → R2`. Browser **tidak pernah** mendapat presigned URL
untuk upload langsung ke R2 dari sisi klien.

Catatan teknis: Livewire's `FileUpload` form component punya tahap
"staging" internal terpisah — sebelum action kita jalan, Livewire menyimpan
file sementara ke disk yang ditentukan oleh
`config('livewire.temporary_file_upload.disk')` (default-nya ikut
`filesystems.default`). Karena `.env` set `FILESYSTEM_DISK=r2`, tanpa
override ini Livewire mencoba staging langsung ke R2 dari browser (presigned
PUT), yang gagal karena **CORS** (bucket R2 tidak dikonfigurasi untuk
menerima preflight dari origin aplikasi).

Fix: set `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=local` di `.env`, supaya
staging sementara selalu di disk lokal server, baru kemudian
`R2FileManagerService::upload()` yang mengirim ke R2 dari sisi server.

Jika suatu saat ingin upload langsung browser → R2 (bypass server, lebih
cepat untuk file besar), gunakan `R2FileManagerService::presignedUploadUrl()`
yang sudah disediakan — tapi itu **mengharuskan** konfigurasi CORS di bucket
R2 (Cloudflare Dashboard → R2 → bucket → Settings → CORS Policy, izinkan
origin aplikasi + method `PUT`).

## 9. Aturan "Folder vs Nama File" Saat Upload (`desiredPath`)

Baik lewat API (`path` field) maupun lewat service (`desiredPath` param),
`R2FileManagerService::upload()` menentukan apakah nilai yang kamu kirim itu
**folder tujuan** atau **nama file lengkap** dengan satu aturan sederhana di
`resolveUploadPath()`:

> Jika segmen terakhir path diakhiri pola `.<1-10 karakter alfanumerik>`
> (terlihat seperti ekstensi file), dia dianggap **nama file lengkap** dan
> dipakai apa adanya. Selain itu, dianggap **folder**, dan nama file asli
> (dari `UploadedFile::getClientOriginalName()`, di-slug + diberi suffix
> random 8 karakter) otomatis ditempelkan di belakangnya.

Contoh:

| `desiredPath` dikirim | Hasil |
| :--- | :--- |
| `profile-21000123` | **Folder**. File disimpan sebagai `profile-21000123/<nama-asli-slug>-<random8>.<ext>` |
| `profile-21000123/avatar.jpg` | **File**. Disimpan persis di `profile-21000123/avatar.jpg` |
| `profile-21000123/avatar` (tanpa ekstensi!) | **Folder** bernama `avatar` di dalam `profile-21000123` — file asli ditaruh *di dalamnya* dengan nama random. Ini **bukan** yang biasanya diinginkan! |

⚠️ **Gotcha paling sering bikin bingung**: kalau kamu bermaksud memberi nama
custom ke file tapi lupa menyertakan ekstensinya, hasilnya adalah folder
baru, bukan file dengan nama itu. Ini pernah jadi bug nyata di halaman
Filament **Upload File** — field "Save as" yang diisi tanpa ekstensi (misal
mengetik `vacation` untuk `vacation.jpg`) membuat folder `vacation/` berisi
file dengan nama random, bukan file `vacation.jpg`. Sudah diperbaiki di
`FileManager.php` dengan menambahkan ekstensi asli secara otomatis jika
field "Save as" diisi tanpa ekstensi — tapi aturan dasar di
`R2FileManagerService` ini tetap berlaku untuk pemanggilan lain (termasuk
lewat API publik), jadi **selalu sertakan ekstensi** kalau maksudnya adalah
nama file, bukan folder.

## 10. Known Limitations / Follow-up

- **Auth publik** hanya memakai 1 shared passcode (bukan token per-sesi Sanctum seperti alur student/admin di `docs/ARCHITECTURE.md`). Siapa pun yang tahu passcode bisa upload/edit/delete file publik. Rate limiting sudah ditambahkan sebagai mitigasi dasar; pertimbangkan audit log per-aksi jika dibutuhkan akuntabilitas lebih ketat.
- Migration **belum dijalankan** ke Supabase dari environment pengembangan ini. PHP lokal sekarang sudah punya `pdo_pgsql` (lihat catatan instalasi PHP terbaru), jadi ini tinggal keputusan kapan mau pindah `DB_CONNECTION` dari `sqlite` ke `pgsql` di `.env`.
- `R2FileManagerService::presignedUploadUrl()` disediakan untuk kasus upload langsung dari client ke R2 (tanpa lewat server), namun belum diekspos sebagai endpoint API — tambahkan route terpisah bila diperlukan.
