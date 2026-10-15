# Rencana Rapat Perdana — Web Buku Angkatan

Dokumen ini adalah **panduan agenda rapat perdana** tim pengembang Web Buku Angkatan. Tujuannya menyamakan visi, mengunci keputusan teknis, dan membagi peran sebelum masuk ke fase eksekusi.

> 📌 **Cara pakai:** jadikan dokumen ini sebagai notulen hidup. Setiap keputusan yang disepakati dicatat di kolom Keputusan; setiap tindak lanjut masuk ke Action Items dengan PIC + deadline.

---

## 1. Tujuan & Output Rapat

**Tujuan:** menyamakan pemahaman tim tentang arsitektur, fitur, tech stack, alur kerja, dan kepemilikan infrastruktur.

**Output wajib (minimal 6):**

1. ✅ **Scope MVP** — daftar fitur wajib vs ditunda.
2. ✅ **Tech stack final** — sudah dikunci: Laravel 13 + Filament v5 (tinggal konfirmasi).
3. ✅ **Alur kerja** — ticketing, Git/commit, AI usage, komunikasi.
4. ✅ **Akun & infra** — status + PIC tiap layanan.
5. ✅ **Pembagian task** — Epic → PIC (rujuk `IMPLEMENTATION_PLAN.md`).
6. ✅ **Skema database** — disepakati atau dijadwalkan review-nya.

---

## 2. Persiapan Sebelum Rapat (PR semua anggota)

| Siapa | Tugas persiapan |
| :--- | :--- |
| **Semua** | Pelajari minimal 1–2 bukang angkatan sebelumnya. Catat fitur + kesan. |
| **Calon backend** | Baca `docs/ARCHITECTURE.md` & `docs/API_CONTRACTS.md`. Familiarisasi dengan Laravel + Filament v5. |
| **Calon frontend/UI** | Kumpulkan referensi UI/UX buku angkatan / platform sejenis. Baca dasar Blade + Tailwind. |
| **Semua** | Siapkan 1 pertanyaan atau kekhawatiran pribadi. |

---

## 3. Alur Rapat (Rekomendasi 90–120 Menit)

```
[FASE 1] Arsitektur & Fitur        (25 mnt)
[FASE 2] Konfirmasi Tech Stack      (15 mnt)  ← sudah dikunci, tinggal validasi
[FASE 3] Alur Kerja Tim             (20 mnt)
[FASE 4] Akun & Infra Angkatan      (15 mnt)
─────────────────────────────────────────────
[FASE 5] Review Dokumentasi         (10 mnt)
[FASE 6] Pembagian Task             (15 mnt)
[FASE 7] Skema Database             (15 mnt)
[FASE 8] Kesimpulan & Action Items  (10 mnt)
```

---

# FASE 1 — Arsitektur & Fitur (±25 menit)

## 1a. Fitur apa saja yang ada di bukang sebelumnya?

Inventarisasi fitur eksisting.

| Fitur | Ada di bukang sebelumnya? | Cara kerja / catatan |
| :--- | :---: | :--- |
| Landing page | ? | |
| Direktori mahasiswa | ? | |
| Detail profil per orang | ? | |
| Galeri foto angkatan | ? | |
| Pencarian / filter kelas | ? | |
| Statistik angkatan | ? | |
| Kontak darurat | ? | |
| _(tambahkan)_ | ? | |

**Pertanyaan pemandu:**
- Fitur mana yang paling sering dipakai?
- Fitur mana yang jadi identitas buku angkatan?
- Apa keluhan / titik lemah versi lama?

## 1b. Fitur apa yang bisa kita tiru?

| Fitur tiruan | Sumber | Kenapa layak | Modifikasi kita |
| :--- | :--- | :--- | :--- |
| | | | |

**Pertanyaan pemandu:**
- Relevankah dengan data Google Form yang kita punya?
- Masuk **MVP** atau **nice-to-have**?
- Realistis dengan jumlah tim & waktu yang ada?

## 1c. Fitur yang perlu dievaluasi + solusinya

| Fitur bermasalah | Masalah | Akar masalah | Solusi kita |
| :--- | :--- | :--- | :--- |
| | | | |

**Contoh yang mungkin muncul:**
- *Data pribadi (WA/alamat) kepublik* → solusi: 3-tier access (guest/student/admin).
- *Semua orang bisa edit* → solusi: admin-only mutation + audit log.
- *Gambar berat / load lama* → solusi: Cloudflare R2 + presigned upload.

