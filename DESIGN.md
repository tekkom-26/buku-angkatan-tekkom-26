# Design System Specification — ASCENT (Teknik Komputer 2026)

## 1. Platform & Form Factor
- Target Device: Responsive Web (Primary: Desktop 1440px, Mobile-friendly 390px)
- Visual Style: Modern Minimalist, High-craft Editorial, Bento Grid Layout
- Theme: Light Mode dominance with structured dark elements

## 2. Color System & Design Tokens
- Primary Brand: `#80bab2` (Sky Wash)
  - Interactive components, active states, pill selections, primary buttons
- Accent / Action: `#d4d86a` (Butter Cream)
  - Highlight tags, warning badges, notification pips, subtle hover borders
- Neutral Dark / Text: `#514051` (Deep Plum)
  - Typography (Headings & body copy), structural hairline borders, dark accents
- Neutral Light / Surface: `#e6e3e8` (Snow White)
  - Bento card background fills, pill chip backgrounds, secondary containers
- Canvas Background: `#FAFAFB` (Subtle off-white canvas for optimal contrast)

## 3. Typography & Micro Spacing
- Font Stack: Clean Sans-Serif (`Inter` or `Plus Jakarta Sans`)
  - Hero Display: 48px – 56px, Bold, Tracking -0.02em (`#514051`)
  - Section Headings: 28px – 36px, Semi-bold (`#514051`)
  - Body Text: 15px – 16px, Regular, 1.6 line height (`#514051` / 85% opacity)
  - Monospace Accents: `JetBrains Mono` or system mono for indices, tags, status labels
- Card Border Radius: 16px (`rounded-2xl`)
- Button / Pill Radius: 8px – 10px (`rounded-lg`)
- Hairline Borders: `1px solid rgba(81, 64, 81, 0.15)`
- Shadow Elevation: None or flat diffused shadow (`0 4px 20px rgba(81, 64, 81, 0.04)`)

## 4. Craft Directives & Anti-Slop Constraints
- Negative Rules:
  - Do NOT use generic purple/blue gradients or neon highlights.
  - Do NOT use generic 3D corporate vector art or stock floating blobs.
  - Do NOT generate repetitive, perfectly symmetrical 3-column card soups.
- Positive Craft Rules:
  - Asymmetric Bento Grid: Arrange layout modules with varied proportions (e.g., 2x2 featured card next to stacked 1x1 cards).
  - Editorial Indexing: Include small technical identifiers on cards and sections (e.g., `// 01_IDENTITY`, `[STATUS: VERIFIED]`, `BATCH_XXVI`).
  - Subtle Hardware Accents: Include hairline divider lines and minimalist crosshairs (`+`) at layout intersections.

## 5. Asset Placeholder Specs
- Logo Placeholder: Circular badge containing "XXVI - ASCENT"
- Mascot Placeholder: Rounded container (1:1 ratio) tagged "[Mascot Gajah Chibi - Tekkom]"
- Image Aspect Ratios: 16:9 for event gallery, 1:1 for student directory avatars

## 6. Project Architectural Contracts & Asset Boundaries

### A. Progressive Global Navbar States
Komponen Global Navbar bersifat baku (immutable structure), dengan tinggi 72px–80px, background `#FAFAFB`, dan border bawah `1px solid rgba(81, 64, 81, 0.12)`. Posisi blok logo di kiri dan daftar menu di tengah TIDAK BOLEH BERGESER saat status autentikasi berubah:
- **State A: Guest / Public State:**
  - Kiri: Wireframe box `[X] [ASSET: LOGO_XXVI]` + "ASCENT // TEKKOM 2026".
  - Tengah: Link navigasi ("Beranda", "Filosofi & Identitas", "Direktori", "Galeri") warna `#514051`.
  - Kanan: Tombol CTA primer `Akses Direktori →` (Solid Sky Wash `#80bab2`, teks `#514051` atau putih, `rounded-lg`).
- **State B: Authenticated / Unlocked Member State:**
  - Posisi logo dan menu tengah terkunci persis sama dengan State A.
  - Pojok kanan HANYA berubah menjadi: Status Chip Pill `[STATUS: INTERNAL_ACCESS_ACTIVE]` (warna `#80bab2` dengan status dot hijau/aktif) serta tombol aksi sekunder minimalis `Kunci Kembali [X]`.

