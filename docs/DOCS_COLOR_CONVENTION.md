# Design Token & Coloring Convention — ASCENT (Teknik Komputer 2026)

> **Status:** Active Standard  
> **Role:** Frontend Architect & Design System Specialist  
> **Target Audience:** Tim UI/UX Designer, Frontend Slicing Engineer, & Full-Stack Developer  
> **Single Source of Truth (SSOT):** `:root` CSS Custom Properties di [`resources/css/app.css`](resources/css/app.css)

---

## 1. Filosofi & Latar Belakang

Palet warna angkatan Teknik Komputer 2026 saat ini berada dalam tahap finalisasi dan berpotensi mengalami penyesuaian nilai *hex/shade*. Untuk mencegah terjadinya **refactor massal (code churn)** pada ratusan komponen dan file Blade/JSX ketika penyesuaian warna terjadi, arsitektur frontend mengadopsi prinsip **Single Source of Truth (SSOT)**:

1. **Abstraksi Berjenjang (Layered Abstraction):**
   - **Tingkat 1 (Raw Values):** Nilai HEX/RGBA fisik hanya dideklarasikan satu kali di dalam blok `:root` pada stylesheet global ([`resources/css/app.css`](resources/css/app.css)).
   - **Tingkat 2 (Semantic Tokens):** Tailwind CSS dan TypeScript mengikat variabel CSS tersebut ke dalam token peran semantik berbobot kontras (`--color-[family]-[weight]`).
   - **Tingkat 3 (Component Slicing):** Pengembang hanya menggunakan utility class Tailwind semantik (misal: `bg-secondary-500`, `text-primary-900`).

2. **Dampak Bagi Tim Slicing:**
   Jika di kemudian hari desainer memutuskan shade Sky Wash berubah dari `#80bab2` menjadi `#72b0a7`, pengembang **TIDAK PERLU** mencari dan mengganti kode di ratusan komponen. **Cukup ubah 1 baris di `:root`**, dan seluruh aplikasi otomatis terbarui secara seragam.

---

## 2. Aturan Batasan Tegas (*Strict Boundaries*)

Setiap anggota tim slicing **WAJIB** mematuhi aturan berikut dalam *code review* dan *pull request*:

| Status | Aturan | Contoh Pelanggaran / Kepatuhan |
| :---: | :--- | :--- |
| ❌ **DILARANG KERAS** | Menggunakan arbitrary hex atau rgb langsung di class HTML/Blade/JSX | `bg-[#80bab2]`, `text-[#514051]`, `border-[rgba(81,64,81,0.12)]` |
| ❌ **DILARANG** | Menggunakan inline CSS `style="..."` untuk mewarnai teks/latar | `<div style="background-color: #FAFAFB">` |
| ❌ **DILARANG** | Menamai class berdasarkan persepsi warna subjektif (misal: `bg-plum`, `text-teal`) | `text-plum-dark`, `bg-cream-yellow` |
| ✅ **WAJIB** | Menggunakan utility class semantik berbasis bobot kontras | `bg-surface-canvas`, `text-primary-900`, `bg-secondary-500` |
| ✅ **WAJIB** | Menggunakan modifier opacity Tailwind jika membutuhkan transparansi | `text-primary-900/75`, `bg-secondary-500/15`, `border-surface-border` |

---

## 3. Spesifikasi Skala Warna & Pemetaan Token

### A. Primary Family — Deep Plum (Teks & Kontras Struktural)
Digunakan untuk hierarki tipografi, teks heading, teks konten utama, dan elemen berbobot gelap.

| Token Semantik | Bobot | Hex Baseline | CSS Custom Property | Contoh Tailwind Class | Rekomendasi Penggunaan |
| :--- | :---: | :---: | :--- | :--- | :--- |
| `primary-900` | 900 | `#514051` | `--color-primary-900` | `text-primary-900` | Teks heading display, title utama, border tajam |
| `primary-800` | 800 | `#645064` | `--color-primary-800` | `text-primary-800` | Sub-heading, hover state teks primer |
| `primary-700` | 700 | `#786178` | `--color-primary-700` | `text-primary-700` | Teks sekunder, deskripsi paragraf, caption |
| `primary-500` | 500 | `#907990` | `--color-primary-500` | `text-primary-500` | Metadata label, teks muted, placeholder |
| `primary-100` | 100 | `#e2dce2` | `--color-primary-100` | `bg-primary-100` | Surface kontras plum sangat lembut |
| `primary-50`  | 50  | `#f4f1f4` | `--color-primary-50`  | `bg-primary-50`  | Tint latar belakang plum ultra-halus |

