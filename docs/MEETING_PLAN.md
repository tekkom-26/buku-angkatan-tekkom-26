# Rencana Rapat Perdana — Web Buku Angkatan

Dokumen ini adalah **panduan agenda rapat perdana** tim pengembang Web Buku Angkatan. Tujuannya menyamakan visi, mengunci keputusan teknis, dan membagi peran sebelum masuk ke fase eksekusi.

> 📌 **Cara pakai:** jadikan dokumen ini sebagai notulen hidup. Setiap keputusan yang disepakati dicatat di kolom Keputusan; setiap tindak lanjut masuk ke Action Items dengan PIC + deadline.

---

## 1. Tujuan & Output Rapat

**Tujuan:** menyamakan pemahaman tim tentang arsitektur, fitur, tech stack, alur kerja, dan kepemilikan infrastruktur.

**Output wajib (minimal 6):**

1. ✅ **Scope MVP** — daftar fitur wajib vs ditunda.
2. ✅ **Tech stack final** — termasuk keputusan frontend stack.
3. ✅ **Alur kerja** — ticketing, Git/commit, AI usage, komunikasi.
4. ✅ **Akun & infra** — status + PIC tiap layanan.
5. ✅ **Pembagian task** — Epic → PIC (rujuk `IMPLEMENTATION_PLAN.md`).
6. ✅ **Skema database** — disepakati atau dijadwalkan review-nya.

---

## 2. Persiapan Sebelum Rapat (PR semua anggota)

| Siapa | Tugas persiapan |
| :--- | :--- |
| **Semua** | Pelajari minimal 1–2 bukang angkatan sebelumnya. Catat fitur + kesan. |
| **Calon backend** | Baca `docs/ARCHITECTURE.md` & `docs/API_CONTRACTS.md`. |
| **Calon frontend** | Kumpulkan referensi UI/UX buku angkatan / platform sejenis. |
| **Semua** | Siapkan 1 pertanyaan atau kekhawatiran pribadi. |

---

## 3. Alur Rapat (Rekomendasi 90–120 Menit)

```
[FASE 1] Arsitektur & Fitur        (25 mnt)
[FASE 2] Keputusan Tech Stack       (20 mnt)  ← penentu arah
[FASE 3] Alur Kerja Tim             (20 mnt)
[FASE 4] Akun & Infra Angkatan      (15 mnt)
─────────────────────────────────────────────
[JIKA stack sudah fix, lanjut:]
[FASE 5] Review Dokumentasi         (10 mnt)
[FASE 6] Pembagian Task             (15 mnt)
[FASE 7] Skema Database             (15 mnt)
[FASE 8] Kesimpulan & Action Items  (10 mnt)
```

> 💡 Fase 1–4 **wajib**. Fase 5–8 dijalankan setelah tech stack fix. Jika stack belum fix, jadwalkan rapat kedua khusus Fase 5–8.

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

# FASE 2 — Tech Stack (±20 menit)

## 2a. Keputusan: tetap pakai stack sekarang, atau ubah?

Stack saat ini (rujuk `docs/ARCHITECTURE.md`):

| Layer | Teknologi saat ini | Alasan |
| :--- | :--- | :--- |
| Framework | Next.js 16 (App Router) | Full-stack, serverless-ready |
| Bahasa | TypeScript | Type safety |
| Database | Supabase PostgreSQL (Free) | Gratis, hosted |
| ORM | Drizzle ORM | Type-safe, ringan |
| Storage | Cloudflare R2 (Free) | Murah, S3-compatible |
| Email | Resend | OTP admin |
| Auth | Custom JWT (`jose`) | Kontrol penuh, tanpa Supabase Auth |
| Hosting | Netlify | Free tier |
| Validasi | Zod | Runtime validation |
| Package manager | pnpm | Sudah disepakati |

**Pertanyaan diskusi:**

