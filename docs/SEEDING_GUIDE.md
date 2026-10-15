# Data Ingestion & CSV Seeding Guide

Panduan resmi migrasi dan penyemaian data dari Google Form (.csv) ke Supabase PostgreSQL & Cloudflare R2 untuk **Web Buku Angkatan**.

> **Prinsip dokumen ini**: panduan **alur & spesifikasi** proses seeding. Berisi pseudocode, mapping kolom, dan kontrak output — bukan baris kode implementasi. Detail teknis diserahkan ke fase koding.

---

## 1. Overview Ingestion Pipeline

Proses penyemaian data dijalankan oleh **satu Artisan command** khusus (mis. `php artisan seed:from-csv`).

```
[ Google Form Export (.csv) ] 
            │
            ▼
┌───────────────────────────────────────┐
│  1. CSV Parser & Row Normalization   │
└───────────────────────────────────────┘
            │
            ▼
┌───────────────────────────────────────┐
│  2. Data Sanitization Rules           │
│     - WA -> '628xxx'                  │
│     - IG -> Username @ stripped       │
│     - Dates -> ISO Date Format        │
└───────────────────────────────────────┘
            │
            ▼
┌───────────────────────────────────────┐
│  3. Media Processing (Drive -> R2)    │
│     - Download Image Stream           │
│     - Upload to R2 via Storage facade │
│     - Get R2 Public CDN URL           │
└───────────────────────────────────────┘
            │
            ▼
┌───────────────────────────────────────┐
│  4. Database Atomic Insertion         │
│     - Transaction per row             │
│     - students / contacts /           │
│       profiles / emergencies          │
└───────────────────────────────────────┘
```

---

## 2. Data Sanitization Specifications

Sanitizer adalah **helper function global** yang tersedia di seluruh aplikasi (di-autoload via composer `autoload.files`). Berikut spesifikasi perilakunya.

### A. Format Nomor WhatsApp (`sanitizeWhatsApp`)

**Alur logika (pseudocode):**
1. Jika input kosong → kembalikan `null`.
2. Hapus semua karakter non-digit.
3. Normalisasi awalan ke `62…`:
   - Jika diawali `0` → ganti `0` dengan `62` (mis. `08…` → `628…`).
   - Jika diawali `8` → tambahkan prefix `62` (mis. `8…` → `628…`).
   - Jika sudah diawali `62` → biarkan.
4. Validasi hasil terhadap regex `^628\d{8,12}$`.
5. Jika cocok → kembalikan nomor; jika tidak → `null`.

**Contoh transformasi:**

| Input | Output |
| :--- | :--- |
| `08123456789` | `628123456789` |
| `+628123456789` | `628123456789` |
| `8123456789` | `628123456789` |
| `abc` | `null` |

### B. Handle Instagram (`sanitizeInstagram`)

**Alur logika (pseudocode):**
1. Jika input kosong → `null`.
2. Trim whitespace.
3. Buang prefix URL (`http(s)://`, `www.`, `instagram.com/`).
4. Buang simbol `@` di awal.
5. Buang trailing slash `/`.
6. Jika hasil kosong → `null`; jika tidak → kembalikan hasil.

**Contoh:** `@ahmad.bekti/` → `ahmad.bekti`.

### C. Parsing Tanggal Lahir (`parseBirthDate`)

**Alur logika (pseudocode):**
1. Jika input kosong → `null`.
2. Trim input.
3. Jika sudah format ISO (`YYYY-MM-DD`) → kembalikan apa adanya.
4. Jika format `DD/MM/YYYY` → susun ulang menjadi `YYYY-MM-DD`.
5. Jika format teks (mis. `17 Agustus 2002`) → parse via library tanggal lalu format ke ISO.
6. Jika parsing gagal → `null`.

**Contoh transformasi:**

| Input | Output |
| :--- | :--- |
| `17/08/2002` | `2002-08-17` |
| `2002-08-17` | `2002-08-17` |
| `17 Agustus 2002` | `2002-08-17` |
| `bukan tanggal` | `null` |

---

## 3. CSV Column Mapping

Mapping header Google Form CSV ke tabel database:

