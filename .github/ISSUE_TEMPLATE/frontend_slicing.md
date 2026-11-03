---
name: "[SLICING] Frontend Component / Page"
about: Template standar pengerjaan slicing UI/UX berbasis spesifikasi DESIGN.md
title: "[SLICING]: <Nama Halaman / Modul Komponen>"
labels: ["slicing", "frontend", "ui/ux"]
assignees: ""
---

<!-- 
📌 PETUNJUK LEAD FRONTEND ARCHITECT:
- Isi semua section dengan lengkap sebelum memindahkan issue ke backlog/sprint aktif tim.
- Task slicing ini wajib mematuhi seluruh design token dan aturan anti-slop di DESIGN.md.
- Slicing harus lolos uji visual di breakpoint Desktop (1440px) dan Mobile (390px).
-->

## 🎯 1. Deskripsi & Scope Pekerjaan
- **Nama Komponen/Halaman**: `[contoh: Landing Page - Bento Grid Values Section]`
- **Target File / Path**: `[contoh: resources/views/components/landing/values-bento.blade.php]`
- **Tipe Scope**:
  - [ ] Halaman Penuh (Page View)
  - [ ] Komponen Modular Baru (Reusable Component)
  - [ ] Refactor / Polish Komponen Eksisting
  - [ ] Interaktivitas Frontend (Alpine.js / Livewire state)
- **Ringkasan Scope**: 
  > *Jelaskan secara ringkas modul apa yang di-slice, fungsinya, dan posisinya di hierarki tampilan.*

---

## 🎨 2. Referensi Desain & Breakpoint
- **Design System Source**: `DESIGN.md` (Teknik Komputer 2026 — ASCENT)
- **Figma Frame / Mockup Stitch**: `[Sematkan Link Frame Figma atau Path Screenshot Stitch]`
- **Target Breakpoint (Wajib Validasi)**:
  - [ ] **Desktop View**: `1440px` (Primary baseline layout)
  - [ ] **Mobile View**: `390px` (Mobile-friendly viewport, responsive tanpa overflow-x)
  - [ ] **Tablet/Laptop Adaptif**: `768px` - `1024px`

---

## 📐 3. Token Desain & Compliance Checklist (`DESIGN.md`)
*Beri tanda centang pada token yang digunakan dan pastikan nilainya sesuai spesifikasi:*

### A. Palet Warna (Strict Tokens)
- [ ] **Canvas Background**: `#FAFAFB` (Kanvas utama)
- [ ] **Neutral Dark / Text (Deep Plum)**: `#514051`
  - Heading & text utama (100% / `#514051`)
  - Secondary body text (`85%` opacity)
  - Structural hairline border (`rgba(81, 64, 81, 0.15)`)
- [ ] **Primary Brand (Sky Wash)**: `#80bab2`
  - HANYA untuk elemen interaktif (active primary button, focus ring, active filter tab, selected pill)
- [ ] **Accent / Action (Butter Cream)**: `#d4d86a`
  - HANYA untuk highlight badge, status tag otorisasi, clearance tag (`[CLEARANCE: LEVEL 02]`), dan notification pip
- [ ] **Neutral Light / Surface (Snow White)**: `#e6e3e8`
  - Surface background untuk Bento cards & secondary container chips

### B. Tipografi & Micro-Spacing
- [ ] **Font Family Body & Display**: Clean Sans-Serif (`Inter` / `Plus Jakarta Sans`)
  - Display Hero: `48px – 56px`, Bold, Tracking `-0.02em` (`#514051`)
  - Section Headings: `28px – 36px`, Semi-bold, Tracking `-0.02em` s/d `-0.03em` (`#514051`)
  - Body Text: `15px – 16px`, Regular, line-height `1.5` – `1.6` (`#514051` 85% opacity)
- [ ] **Monospace Family**: `JetBrains Mono` (Uppercase, tracking `+0.05em` untuk tag/indeks: `// 01_IDENTITY`, `[STATUS: VERIFIED]`, `BATCH_XXVI`)
- [ ] **Card Border Radius**: `16px` (`rounded-2xl`)
- [ ] **Button / Pill Radius**: `8px – 10px` (`rounded-lg`)
- [ ] **Hairline Borders**: `1px solid rgba(81, 64, 81, 0.15)`
- [ ] **Shadow Elevation**: None atau flat diffused whisper shadow (`0 4px 20px rgba(81, 64, 81, 0.04)`)