### B. Standardized Fixed Global Footer (Grid 4-Kolom)
Struktur kolom Footer bersifat baku (immutable layout) berlatar Snow White `#e6e3e8` atau `#FAFAFB` dengan border atas `1px solid rgba(81, 64, 81, 0.15)` dan padding konsisten:
- **Kolom 1 (Brand & Alamat Resmi):** Logo wireframe `[X] [ASSET: LOGO_XXVI]`, identitas angkatan, alamat Departemen Teknik Komputer FT Undip Tembalang, dan link tautan peta `Google Maps ↗`.
- **Kolom 2 (Navigasi Cepat Internal):** Label monospace `// NAVIGASI` serta 4 link rute internal.
- **Kolom 3 (Kanal Resmi Eksternal):** Label monospace `// KANAL_RESMI` serta tautan polos monoline: Instagram, GitHub, LinkedIn (`↗`).
- **Kolom 4 (Telemetri Sistem Angkatan):** Monospace diagnostic box `[BATCH_STATUS: ACTIVE // COHORT_XXVI]` dan `[SECURITY_LEVEL: VERIFIED]`.

### C. Collapsible In-Page Side Navigation Drawer Component
Komponen widget mengambang (floating sticky widget) di sisi layar untuk navigasi cepat antar section:
- **State 1: Collapsed (Handle Minimalis):**
  - Tab vertikal menempel di tepi viewport dengan label monospace `[NAV]` dan ikon panah wireframe `[X] [ICON: CHEVRON_LEFT]`, border hairline `rgba(81, 64, 81, 0.2)`.
- **State 2: Expanded (Drawer Card Monokromatik Tajam):**
  - Kartu modular bergaris tegas dengan border solid `1px solid #514051` (atau `#000000`) dan background putih bersih (`#FFFFFF`).
  - Baris Header: Label `[PAGE NAV]` di sisi kiri dan `[IN-PAGE]` di sisi kanan, dipisahkan hairline divider.
  - Item List Berkurung Siku Monospace:
    - Item Aktif: `[1. HERO]` dengan tag `(ACTIVE)` (background hitam pekat `#1A1A1A` atau `#514051`, teks putih).
    - Item Pasif: `[2. STATS]`, `[3. VALUES]`, `[4. VISION]`, `[5. DIRECTORY]` dengan hover state lembut dan tracking monospace tajam.

### D. Content & Narrative Boundary
- Dilarang membuat/mengarang data narasi, teks filosofi, atau konten angkatan yang belum resmi.
- Semua bagian yang belum difinalisasi oleh tim narasi/konten wajib menggunakan teks terstruktur: "Lorem ipsum dolor sit amet...".

### E. Visual Assets & Icon Boundary (No AI Graphics)
- Dilarang menggunakan icon pack generik AI, ilustrasi 3D, atau dekorasi random. Seluruh aset grafis final akan dikerjakan manual oleh tim Desain Grafis.
- Seluruh kebutuhan aset visual (foto mahasiswa, foto event, maskot, logo, icon fungsional) wajib dirender sebagai Wireframe Box `[X]` bersilang diagonal dengan border hairline tipis dan label fungsional jelas (contoh: `[X] [ASSET: LOGO_XXVI]`, `[X] [AVATAR: 1:1]`, `[X] [ASSET: MASCOT_CHIBI]`).

## 7. Frontend Craft & Aesthetic Directives (Impeccable Standards)

### A. Absolute Visual Bans (Anti-AI Slop & Shallow Trends)
- **Zero Gradient Text:** Dilarang menggunakan `background-clip: text` atau gradien ungu-ke-pink/sian generik. Penekanan visual dicapai melalui bobot (`font-weight`), kontras ukuran, dan pilihan tipe huruf, bukan gradasi warna.
- **No Decorative Glassmorphism:** Dilarang menaburkan `backdrop-blur` atau efek kaca frosted di atas kanvas kosong tanpa alasan fungsional proteksi fokus. Blur dekoratif murahan membuat tampilan terkesan template AI instan.
- **No Symmetrical "Card-Soup":** Dilarang menyusun layout menggunakan kartu-kartu berukuran identik (ikon di atas + judul + 2 baris teks). Struktur antarmuka harus memiliki dinamika visual asimetris yang merefleksikan nilai hierarki data sebenarnya.
- **No Heavy Drop Shadows & Zero-Blur Block Shadows:** Hindari bayangan hitam tebal (`box-shadow: 0 20px 40px rgba(0,0,0,0.3)`). Jangan kombinasikan border 1px tebal dengan bayangan blur lebar ("ghost card"). Cukup deklarasikan elevasi satu kali: flat hairline border `rgba(81, 64, 81, 0.15)` atau diffused whisper shadow (`0 4px 20px rgba(81, 64, 81, 0.04)`).
- **No Emojis as System UI:** Emojis dilarang menggantikan ikon antarmuka. Navigasi, status gembok, dan aksi wajib menggunakan wireframe monoline SVG stroke tunggal atau functional labels.

