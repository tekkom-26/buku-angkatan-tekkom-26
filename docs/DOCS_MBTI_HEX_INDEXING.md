# Dokumentasi Filosofi & Spesifikasi Teknis: 4-Bit Binary / Hexadecimal MBTI Indexing (`0x0`–`0xF`)

> Dokumen ini menjelaskan landasan filosofis mengapa **Direktori Angkatan COMETS 2026 (Teknik Komputer)** memakai sistem pengurutan (*indexing*) berbasis **alamat Biner 4-Bit / Heksadesimal (`0x0`–`0xF`)**, sekaligus menjadi kontrak teknis implementasi pada tabel `students`.

---

## 1. Filosofi & Materi Share untuk Angkatan

### Mengapa Tidak Pakai Urutan ID / Abjad / Popularitas?
- Urutan `id`, NIM, atau abjad kelas murni bersifat administratif, kurang mencerminkan karakter tiap individu di dalam angkatan.
- Sebaliknya, mengurutkan berdasarkan *populasi terbanyak ke paling langka* atau berdasar *peran kelompok tertentu* berisiko menimbulkan kesan hierarkis ("kelompok tertentu di depan, kelompok lain di belakang").
- Sebagai angkatan **Teknik Komputer 2026 (COMETS — Koloni Lebah Biner *Bee-ner*)**, kita membutuhkan sistem *indexing* yang **100% netral, objektif, matematis, dan merepresentasikan disiplin ilmu kita**: memetakan 4 dimensi MBTI layaknya **register digital 4-bit (`4-bit nibble`)**.

### Prinsip Logika Digital: Semua Bit Setara (`0` ≡ `1`)
Di arsitektur komputer dan sistem digital, bit `0` dan bit `1` memiliki **nilai kesetaraan penuh** — `0` bukan lebih rendah dari `1`, dan `1` bukan lebih unggul dari `0`. Keduanya sekadar koordinat biner pada tabel kebenaran (*Truth Table*) 4 variabel (`2⁴ = 16` alamat unik).

Mengikuti 4 sumbu kepribadian (`Energy · Mind · Nature · Tactics`):

| Posisi Bit | Bobot | Dimensi | Bit `0` | Bit `1` |
| :---: | :---: | :--- | :---: | :---: |
| **Bit 3 (MSB)** | `8` | Energi (*Extravert / Introvert*) | **`E`** | **`I`** |
| **Bit 2** | `4` | Cara Memproses Informasi (*Intuition / Sensing*) | **`N`** | **`S`** |
| **Bit 1** | `2` | Pengambilan Keputusan (*Thinking / Feeling*) | **`T`** | **`F`** |
| **Bit 0 (LSB)** | `1` | Gaya Eksekusi (*Judging / Perceiving*) | **`J`** | **`P`** |

### Tabel Lengkap Alamat Register Heksadesimal (`0x0`–`0xF`)