1. Apakah tim nyaman dengan stack ini? Ada yang belum pernah pakai Next.js / Drizzle / Supabase? Siapa mentor?
2. Apakah ada bagian yang **terlalu rumit**?
   - *Custom JWT + OTP* — alternatif: Supabase Auth (lebih simpel, tapi kurang fleksibel).
   - *Cloudflare R2* — alternatif: Supabase Storage (satu platform, limit lebih kecil).
   - *Drizzle* — alternatif: Prisma (lebih populer, lebih berat).
3. **Kriteria ubah stack:**
   - ✅ Ubah hanya jika ada **blocker teknis nyata** atau mayoritas tim tidak bisa pakai.
   - ❌ Jangan ubah hanya karena "kelihatan keren".
4. **Frontend stack belum ditentukan** (placeholder di README) — ini harus diputuskan.

## 2b. Decision Matrix (agar debat tidak berlarut)

| Opsi | Effort belajar | Kesesuaian fitur | Dukungan tim | Keputusan |
| :--- | :---: | :---: | :---: | :--- |
| Tetap stack sekarang | | | | |
| Ganti ORM → Prisma | | | | |
| Ganti Auth → Supabase Auth | | | | |
| _(tambahkan)_ | | | | |

**Aturan resolusi konflik:** jika seri, putuskan oleh **lead backend** (tunjuk di rapat ini).

## 2c. Output Fase 2

- ✅ Stack **final** + alasan.
- ✅ Daftar perubahan dari `docs/ARCHITECTURE.md` (jika ada) → catat untuk diupdate.
- ✅ Frontend stack diputuskan (atau shortlist + deadline).

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
| **Definition of Done** | Kode + docs + lolos review + tidak bocor data sensitif |

## 3b. Commit & Git Flow (konfirmasi `docs/CONTRIBUTING.md`)

| Aturan | Detail |
| :--- | :--- |
| Branch utama | `main` (produksi), `develop` (integrasi) |
| Branch kerja | `feat/`, `fix/`, `docs/`, `chore/` |
| Commit | Conventional Commits (`feat:`, `fix:`, `docs:`, `chore:`, `refactor:`, `test:`) |
| PR | Minimal 1 reviewer, judul ikut format commit, `pnpm build`/lint lolos |
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

Rujuk juga **Vibe Coding Rules** di `docs/CONTRIBUTING.md`: no `any`, privacy-first, audit everything sensitive, plan-before-code.

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
| **Netlify** | ? | ? | Site sudah dibuat? |
| **Domain (opsional)** | ? | ? | Sudah beli? Untuk apa? |

## 4b. Poin Krusial

1. **Kepemilikan akun** — gunakan akun angkatan, bukan akun pribadi.
   - ⚠️ Risiko: memakai email pribadi → akses hilang saat orang keluar.
   - Solusi: buat email angkatan khusus sebagai owner semua akun.
2. **Manajemen kredensial** — minimal orang pegang kredensial sensitif (service role key, JWT secret). Gunakan password manager bersama.
3. **Budget** — semua di free tier; siapkan rencana jika lewat limit (Supabase free 500MB DB, R2 free 10GB storage).
4. **Backup** — data Google Form adalah aset berharga; simpan backup di luar Supabase.
5. **Etika & Privasi** — data berisi kontak darurat; tetapkan siapa boleh akses (admin saja) dan apakah perlu izin.

## 4c. Output Fase 4

- ✅ Tabel akun terisi (status + PIC).
- ✅ Email angkatan khusus dibuat (action item).
- ✅ Password manager dipilih.

---

# 🔀 JIKA TECH STACK SUDAH FIX → Lanjut Fase 5–8

# FASE 5 — Review Dokumentasi Project (±10 menit)

Validasi `docs/` sebagai **Source of Truth**.