---

## 🛡️ 4. Anti-Slop & Craft Directives (Zero Tolerance)
*Setiap PR slicing yang melanggar poin berikut wajib direvisi sebelum merger:*
- [ ] **Zero Gradient Text**: Dilarang menggunakan `background-clip: text` atau gradien ungu/pink/cyan generik.
- [ ] **No Decorative Glassmorphism**: Dilarang menaburkan `backdrop-blur` / efek kaca frosted murahan tanpa alasan fungsional proteksi fokus.
- [ ] **Asymmetric Bento Grid**: Layout harus modular dan dinamis (rasio 60:40, 2:1, atau 3:2), BUKAN 3-card simetris identik ("card soup").
- [ ] **No Heavy Drop Shadows & Zero-Blur Block Shadows**: Hindari bayangan pekat (`box-shadow: 0 20px 40px rgba(0,0,0,0.3)`).
- [ ] **No Emojis as System UI**: Ikon wajib monoline SVG stroke tunggal atau functional labels berkurung siku.
- [ ] **Asset Wireframe Boundary**: Semua gambar/foto/maskot/logo yang belum final WAJIB menggunakan Wireframe Box `[X]` bersilang diagonal dengan label spesifik (contoh: `[X] [ASSET: LOGO_XXVI]`, `[X] [AVATAR: 1:1]`, `[X] [ASSET: MASCOT_CHIBI]`). Dilarang memakai ilustrasi 3D generik AI.
- [ ] **Content Integrity Boundary**: Dilarang mengarang narasi/filosofi angkatan. Gunakan teks resmi atau structured `Lorem ipsum dolor sit amet...`.

---

## ⚙️ 5. State Handling & Micro-Interactions
- [ ] **Default State**: Bersih, presisi, contrast ratio ≥ 4.5:1 terhadap canvas.
- [ ] **Hover State**: Elevasi subtle / hairline border highlight, transisi halus `cubic-bezier(0.16, 1, 0.3, 1)` (180ms).
- [ ] **Focus-Visible State**: Outline `#80bab2` dengan `outline-offset: 2px` (ramah aksesibilitas keyboard).
- [ ] **Selection Color**: `::selection { background: #80bab2; color: #514051; }`.
- [ ] **Interactive Transitions**: Tidak ada animasi transisi patah-patah atau delay lambat (> 250ms).

---

## 📋 6. Rincian Task Pengerjaan (Engineer Checklist)
- [ ] Buat / perbarui file Blade template di lokasi yang ditentukan.
- [ ] Susun struktur HTML semantik (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`).
- [ ] Terapkan kelas Tailwind CSS berbasis token `DESIGN.md`.
- [ ] Implementasikan layout asimetris Bento Grid untuk Desktop (`1440px`).
- [ ] Implementasikan adaptasi responsif mobile stacking yang rapi (`390px`).
- [ ] Render wireframe placeholder box `[X]` untuk semua slot grafis dan foto.
- [ ] Hubungkan script / event Alpine.js / Livewire (jika ada interaktivitas drawer/modal/filter).
- [ ] Jalankan code formatting: `vendor/bin/pint --dirty --format agent`.
- [ ] Jalankan frontend build check: `npm run build` (pastikan bebas error Vite manifest).

---

## 🧪 7. Kriteria Pengujian & Acceptance Criteria (Definition of Done)
1. **Visual Accuracy**: 100% presisi mengacu pada spesifikasi layout dan token warna di `DESIGN.md`.
2. **Zero Horizontal Overflow**: Tidak ada layout shift (CLS) atau scroll horizontal pada resolusi `390px` s/d `1440px+`.
3. **Accessibility Ready**: Focus states terlihat jelas saat menggunakan tombol `Tab`, kontrast warna teks memenuhi standar WCAG AA.
4. **Clean Codebase**: Bebas dari inline CSS kotor, class Tailwind duplikat, atau console error.
5. **PR Deliverable**: Melampirkan tangkapan layar (screenshot / screen recording) untuk **Desktop (1440px)** dan **Mobile (390px)** pada deskripsi Pull Request.