## 1d. Definisi Scope (WAJIB)

Sepakati tiga daftar:

- ✅ **MVP (wajib ada)** — mis. Landing, Directory, Galeri, Detail, Auth passcode, Admin.
- 🟡 **Fase 2 (kalau sempat)** — mis. Export CSV, statistik lanjutan.
- ❌ **Out of scope** — mis. fitur chat, komentar, social feed.

> ⚠️ **Risiko utama rapat perdana:** terlalu banyak fitur → tidak ada yang selesai. Batasi MVP.

---

# FASE 2 — Konfirmasi Tech Stack (±15 menit)

Stack **sudah diputuskan** pada sesi sebelumnya. Fase ini untuk validasi & pemahaman bersama (rujuk `docs/ARCHITECTURE.md`):

| Layer | Teknologi final | Alasan |
| :--- | :--- | :--- |
| Framework | Laravel 13 | Framework PHP populer, ekosistem matang, ramah pemula |
| Admin Panel | Filament v5 | CRUD & dashboard cepat, TALL stack |
| UI Layer | Livewire v4 + Tailwind v4 | Reactive tanpa banyak JS (dibundel Filament) |
| Bahasa | PHP 8.3+ | Strict types, enums, modern syntax |
| Database | Supabase PostgreSQL (Free) | Gratis, hosted, powerful |
| ORM | Eloquent ORM | Native Laravel, mudah dipelajari |
| Storage | Cloudflare R2 (Free) | Murah, S3-compatible, CDN |
| Email | Resend | OTP admin, transactional |
| Auth | Laravel Sanctum | Token-based, first-party Laravel |
| Validasi | Form Requests | Native Laravel, terintegrasi |
| Public Frontend | Blade Templates | Server-side rendering, ramah pemula |
| Deployment | Vercel | Free tier, serverless PHP |

**Pertanyaan diskusi:**

1. Apakah tim nyaman dengan stack ini? Ada yang belum pernah pakai Laravel / Filament / Blade? Siapa mentor?
2. Apakah ada bagian yang **terlalu rumit**?
   - *Custom Sanctum token + OTP* — alternatif: Breeze starter kit (lebih simpel, tapi kurang fleksibel untuk OTP).
   - *Cloudflare R2* — alternatif: Supabase Storage (satu platform, limit lebih kecil).
   - *Filament* — alternatif: CRUD manual dengan Blade (lebih banyak kerja).
3. **Kriteria ubah stack:**
   - ✅ Ubah hanya jika ada **blocker teknis nyata** atau mayoritas tim tidak bisa pakai.
   - ❌ Jangan ubah hanya karena "kelihatan keren".

## 2b. Decision Matrix (jika ada usulan alternatif)

| Opsi | Effort belajar | Kesesuaian fitur | Dukungan tim | Keputusan |
| :--- | :---: | :---: | :---: | :--- |
| Laravel 13 + Filament v5 (final) | | | | ✅ |
| Filament v4 (stabil lama) | | | | |
| Ganti Auth → Laravel Breeze | | | | |
| _(tambahkan)_ | | | | |

**Aturan resolusi konflik:** jika seri, putuskan oleh **lead backend** (tunjuk di rapat ini).

## 2c. Output Fase 2

- ✅ Stack **final** + alasan (sudah terkunci).
- ✅ Daftar perubahan dari `docs/ARCHITECTURE.md` (jika ada) → catat untuk diupdate.
- ✅ Konfirmasi kesiapan tim (siapa butuh onboarding).

---

# FASE 3 — Kesepakatan Alur Kerja (±20 menit)

## 3a. Task Ticketing

| Poin | Yang harus disepakati |
| :--- | :--- |
| **Tool** | GitHub Issues / Projects (rekomendasi gratis & simpel), Trello, Notion, Linear |
| **Struktur task** | Satu task = satu Epic di `IMPLEMENTATION_PLAN.md`? Sub-task? |
| **Status** | Backlog → In Progress → Review → Done |
| **Owner** | Setiap task punya **1 PIC** (kecuali pairing) |
| **Estimasi** | Poin S/M/L (rekomendasi) atau jam |
| **Definition of Done** | Kode + docs + lolos `pint`/test + tidak bocor data sensitif |

## 3b. Commit & Git Flow (konfirmasi `docs/CONTRIBUTING.md`)