---

### B. Secondary Family — Sky Wash (Action & Link Aktif)
Warna aksen interaktif utama untuk CTA, indikator aktif, tautan, dan elemen fokus.

| Token Semantik | Bobot | Hex Baseline | CSS Custom Property | Contoh Tailwind Class | Rekomendasi Penggunaan |
| :--- | :---: | :---: | :--- | :--- | :--- |
| `secondary-900` | 900 | `#3b655f` | `--color-secondary-900` | `bg-secondary-900` | State tombol ditekan (active/pressed), teks kontras tinggi |
| `secondary-700` | 700 | `#548981` | `--color-secondary-700` | `bg-secondary-700` | State tombol hover, icon interaktif |
| `secondary-500` | 500 | `#80bab2` | `--color-secondary-500` | `bg-secondary-500` | **Warna Sky Wash utama**: Tombol CTA, chip aktif, link |
| `secondary-300` | 300 | `#aed4ce` | `--color-secondary-300` | `border-secondary-300` | Border aksen lembut, focus ring |
| `secondary-100` | 100 | `#e0efec` | `--color-secondary-100` | `bg-secondary-100` | Latar belakang badge status aktif, chip pill |
| `secondary-50`  | 50  | `#f2f8f7` | `--color-secondary-50`  | `bg-secondary-50`  | Latar hover baris tabel, wash container |

---

### C. Highlight Family — Butter Cream (Badge & Accent Khusus)
Warna aksen hangat untuk badge peringatan, penanda khusus, highlight, dan aksen ceria.

| Token Semantik | Bobot | Hex Baseline | CSS Custom Property | Contoh Tailwind Class | Rekomendasi Penggunaan |
| :--- | :---: | :---: | :--- | :--- | :--- |
| `highlight-900` | 900 | `#6b6f1e` | `--color-highlight-900` | `text-highlight-900` | Teks di dalam badge highlight untuk rasio kontras WCAG |
| `highlight-700` | 700 | `#9fa436` | `--color-highlight-700` | `text-highlight-700` | Border badge highlight pekat |
| `highlight-500` | 500 | `#d4d86a` | `--color-highlight-500` | `bg-highlight-500` | **Warna Butter Cream utama**: Badge tag, status penting |
| `highlight-300` | 300 | `#e4e79b` | `--color-highlight-300` | `bg-highlight-300` | Tag sekunder lembut |
| `highlight-100` | 100 | `#f5f6db` | `--color-highlight-100` | `bg-highlight-100` | Latar belakang alert informasi hangat |

---

### D. Surface & Neutral Family — Snow White & Canvas
Kanvas dasar, latar belakang kartu bento, kontainer, dan garis pemisah struktur (*hairline divider*).

| Token Semantik | Hex / RGBA Baseline | CSS Custom Property | Contoh Tailwind Class | Rekomendasi Penggunaan |
| :--- | :---: | :--- | :--- | :--- |
| `surface-canvas` | `#FAFAFB` | `--color-surface-canvas` | `bg-surface-canvas` | **Kanvas utama halaman web** (body background) |
| `surface-subtle` | `#f5f4f6` | `--color-surface-subtle` | `bg-surface-subtle` | Latar input field, container bento sekunder |
| `surface-card`   | `#e6e3e8` | `--color-surface-card`   | `bg-surface-card`   | **Snow White utama**: Latar kartu bento, drawer |
| `surface-border` | `rgba(81, 64, 81, 0.12)` | `--color-surface-border` | `border-surface-border` | **Hairline divider standard (1px)** |

---

## 4. Tabel Panduan Penggunaan Elemen UI

Gunakan panduan berikut saat menyusun antarmuka komponen:

| Elemen UI | Utility Class yang Direkomendasikan | Catatan Desain |
| :--- | :--- | :--- |
| **Background Body / Halaman** | `bg-surface-canvas` | Latar kanvas bersih off-white, kontras optimal untuk kartu |
| **Kartu Bento (Container)** | `bg-surface-card border border-surface-border rounded-2xl` | Menghasilkan struktur bersih tanpa bayangan tebal |
| **Teks Display / Judul Halaman** | `text-primary-900 font-extrabold tracking-tight` | Kontras tajam Deep Plum |
| **Teks Sekunder / Meta Tag** | `text-primary-700` atau `text-primary-500` | Menjaga hierarki keterbacaan (*visual hierarchy*) |
| **Tombol CTA Utama** | `bg-secondary-500 hover:bg-secondary-700 text-white font-semibold rounded-lg shadow-sm transition-colors` | Action solid berbasis Sky Wash |
| **Tombol Sekunder (Outline)** | `border border-surface-border hover:bg-surface-subtle text-primary-900 rounded-lg` | Tombol pendukung dengan border halus |
| **Badge / Tag Highlight** | `bg-highlight-500 text-primary-900 font-medium px-2.5 py-1 rounded-full text-xs` | Memastikan kontras teks tetap terbaca dengan jelas |
| **Badge / Chip Aktif** | `bg-secondary-100 text-secondary-900 font-medium px-3 py-1 rounded-full text-xs` | Menandai status aktif atau kategori terpilih |
| **Hairline Divider / Garis Batas** | `border-b border-surface-border` | Pengganti border hitam pekat atau bayangan murahan |
| **Input Form** | `bg-white border border-surface-border focus:border-secondary-500 focus:ring-1 focus:ring-secondary-500` | Input bersih dengan umpan balik visual jelas |

---

## 5. Dukungan Opacity Channel (*Alpha Modifiers*)

Tailwind CSS dikonfigurasikan agar mengenali modifier opacity secara native. Anda dapat menambahkan modifier slash (`/opacity`) langsung pada token semantik:

```html
<!-- Background Sky Wash dengan opasitas 15% untuk badge halus -->
<span class="bg-secondary-500/15 text-secondary-900">Tag Aktif</span>

<!-- Teks Deep Plum dengan opasitas 75% untuk keterangan -->
<p class="text-primary-900/75 text-sm">NIM: 21120126120037</p>

<!-- Hairline border dengan opasitas 50% untuk divider ekstra halus -->
<hr class="border-surface-border/50" />
```

---

## 6. Integrasi Kode & Arsitektur File

Konfigurasi token warna terpasang di file-file berikut:

1. **Stylesheet Utama:** [`resources/css/app.css`](resources/css/app.css)  
   Mendefinisikan `:root` CSS Custom Properties dan layer `@theme` Tailwind v4.
2. **Tailwind Config:** [`tailwind.config.ts`](tailwind.config.ts)  
   Menyediakan mapping objek token untuk tooling eksternal, IDE autocomplete, dan backward compatibility.
3. **Helper & Type Definitions:** [`src/constants/colors.ts`](src/constants/colors.ts)  
   Konstanta JavaScript/TypeScript untuk kebutuhan programmatic (misal: grafik canvas, SVG generator, dynamic styling).

### Contoh Penggunaan Helper di TypeScript:
```ts
import { COLOR_PALETTE, getCssVar } from '@/constants/colors';

// Mengambil variabel CSS untuk inline style dinamis atau SVG
const primaryColor = getCssVar('primary', 900); // => "var(--color-primary-900)"

// Mengakses nilai HEX baseline jika diperlukan oleh pustaka grafis pihak ketiga (misal: Chart.js)
const rawSkyWash = COLOR_PALETTE.secondary[500]; // => "#80bab2"
```

---

## 7. Prosedur Perubahan Palet (*Theme Update Procedure*)

Jika tim desain grafis/BPH angkatan merilis pembaruan palet warna resmi:

1. Buka file [`resources/css/app.css`](resources/css/app.css).
2. Temukan blok `:root` di bagian atas file.
3. Ubah nilai HEX pada baris token yang bersangkutan (misal mengganti `--color-secondary-500: #80bab2;` menjadi nilai baru).
4. Jalankan kompilasi frontend:
   ```bash
   npm run build
   ```
5. **Selesai!** Seluruh halaman, tombol, badge, teks, dan kartu akan langsung mengikuti warna baru tanpa ada satu baris kode komponen pun yang perlu diubah.