| Header CSV Google Form | Table & Column Target | Rules & Transform |
| :--- | :--- | :--- |
| `Nama Lengkap` | `students.full_name` | Trim String |
| `Nama Panggilan` | `students.nickname` | Trim String |
| `NIM` | `students.nim` | Uppercase, Unique Index |
| `Kelas` | `students.class_name` | Uppercase (`A`, `B`, `C`, `D`) |
| `Agama` | `students.religion` | Enum mapping (`ISLAM`, `CATHOLIC`, dll) |
| `Tempat Lahir` | `students.birth_place` | Trim String |
| `Tanggal Lahir` | `students.birth_date` | `parseBirthDate` |
| `Kota Asal / Domisili` | `students.origin_city` | Trim String |
| `No WhatsApp Aktif` | `student_contacts.whatsapp_number` | `sanitizeWhatsApp` |
| `Username Instagram` | `student_contacts.instagram_handle` | `sanitizeInstagram` |
| `Alamat Kost` | `student_contacts.boarding_address` | Text |
| `Hobi / Minat` | `student_profiles.hobbies` | Split by comma `,` → JSON array |
| `Quotes / Motto` | `student_profiles.quote` | Text |
| `Tempat Makan Favorit` | `student_profiles.favorite_food_place` | Text |
| `Link Spotify Lagu` | `student_profiles.spotify_track_url` | URL string |
| `Foto Formal (KTM)` | `student_profiles.formal_photo_url` | Download Google Drive → R2 Upload |
| `Foto Non-Formal` | `student_profiles.informal_photo_url` | Download Google Drive → R2 Upload |
| `No WA Orang Tua` | `student_emergencies.parent_whatsapp` | `sanitizeWhatsApp` |
| `No WA Ibu/Bapak Kost` | `student_emergencies.landlord_whatsapp` | `sanitizeWhatsApp` |

---

## 4. Execution Guide

### Prerequisites
Pastikan `.env` sudah terisi dengan credential Supabase & Cloudflare R2.

### Step 1: Letakkan File Export CSV
```bash
mkdir -p storage/app/data
cp ~/Downloads/data-buku-angkatan.csv storage/app/data/input.csv
```

### Step 2: Run Seeder Command
```bash
php artisan seed:from-csv storage/app/data/input.csv
```

### Dry Run / Testing Mode
Uji sanitasi & mapping **tanpa** insert DB dan **tanpa** upload R2:
```bash
php artisan seed:from-csv storage/app/data/input.csv --dry-run
```

### Kontrak Output Command (spesifikasi)

Command harus menampilkan progres per baris dan ringkasan akhir:

```
Processing CSV: storage/app/data/input.csv
----------------------------------------
Row 1: Ahmad Subekti (21000123) OK
  - Sanitized WA: 628123456789
  - Uploaded formal photo: formal/21000123.jpg
Row 2: Budi Santoso (21000124) OK
...
----------------------------------------
Summary:
  Total rows: 122
  Successful: 120
  Failed: 2
  Duration: 45s
```

> Dalam mode `--dry-run`, kolom "Uploaded ..." diganti dengan keterangan "would upload", dan tidak ada penulisan ke DB.

---

## 5. Command Logic (Pseudocode)

### 5.1 Signature & Options

| Elemen | Nilai |
| :--- | :--- |
| Command name | `seed:from-csv` |
| Argumen wajib | `file` — path ke file CSV |
| Opsi | `--dry-run` — jalankan tanpa insert DB & upload R2 |

### 5.2 Alur Utama

1. Baca file CSV dari argumen; jika tidak ada → tampilkan error dan berhenti.
2. Baca baris pertama sebagai header kolom.
3. Iterasi setiap baris data:
   - Normalisasi & sanitasi setiap field sesuai Section 2 & 3.
   - Jalankan validasi baris (lihat Section 7).
   - Jika valid → proses insert (kecuali `--dry-run`).
   - Jika tidak valid → catat sebagai gagal, lanjut ke baris berikutnya.
   - Tampilkan progres per baris.
4. Setelah selesai → tampilkan ringkasan (total, sukses, gagal, durasi).

### 5.3 Alur Insert Satu Baris (transaksional)

1. Buka transaksi database.
2. Insert `students` → dapatkan ID.
3. Insert `student_contacts` (relasi ke ID).
4. Insert `student_profiles` (termasuk hasil upload foto).
5. Insert `student_emergencies`.
6. Commit transaksi.
7. Jika ada error → rollback, catat baris gagal.

> Transaksi memastikan **tidak ada data parsial** bila salah satu langkah gagal.

### 5.4 Alur Media (Google Drive → R2)