| Aturan | Detail |
| :--- | :--- |
| Branch utama | `main` (produksi), `develop` (integrasi) |
| Branch kerja | `feat/`, `fix/`, `docs/`, `chore/` |
| Commit | Conventional Commits (`feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`) |
| PR | Minimal 1 reviewer, judul ikut format commit, `php artisan test` + `pint --test` lolos |
| Merge | PR ke `develop`, jangan langsung ke `main` (kecuali hotfix) |

**Pertanyaan pemandu:**
- Siapa reviewer tetap (boleh bergilir)?
- Wajib PR atau boleh push langsung saat eksperimen? (rekomendasi: wajib PR untuk `develop`)
- Bagaimana menangani konflik merge?

## 3c. AI Usage Rules

| Aspek | Yang disepakati |
| :--- | :--- |
| **Boleh dipakai untuk** | Boilerplate, refactor, dokumentasi, debugging, test |
| **Harus lewat manusia** | Keputusan arsitektur, skema DB, perubahan auth/security |
| **Wajib direview** | Semua kode AI dibaca & dipahami sebelum commit |
| **Dilarang** | Memasukkan secret/API key ke prompt; commit kode yang tidak dipahami |
| **Transparansi** | Tandai PR hasil AI? (opsional, sesuai kesepakatan tim) |

Rujuk juga **Vibe Coding Rules** di `docs/CONTRIBUTING.md`: strict types, privacy-first, audit everything sensitive, plan-before-code.

## 3d. Komunikasi

- Channel utama: (WA grup / Discord / Slack).
- Daily standup: async chat atau meeting?
- Jam respon cepat vs santai?
- Lokasi notulen rapat (rekomendasi: `docs/`).

---

# FASE 4 — Akun & Infra Angkatan (±15 menit)

## 4a. Inventaris Akun

| Layanan | Status | Pemilik akun | Catatan |
| :--- | :---: | :--- | :--- |
| **GitHub repository** | ? | ? | Public/private? Siapa admin org? |
| **Supabase** | ? | ? | Project sudah dibuat? Region? |
| **Cloudflare R2** | ? | ? | Bucket `buku-angkatan-assets`? Custom domain? |
| **Resend** | ? | ? | Domain pengirim terverifikasi? |
| **Vercel** | ? | ? | Project sudah dibuat? |
| **Domain (opsional)** | ? | ? | Sudah beli? Untuk apa? |

## 4b. Poin Krusial

1. **Kepemilikan akun** — gunakan akun angkatan, bukan akun pribadi.
   - ⚠️ Risiko: memakai email pribadi → akses hilang saat orang keluar.
   - Solusi: buat email angkatan khusus sebagai owner semua akun.
2. **Manajemen kredensial** — minimal orang pegang kredensial sensitif (`APP_KEY`, DB password, R2 keys). Gunakan password manager bersama.
3. **Budget** — semua di free tier; siapkan rencana jika lewat limit (Supabase free 500MB DB, R2 free 10GB storage).
4. **Backup** — data Google Form adalah aset berharga; simpan backup di luar Supabase.
5. **Etika & Privasi** — data berisi kontak darurat; tetapkan siapa boleh akses (admin saja) dan apakah perlu izin.

## 4c. Output Fase 4

- ✅ Tabel akun terisi (status + PIC).
- ✅ Email angkatan khusus dibuat (action item).
- ✅ Password manager dipilih.

---

# FASE 5 — Review Dokumentasi Project (±10 menit)

Validasi `docs/` sebagai **Source of Truth**.

| Dokumen | Isi | Status |
| :--- | :--- | :---: |
| `ARCHITECTURE.md` | ERD, RLS, RBAC, auth flow (Sanctum), env | ✅ Laravel 13 |
| `API_CONTRACTS.md` | Spesifikasi endpoint + JSON contract | ✅ Laravel 13 |
| `SEEDING_GUIDE.md` | Migrasi CSV Google Form (Artisan command) | ✅ Laravel 13 |
| `CONTRIBUTING.md` | Git Flow, commit, Vibe Coding | ✅ Laravel 13 |
| `PROJECT_PLAN.md` | Roadmap milestone | ✅ Laravel 13 |
| `IMPLEMENTATION_PLAN.md` | 13 Epic + task checklist | ✅ Laravel 13 |

**Aturan:** perubahan arsitektur/DB → **update docs dulu, baru kode**.