### B. Typographic Hierarchy & Vertical Rhythm (Micro-Typography)
- **Tracking & Proportion:**
  - Display Headings (36px–56px): Tight tracking `-0.02em` hingga `-0.03em`. Dilarang melampaui `-0.04em`.
  - Body Text (14px–16px): Regular tracking normal (`0` hingga `+0.01em`), line-height ketat `1.5` – `1.6`, batas lebar pembacaan nyaman (measure 65–75 karakter).
  - Technical Monospace (`JetBrains Mono`): Tracking lebar `+0.05em` dengan penulisan uppercase khusus untuk indeks berkas, NIM, koordinat, dan metadata sistem (`// 02_AUTH`, `[STATUS: ACTIVE]`).
- **Vertical Spacing Discipline:**
  - Spasi di atas heading selalu lebih besar daripada spasi antara heading dan paragraf di bawahnya (`mt-8 mb-2`, bukan margin simetris atas-bawah).
  - Kelompok data terkait dikelompokkan rapat (tight grouping), sedangkan pemisah antar modul diberi ruang bernapas lega (generous separation).

### C. Asymmetric Bento Grid & Layout Physics
- **Editorial Proportions:** Susun container dengan rasio asimetris bernilai fungsional: modul utama 60% berhadapan dengan modul analitik/metadata bertumpuk 40% (rasio 2:1 atau 3:2).
- **Hardware & Blueprint Accents:** Terapkan aksen hairline horizontal tipis (`1px solid rgba(81, 64, 81, 0.15)`) serta penanda crosshair minimalis (`+`) pada titik persimpangan grid layout untuk mempertegas identitas teknis mahasiswa Teknik Komputer tanpa mengotori ruang visual.
- **Nested Card Discipline:** Dilarang membungkus kartu di dalam kartu bertingkat-tingkat tanpa perubahan level elevasi atau kontras surface yang masuk akal.

### D. Color Role Matrix & Surface Semantics
- **#514051 (Deep Plum) — Structural Authority:**
  - Bertindak sebagai jangkar kontras utama (contrast ratio ≥ 4.5:1 terhadap canvas). Digunakan mutlak untuk heading, body copy, border struktural, dan divider. Teks sekunder diturunkan opasitasnya (`70%–85%`), bukan diganti dengan warna abu-abu netral murahan.
- **#80bab2 (Sky Wash) — Intentional Interactivity:**
  - Digunakan secara selektif HANYA untuk elemen interaktif: background primary button aktif, border fokus input aktif, active filter tab, dan indikator audio aktif. Dilarang dijadikan background blok besar yang statis.
- **#d4d86a (Butter Cream) — Strict Signal & Alert:**
  - Dibatasi secara ketat hanya untuk pill badge otorisasi, status tag keamanan (`[CLEARANCE: LEVEL 02]`), dan pip notifikasi. Jangan pernah digunakan untuk teks paragraf atau background tombol interaksi utama.
- **#e6e3e8 (Snow White) — Tactile Surface:**
  - Menjadi warna dasar pengisi surface Bento card di atas kanvas `#FAFAFB`. Menghasilkan kedalaman visual subtle yang membedakan kanvas dasar dan ruang kerja konten.

### E. Browser Surface Polish
- **Selection Color:** Teks yang di-highlight/blok wajib diberi warna seleksi berkarakter sistem: `::selection { background: #80bab2; color: #514051; }`.
- **Caret & Focus Rings:** Caret input teks dan outline fokus keyboard wajib menggunakan rona `#80bab2` dengan `outline-offset: 2px`, bukan biru default bawaan peramban web.
- **Smooth State Transitions:** Semua interaksi hover dan focus menggunakan transisi kurva eksponensial yang halus (`transition: all 180ms cubic-bezier(0.16, 1, 0.3, 1)`).