1. Terima URL Google Drive dari kolom CSV.
2. Unduh konten image (stream).
3. Jika unduhan gagal → catat warning, lanjut tanpa foto.
4. Bentuk object key mengikuti konvensi: `{tipe}/{nim}.jpg` (mis. `formal/21000123.jpg`).
5. Upload ke disk R2.
6. Ambil public URL dari CDN.
7. Simpan public URL ke kolom profil.

> **Alternatif**: proses upload foto bisa dilempar ke **queue job** agar command tidak blocking saat file besar.

---

## 6. Bootstrap Auth Data (Passcode & Admin Whitelist)

Selain data mahasiswa, ada dua data bootstrap yang perlu di-seed. Keduanya **bukan** dari CSV Google Form.

### A. Passcode Angkatan & Auth Epoch (`app_config`)

Disimpan sebagai bcrypt hash (bukan plaintext):

| key | value | deskripsi |
| :--- | :--- | :--- |
| `angkatan_passcode` | bcrypt hash passcode | Shared secret akses directory & galeri |
| `auth_epoch` | `1` (integer) | Epoch sesi; increment untuk mencabut semua sesi |

**Alur seeding (pseudocode):**
1. Jalankan seeder khusus auth (mis. `php artisan db:seed --class=AuthSeeder`).
2. Ambil passcode plaintext (dari input seeder / env khusus, **bukan** di-commit).
3. Hash passcode dengan bcrypt.
4. `upsert` baris `angkatan_passcode` di `app_config`.
5. `upsert` baris `auth_epoch` dengan nilai awal `1`.

```bash
php artisan db:seed --class=AuthSeeder
```

### B. Whitelist Admin (`admin_whitelist`)

Dikelola **via Filament Resource `AdminWhitelistResource`** atau langsung via SQL / Supabase Table Editor. Whitelist **per email individu**, bukan domain.

**Menambah admin (pseudocode):**
1. Tambahkan baris baru: `email` (lowercase), `display_name`, `is_active = true`.

**Mencabut akses admin (pseudocode — dua langkah):**
1. Set `is_active = false` pada baris `admin_whitelist` dengan email terkait.
2. Increment nilai `auth_epoch` di `app_config` → seluruh sesi admin aktif langsung mati.

---

## 7. Error Handling & Validation

### Validasi per Row (spesifikasi aturan)

| Kolom | Aturan |
| :--- | :--- |
| `NIM` | required, max:20, unique terhadap `students.nim` |
| `Nama Lengkap` | required, max:100 |
| `Kelas` | required, in: `A`, `B`, `C`, `D` |
| `Tanggal Lahir` | required, dapat diparse ke tanggal valid |
| `No WhatsApp Aktif` | hasil `sanitizeWhatsApp` tidak boleh `null` |

**Alur validasi per baris (pseudocode):**
1. Jalankan aturan validasi di atas terhadap data baris.
2. Jika gagal → tampilkan error baris, tandai gagal, lanjut.
3. Jika lolos → lanjut ke insert.

### Logging Failed Rows (pseudocode)
1. Untuk setiap baris gagal, catat: nomor baris, data mentah, dan pesan error.
2. Simpan ke channel log khusus (mis. `csv_import`) atau tabel log.

---

## 8. Performance Optimization

| Strategi | Kapan dipakai | Catatan |
| :--- | :--- | :--- |
| Insert per baris + transaksi | default | Aman, relasi lengkap, event Eloquent jalan |
| Batch insert | CSV sangat besar (>1000 baris) | Tidak memicu event Eloquent & tidak mengisi relasi — hanya untuk data awal |
| Queue untuk upload foto | saat file gambar besar/banyak | Command tidak blocking |

**Alur batch insert (pseudocode):**
1. Kumpulkan baris ke dalam batch (mis. 100 baris).
2. Saat batch penuh → insert massal, kosongkan batch.
3. Setelah loop → insert sisa batch.
4. Setelah semua, lakukan pass kedua untuk mengisi relasi (contacts/profiles/emergencies).

---

## 9. Post-Import Verification

Setelah import, verifikasi kelengkapan data (spesifikasi cek):

| Cek | Ekspektasi |
| :--- | :--- |
| Total `students` | sama dengan jumlah baris valid di CSV |
| Student tanpa `contact` | `0` |
| Student tanpa `profile` | `0` |
| Student tanpa `emergency` | `0` |
| URL foto formal/informal | semua terisi public URL yang valid |

Verifikasi dapat dilakukan via `php artisan tinker` atau query langsung ke Supabase.