---

# FASE 6 — Pembagian Task (±15 menit)

Map 13 Epic di `docs/IMPLEMENTATION_PLAN.md` ke owner.

| Epic | Deskripsi | PIC | Deadline |
| :--- | :--- | :--- | :--- |
| 1 | Foundation & Infrastructure | ? | ? |
| 2 | Database Schema & Migration | ? | ? |
| 3 | Eloquent Models & Enums | ? | ? |
| 4 | Validation Layer (Form Requests) | ? | ? |
| 5 | Auth: Angkatan Passcode | ? | ? |
| 6 | Auth: Admin Whitelist + OTP | ? | ? |
| 7 | Audit & Security Helpers | ? | ? |
| 8 | Public Pages & API (Landing) | ? | ? |
| 9 | Protected Pages & API (Directory) | ? | ? |
| 10 | Admin Panel Filament v5 | ? | ? |
| 11 | Storage: Cloudflare R2 | ? | ? |
| 12 | CSV Ingestion & Seeder | ? | ? |
| 13 | Deployment & Production | ? | ? |

**Pertanyaan pemandu:**
- Berapa orang tersedia? Bagaimana beban dibagi seimbang?
- Ada dependency antar-Epic? (mis. Epic 5 butuh Epic 2 selesai)
- Urutan mulai: biasanya DB schema → auth → API → panel.

---

# FASE 7 — Skema Database (±15 menit)

**Status saat ini:** migrasi & model **belum** dibuat (akan dikerjakan di Epic 2 & 3).

**Yang dibahas:**
- Review rancangan 8 tabel (`ARCHITECTURE.md` Section 2):
  `students`, `student_contacts`, `student_profiles`, `student_emergencies`, `app_config`, `admin_whitelist`, `admin_otp`, `audit_logs`.
- **Pertanyaan:**
  - Field sudah sesuai data Google Form?
  - Enum `class` & `religion` sudah cocok? (implementasi via PHP Enum + kolom string)
  - Field mana yang **sensitif** (admin-only)?
  - Siapa yang review schema sebelum di-migrate?

**Aturan:** schema = kontrak; perubahan setelah data masuk mahal. Review dulu.

---

# FASE 8 — Kesimpulan & Action Items (±10 menit)

## Ringkasan Keputusan

| Topik | Keputusan |
| :--- | :--- |
| Scope MVP | |
| Tech stack | Laravel 13 + Filament v5 (final) |
| Public frontend | Blade Templates |
| Alur kerja | |
| Akun & infra | |
| Skema database | |

## Action Items

| # | Action | PIC | Deadline |
| :--- | :--- | :--- | :--- |
| 1 | Buat email angkatan khusus | ? | ? |
| 2 | Setup Supabase project | ? | ? |
| 3 | Init Laravel 13 + Filament v5 | ? | ? |
| 4 | Buat GitHub repo/org | ? | ? |
| 5 | Update docs (jika ada perubahan) | ? | ? |
| 6 | _(tambahkan)_ | ? | ? |

**Penutup:** tetapkan tanggal rapat berikutnya dan lokasi penyimpanan notulen.

---

## 4. Jebakan yang Harus Dihindari

| Jebakan | Solusi |
| :--- | :--- |
| Debat tech stack tanpa ujung | Stack sudah dikunci; fokus eksekusi |
| Semua mau pegang semua | 1 task = 1 PIC |
| Scope melebar (scope creep) | Kunci MVP di awal |
| Pakai akun pribadi untuk infra | Email angkatan khusus |
| Skema DB diubah setelah data masuk | Review schema sebelum migrate |
| Kode AI di-copy tanpa dipahami | Aturan review wajib |
| Tidak ada notulen | Simpan di `docs/` |

---

## 5. Template Notulen (siap isi)

```
Tanggal   : ____
Peserta   : ____
Notulis   : ____

--- KEPUTUSAN ---
1. Scope MVP        : ____
2. Tech stack       : Laravel 13 + Filament v5
3. Public frontend  : Blade Templates
4. Alur kerja       : ____
5. Akun & infra     : ____
6. Skema database   : ____

--- ACTION ITEMS ---
| # | Action | PIC | Deadline | Status |
|---|--------|-----|----------|--------|
| 1 |        |     |          |        |

--- RAPAT BERIKUTNYA ---
Tanggal : ____
Agenda  : ____
```