| Alamat Hex | Nilai DB (`mbti_hex_index`) | Biner 4-Bit | Kode MBTI | Arketipe | Komposisi Bit |
| :---: | :---: | :---: | :---: | :--- | :--- |
| **`0x0`** | `0` | `0000` | **`ENTJ`** | Commander | `E(0) · N(0) · T(0) · J(0)` |
| **`0x1`** | `1` | `0001` | **`ENTP`** | Debater | `E(0) · N(0) · T(0) · P(1)` |
| **`0x2`** | `2` | `0010` | **`ENFJ`** | Protagonist | `E(0) · N(0) · F(1) · J(0)` |
| **`0x3`** | `3` | `0011` | **`ENFP`** | Campaigner | `E(0) · N(0) · F(1) · P(1)` |
| **`0x4`** | `4` | `0100` | **`ESTJ`** | Executive | `E(0) · S(1) · T(0) · J(0)` |
| **`0x5`** | `5` | `0101` | **`ESTP`** | Entrepreneur | `E(0) · S(1) · T(0) · P(1)` |
| **`0x6`** | `6` | `0110` | **`ESFJ`** | Consul | `E(0) · S(1) · F(1) · J(0)` |
| **`0x7`** | `7` | `0111` | **`ESFP`** | Entertainer | `E(0) · S(1) · F(1) · P(1)` |
| **`0x8`** | `8` | `1000` | **`INTJ`** | Architect | `I(1) · N(0) · T(0) · J(0)` |
| **`0x9`** | `9` | `1001` | **`INTP`** | Logician | `I(1) · N(0) · T(0) · P(1)` |
| **`0xA`** | `10` | `1010` | **`INFJ`** | Advocate | `I(1) · N(0) · F(1) · J(0)` |
| **`0xB`** | `11` | `1011` | **`INFP`** | Mediator | `I(1) · N(0) · F(1) · P(1)` |
| **`0xC`** | `12` | `1100` | **`ISTJ`** | Logistician | `I(1) · S(1) · T(0) · J(0)` |
| **`0xD`** | `13` | `1101` | **`ISTP`** | Virtuoso | `I(1) · S(1) · T(0) · P(1)` |
| **`0xE`** | `14` | `1110` | **`ISFJ`** | Defender | `I(1) · S(1) · F(1) · J(0)` |
| **`0xF`** | `15` | `1111` | **`ISFP`** | Adventurer | `I(1) · S(1) · F(1) · P(1)` |
| **`—`** | `NULL` | *Unallocated* | *Belum Mengisi* | *Pending Address* | Ditempatkan setelah `0xF` |

> **Ringkasan Share ke Teman:**
> *"Kenapa urutan Direktori COMETS 2026 nggak pakai ID atau abjad kelas biasa? Karena kita anak Tekkom! 4 dimensi MBTI (`E/I · N/S · T/F · J/P`) membentuk **4-bit biner (`0000`–`1111`) = alamat Heksadesimal `0x0` sampai `0xF`**. Di logika digital, bit `0` dan `1` sepenuhnya setara — tidak ada tipe yang lebih unggul atau disudutkan. Setiap anggota koloni Bee-ner punya alamat register yang unik & setara!"*

---

## 2. Arsitektur Database & Sinkronisasi (Anti-Fragile Design)

Agar pengurutan tidak bergantung pada multi-table `JOIN` atau kalkulasi rawan di memori saat query database berjalan:
1. **Tambahkan kolom terindeks langsung di tabel `students`:**
   - `mbti_hex_index` (`unsignedTinyInteger`, `nullable`, `index()`), menyimpan nilai `0..15` (`NULL` untuk mahasiswa tanpa data MBTI).
   - Tabel `mbti_types` & `student_mbti_profiles` tetap menjadi *single source of truth* untuk skor persentase (`pct_e..pct_p`), arketipe, dan link `screenshot_url` admin.
2. **Sinkronisasi Otomatis via `StudentMbtiProfileObserver`:**
   - Saat profil MBTI dibuat/diubah (`saved`), observer otomatis menghitung nilai `0..15` dari kode MBTI dan memperbarui kolom `students.mbti_hex_index` secara langsung (lalu membersihkan cache direktori).
   - Saat profil MBTI dihapus (`deleted`), observer mengatur kembali `students.mbti_hex_index = null`.
3. **Query Cross-Database Aman (SQLite & PostgreSQL):**
   - Pengurutan memakai `orderByRaw('CASE WHEN mbti_hex_index IS NULL THEN 1 ELSE 0 END ASC')` -> `orderBy('mbti_hex_index', 'asc')` -> *tie-breaker* yang sudah ada (`orderBy('class_name', 'asc')->orderBy('name', 'asc')`).
   - Penulisan `CASE WHEN ... IS NULL` menjamin `NULL` selalu berada di akhir (`after 0xF`) baik di SQLite lokal maupun PostgreSQL produksi.