| Dokumen | Isi | Perlu update? |
| :--- | :--- | :---: |
| `ARCHITECTURE.md` | ERD, RLS, RBAC, auth flow, env | ? |
| `API_CONTRACTS.md` | Spesifikasi endpoint + JSON contract | ? |
| `SEEDING_GUIDE.md` | Migrasi CSV Google Form | ? |
| `CONTRIBUTING.md` | Git Flow, commit, Vibe Coding | ? |
| `PROJECT_PLAN.md` | Roadmap milestone | ? |
| `IMPLEMENTATION_PLAN.md` | 12 Epic + task checklist | ? |

**Aturan:** perubahan arsitektur/DB → **update docs dulu, baru kode**.

---

# FASE 6 — Pembagian Task (±15 menit)

Map 12 Epic di `docs/IMPLEMENTATION_PLAN.md` ke owner.

| Epic | Deskripsi | PIC | Deadline |
| :--- | :--- | :--- | :--- |
| 1 | Foundation & Infrastructure | ? | ? |
| 2 | Database Schema & Migration | ? | ? |
| 3 | Validation Layer (Zod) | ? | ? |
| 4 | Auth: Angkatan Passcode | ? | ? |
| 5 | Auth: Admin Whitelist + OTP | ? | ? |
| 6 | Audit & Security Helpers | ? | ? |
| 7 | Public API (Landing) | ? | ? |
| 8 | Protected API (Directory) | ? | ? |
| 9 | Admin API (CRUD, dll) | ? | ? |
| 10 | Storage: Cloudflare R2 | ? | ? |
| 11 | CSV Ingestion & Seeder | ? | ? |
| 12 | Deployment & UI Readiness | ? | ? |

**Pertanyaan pemandu:**
- Berapa orang tersedia? Bagaimana beban dibagi seimbang?
- Ada dependency antar-Epic? (mis. Epic 4 butuh Epic 2 selesai)
- Urutan mulai: biasanya DB schema → auth → API.

---

# FASE 7 — Skema Database (±15 menit)

**Status saat ini:** `db/index.ts` (konektor Drizzle) sudah ada; `db/schema.ts` **belum** (sedang dikerjakan terpisah).

**Yang dibahas:**
- Review rancangan 8 tabel (`ARCHITECTURE.md` Section 2):
  `students`, `student_contacts`, `student_profiles`, `student_emergencies`, `app_config`, `admin_whitelist`, `admin_otp`, `audit_logs`.
- **Pertanyaan:**
  - Field sudah sesuai data Google Form?
  - Enum `class` & `religion` sudah cocok?
  - Field mana yang **sensitif** (admin-only)?
  - Siapa yang review schema sebelum di-push?

**Aturan:** schema = kontrak; perubahan setelah data masuk mahal. Review dulu.

---

# FASE 8 — Kesimpulan & Action Items (±10 menit)

## Ringkasan Keputusan

| Topik | Keputusan |
| :--- | :--- |
| Scope MVP | |
| Tech stack | |
| Frontend stack | |
| Alur kerja | |
| Akun & infra | |
| Skema database | |

## Action Items

| # | Action | PIC | Deadline |
| :--- | :--- | :--- | :--- |
| 1 | Buat email angkatan khusus | ? | ? |
| 2 | Setup Supabase project | ? | ? |
| 3 | Selesaikan `db/schema.ts` | ? | ? |
| 4 | Buat GitHub repo/org | ? | ? |
| 5 | Update docs (jika ada perubahan) | ? | ? |
| 6 | _(tambahkan)_ | ? | ? |

**Penutup:** tetapkan tanggal rapat berikutnya dan lokasi penyimpanan notulen.

---

## 4. Jebakan yang Harus Dihindari

| Jebakan | Solusi |
| :--- | :--- |
| Debat tech stack tanpa ujung | Decision matrix + tunjuk decision maker |
| Semua mau pegang semua | 1 task = 1 PIC |
| Scope melebar (scope creep) | Kunci MVP di awal |
| Pakai akun pribadi untuk infra | Email angkatan khusus |
| Skema DB diubah setelah data masuk | Review schema sebelum push |
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
2. Tech stack       : ____
3. Frontend stack   : ____
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
