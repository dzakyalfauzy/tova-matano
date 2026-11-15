# PHASE 3.1 — ABOUT CONTENT ARCHITECTURE & PHASE 3B IMPLEMENTATION PLAN

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Desain Arsitektur Konten About & Rencana Implementasi Phase 3B  
**Tanggal:** 11 November 2026  
**Status:** DESIGN ONLY — belum ada implementasi  
**Sumber konten:** `docs/revisi web.docx` (terverifikasi) + laporan `PHASE_3_1_CLIENT_CONTENT_EXTRACTION.md`

---

## 0. RINGKASAN KEPUTUSAN

- Konten halaman *Tentang Kami* **sudah tersedia verbatim** dari klien (MASUKAN #7–#10).
- Data pendiri & gambar hero/danau **sudah ada di database** (bukan placeholder).
- PRD §6.2 mewajibkan konten profil/sejarah/legalitas/visi-misi **admin-editable**.
- **Rekomendasi arsitektur: Opsi A** — satu kolom **`about_content` (JSON, nullable)** di tabel `site_settings`, mengikuti pola `fixed_text` `{id, en}` yang sudah ada, ditambah satu kolom **`about_lake_image_credit` (string, nullable)**. Ini **paling aman/aditif**, nol risiko terhadap data produksi, dan konsisten dengan UI admin yang ada.
- **Keselamatan legal (BARU):** tambah kolom **`about_legal_verified` (boolean, NOT NULL, DEFAULT FALSE)**. Data legal hanya boleh tampil publik bila flag ini `true` **dan** item legal terisi. Data legal tidak pernah ditaruh di default/fallback frontend.
- **Fallback berlapis**: bila `about_content` kosong, halaman About memakai konten default (verbatim klien) yang ditanam di frontend → halaman tidak pernah kosong, dan admin dapat mengisi/mengubah kapan pun.

---

## 1. EVALUASI ARSITEKTUR (Opsi A vs B vs C)

### Opsi A — Kolom JSON `about_content` di `site_settings` **(DIREKOMENDASIKAN)**
**Konsep:** 1 kolom `about_content` (JSON) menyimpan seluruh teks About terstruktur, bilingual `{id, en}`; 1 kolom `about_lake_image_credit` untuk atribusi gambar danau. Gambar hero/danau tetap memakai kolom existing `about_hero_image_url` / `about_lake_image_url`.

| Aspek | Penilaian |
|---|---|
| Perubahan DB | 2 kolom **nullable** (aditif) — tidak menyentuh data lama |
| Model | Tambah 2 fillable + cast `about_content => array` + helper lokal |
| Admin | Reuse `ManageSiteSettings` (sudah ada) + Section baru |
| Risiko produksi | **Sangat rendah** (aditif, default NULL, fallback di frontend) |
| Konsistensi | Mengikuti pola `fixed_text` (`{id,en}` + fallback `id`) |
| Kekurangan | Skema JSON tidak ditegakkan DB; satu "blob" besar |

### Opsi B — Tabel khusus `about_contents`
**Konsep:** tabel singleton dengan kolom typed per seksi.
| Aspek | Penilaian |
|---|---|
| Perubahan DB | Tabel baru (idempotent, aditif) |
| Model/Admin | Model + Page/Resource baru |
| Risiko produksi | Rendah, tetapi lebih banyak permukaan kode baru |
| Kekurangan | Duplikasi pola singleton; lebih banyak kode untuk benefit minim saat ini |

### Opsi C — Tabel generik `page_sections`
**Konsep:** baris `(page, section_key, title, body, image, order)` untuk semua halaman.
| Aspek | Penilaian |
|---|---|
| Cocok jangka panjang | Ya (klien juga minta revisi redaksional Partnership/Program/Blog/Kontak) |
| Untuk Phase 3B | **Berlebihan** (scope meluas) |

### Keputusan
**Opsi A untuk Phase 3B.** Alasan: risiko paling rendah, aditif penuh, memakai ulang halaman admin & pola i18n yang ada, dan tidak mengubah data produksi. Opsi B/C dicatat sebagai evolusi bila halaman lain dijadikan editable.

---

## 2. STRUKTUR DATA `about_content` (JSON)

Semua field teks disimpan sebagai objek `{ "id": "...", "en": "" }`. Paragraf ganda disimpan sebagai **string dengan pemisah baris kosong** (`\n\n`); daftar (misi/tujuan/legal) sebagai **string satu item per baris** (`\n`). Ini menjaga UI admin sederhana (textarea) tanpa perlu Repeater.

```jsonc
{
  "hero": {
    "eyebrow":  { "id": "Tentang Kami", "en": "" },
    "title":    { "id": "ADVOKASI LINGKUNGAN LOKAL", "en": "" },
    "subtitle": { "id": "Membangun masa depan yang berkelanjutan untuk Danau Matano", "en": "" }
  },
  "lake": {
    "title": { "id": "Sekilas Danau Matano", "en": "" },
    "body":  { "id": "<P1>\n\n<P2>\n\n<P3>", "en": "" }
  },
  "profile": {
    "title": { "id": "Profil & Sejarah", "en": "" },
    "body":  { "id": "<P1>\n\n<P2>\n\n<P3>", "en": "" }
  },
  "potential": {
    "title": { "id": "Potensi TMCC", "en": "" },
    "body":  { "id": "<P1>\n\n<P2>", "en": "" }
  },
  "legal": {
    "title": { "id": "Legalitas", "en": "" },
    "items": { "id": "Surat Keputusan ...\nAkta Notaris ...\nNPWP: ...", "en": "" }
  },
  "vision":   { "id": "Terwujudnya kelestarian Danau Matano ...", "en": "" },
  "mission":  { "id": "<misi1>\n<misi2>\n<misi3>\n<misi4>", "en": "" },
  "goal":     { "id": "<tujuan1>\n<tujuan2>\n<tujuan3>\n<tujuan4>", "en": "" },
  "founders": {
    "title":     { "id": "DEWAN PENDIRI", "en": "" },
    "narrative": { "id": "TMCC didirikan oleh dua anak pesisir ...", "en": "" }
  }
}
```

**Kolom tambahan:** `about_lake_image_credit` (string, nullable) — caption kecil sumber gambar danau (tampil hanya bila diisi; sesuai pertanyaan klien #8).

> Gambar **tidak** disimpan di JSON agar konsisten dengan kolom existing (`about_hero_image_url`, `about_lake_image_url`). `about_lake_image_credit` dikelompokkan dekat gambar danau.

> **Default publik `legal.items` = KOSONG.** Data legal tidak boleh menjadi bagian dari fallback frontend. Lihat §2A.

---

## 2A. LEGALITAS — GATING & VERIFIKASI (KESELAMATAN PUBLIKASI)

**Prinsip:** Data legal (SK Kemenkumham, Akta Notaris, NPWP, dst.) **tidak boleh tampil publik sebelum diverifikasi**. Karena itu, visibilitas Legalitas dikendalikan oleh **dua syarat** (keduanya harus benar):

1. `site_settings.about_legal_verified === true`
2. `about_content.legal.items` tidak kosong

### Skema kolom
| Kolom | Tipe | Null? | Default | Keterangan |
|---|---|---|---|---|
| `about_legal_verified` | `TINYINT(1)` / boolean | **NOT NULL** | **FALSE (0)** | Hanya admin berwenang yang boleh mengubah ke `true` |

### Perilaku
- **Admin** dapat **menyiapkan** item legal di Filament (`about_content.legal.items`) kapan saja, tetapi **tidak tampil** sampai flag `about_legal_verified` diaktifkan.
- **Toggle verifikasi** hanya ada di panel admin (`ManageSiteSettings`), tidak pernah di frontend publik.
- **Default migration:** `about_legal_verified = 0` → Legalitas tersembunyi pada semua instalasi/fresh deploy.
- **Tidak ada fallback legal:** hapus seluruh nomor legal placeholder/hardcoded dari `About.jsx`. Bila flag `false` atau item kosong → **seksi Legalitas tidak dirender sama sekali** (bukan dirender kosong, bukan menampilkan placeholder).

### Pengamanan data (penting)
Karena data legal tidak boleh "muncul" sekalipun tidak dirender, **controller `WebController::about()` harus MENGHILANGKAN (empty) item legal dari props bila `about_legal_verified !== true`**. Dengan begitu, informasi legal yang belum diverifikasi **tidak pernah dikirim ke browser**.

```
if (! $settings?->about_legal_verified) {
    $aboutContent['legal']['items'] = ['id' => '', 'en' => ''];
}
```

### Matriks tampilan
| `about_legal_verified` | `legal.items` terisi | Tampil? |
|---|---|---|
| `false` | ya | **Tidak** (data juga tidak dikirim ke frontend) |
| `false` | tidak | **Tidak** |
| `true` | tidak | **Tidak** (hindari seksi kosong) |
| `true` | ya | **Ya** |

---

## 3. STRATEGI LOKALISASI (id/en)

Mengikuti pola `SiteSettings::getFixedText()`:
- Setiap field `{id, en}` diselesaikan berdasarkan `app()->getLocale()`.
- Bila nilai `en` kosong → **fallback ke `id`**.
- Bila `id` juga kosong → fallback ke **konten default frontend** (verbatim).
- Klien baru menyuplai **Bahasa Indonesia**; `en` dibiarkan kosong hingga terjemahan disetujui (tidak diterjemahkan otomatis).

Helper usulan di `SiteSettings`:
```php
public function getAboutContent(?string $locale = null): array
{
    // resolve {id,en} → string, fallback en→id, keep structure
}
```

---

## 4. DESAIN ADMIN (FILAMENT)

**Lokasi:** perluas `app/Filament/Pages/ManageSiteSettings.php` (halaman tunggal pengaturan situs).

**Section baru:** `Konten Halaman Tentang Kami` (di bawah Section gambar), berisi sub-Section / Tabs:

| Sub-seksi | Field (id & en) | Tipe |
|---|---|---|
| Hero | eyebrow, title, subtitle | TextInput ×2 bahasa |
| Sekilas Danau Matano | title; body (paragraf pisah baris kosong) | TextInput; Textarea |
| Atribusi Gambar Danau | `about_lake_image_credit` (di Section gambar) | TextInput |
| Profil & Sejarah | title; body | TextInput; Textarea |
| Potensi TMCC | title; body | TextInput; Textarea |
| Legalitas | title; items (satu per baris) | TextInput; Textarea |
| Visi / Misi / Tujuan | vision; mission (baris); goal (baris) | Textarea |
| Dewan Pendiri | title; narrative | TextInput; Textarea |

**Binding state:** `about_content.hero.eyebrow.id`, dst. (Filament mendukung state path bertitik ke kolom JSON cast — sama seperti `fixed_text.id`).
**Normalisasi `mount()`:** pastikan `about_content` berupa array dengan struktur default (analog penanganan `fixed_text`), agar form tidak error saat kolom NULL.
**Gambar:** `FileUpload` existing untuk hero/lake dipertahankan; tambah `TextInput('about_lake_image_credit')`.
**Bahasa:** gunakan pasangan field `...id` + `...en` berdampingan (dua kolom). Bila tersedia `Tabs`, kelompokkan per bahasa; jika tidak, cukup dua kolom.

**Catatan versi:** Verifikasi ketersediaan komponen (`Tabs`, `Repeater`, `RichEditor`) untuk Filament terpasang (5.8) via `search-docs` sebelum coding. Desain ini hanya memakai `TextInput`, `Textarea`, `FileUpload`, `Section` (sudah terbukti dipakai di `ManageSiteSettings`).

---

## 5. STRATEGI RENDER FRONTEND (`About.jsx`)

1. **Props baru:** `aboutContent` (hasil resolusi lokal) + `settings.about_lake_image_credit`.
2. **Default verbatim** ditanam sebagai konstanta di `About.jsx` (persis §5 audit) — dipakai bila `aboutContent` kosong/null.
3. **Helper render:**
   - `splitParagraphs(text)` → `text.split(/\n\s*\n/)` → `<p>`.
   - `splitLines(text)` → `text.split('\n').filter(Boolean)` → `<li>`.
4. **Urutan seksi target:** Hero → Sekilas Danau Matano → Profil & Sejarah → **Potensi TMCC** → **Legalitas** → Visi/Misi/Tujuan → Dewan Pendiri → CTA.
5. **Hero:** `eyebrow` (label kecil) + `h1` + `subtitle`.
6. **Atribusi gambar:** bila `about_lake_image_credit` terisi → caption kecil `absolute bottom-2 right-2 text-[10px] text-white/70`. Jika kosong → tidak ditampilkan.
7. **Foto/logo:** perkecil logo profil (mis. `max-w-[220px]`) & kartu pendiri (mis. `max-w-[200px]`, rasio tetap); sesuai instruksi "diperkecil".
8. **Fallback gambar:** `about_lake_image_url` → `/image/danau-matano.jpg` → logo (chain existing dipertahankan).

---

## 6. MISSING INFORMATION REGISTER (untuk konfirmasi klien)

| # | Item | Status | Blocking? |
|---|---|---|---|
| M1 | Logo versi bertulisan lengkap "Tova Matano Care Center" | **Belum ada aset** | Ya (kosmetik penting) |
| M2 | Keputusan caption/atribusi sumber gambar danau | Klien bertanya; belum diputuskan | Tidak (opsional) |
| M3 | Konfirmasi foto hero & danau terunggah = versi disetujui ("sudah kami ganti") | Perlu konfirmasi | Tidak |
| M4 | Verifikasi angka legal (SK AHU-01482.AH.02.01.TAHUN 2025; Akta No. 07 15 Apr 2026; NPWP 1000.0000.0956.7880) | Perlu verifikasi final | **Ya untuk publikasi (dikendalikan `about_legal_verified`)** |
| M5 | Konten Bahasa Inggris | Belum disuplai | Tidak (fallback id) |
| M6 | Hierarki visual hero (label vs H1) | Perlu konfirmasi | Tidak (usulan jelas) |
| M7 | Nama/jabatan takarir pendiri saat foto diperkecil | Data DB sudah ada | Tidak |

> Tidak ada konten/aset yang dikarang. Semua yang kosong ditandai eksplisit.

---

## 7. RENCANA IMPLEMENTASI PHASE 3B (DETAIL)

### 7.1 File yang Diubah/Ditambah
| Aksi | File |
|---|---|
| **Tambah** | `database/migrations/xxxx_xx_xx_add_about_content_to_site_settings_table.php` |
| **Ubah** | `app/Models/SiteSettings.php` (fillable + cast + `getAboutContent()`) |
| **Ubah** | `app/Filament/Pages/ManageSiteSettings.php` (Section Konten About + `about_lake_image_credit`) |
| **Ubah** | `app/Http/Controllers/WebController.php` (`about()` kirim `aboutContent`) |
| **Ubah** | `resources/js/Pages/About.jsx` (restrukturisasi + default verbatim + seksi baru) |
| **Ubah** | `lang/id/site.php`, `lang/en/site.php` (label baru, **aditif**) |
| **Tambah** | `tests/Feature/AboutPageTest.php` (Pest) |
| **Tidak diubah** | Phase 1 & 2 files; `AboutSnippet.jsx`; `Home.jsx`; `WebController::home()` |

### 7.2 Perubahan Database (Aditif, Aman untuk Produksi)
```php
Schema::table('site_settings', function (Blueprint $table) {
    $table->json('about_content')->nullable()->after('fixed_text');
    $table->string('about_lake_image_credit')->nullable()->after('about_lake_image_url');
    $table->boolean('about_legal_verified')->default(false)->after('about_content'); // NOT NULL DEFAULT FALSE
});
```
- **Hanya menambah kolom nullable/default** — tidak mengubah/menghapus kolom/data lama.
- `about_legal_verified` bersifat **NOT NULL DEFAULT FALSE** (legal tersembunyi secara default).
- **Tidak ada data migration wajib** (frontend default menutup tampilan; admin mengisi belakangan).
- `down()`: drop 3 kolom (hanya untuk rollback dev; **jangan** dijalankan di produksi sebelum backup).

### 7.3 Migration & Backward Compatibility
- Migration bersifat **forward-only-safe**: kolom baru NULL pada baris lama.
- Kode membaca `about_content` dengan `?? []` + fallback default → aplikasi tetap berjalan walau migration belum diisi.
- **Tidak menyentuh** `founders` (data riil) maupun gambar existing.
- Strategi rollback: restore backup DB; `down()` tersedia.

### 7.4 Filament UI
- Section baru dengan field Additive; `mount()` normalisasi `about_content` default.
- Save existing (`$settings->update($this->form->getState())`) tetap dipakai.

### 7.5 Frontend Rendering
- Default verbatim di `About.jsx`; merge dengan `aboutContent` (per-field `value ?? default`).
- Helper `splitParagraphs`/`splitLines`.
- Seksi baru `Potensi TMCC`; `Legalitas` dipisah; Visi/Misi/Tujuan baru.

### 7.6 Validasi & Perilaku Fallback
- `aboutContent` null/kosong → default frontend.
- `en` kosong → `id`.
- Field kosong → default (kecuali **legal** — tidak ada default).
- Credit kosong → caption disembunyikan.
- Gambar: DB → file fallback → logo.
- **Legal:** dirender hanya bila `about_legal_verified === true` DAN `legal.items` non-kosong; jika tidak, seksi tidak dirender dan item legal dikosongkan dari props.

### 7.7 Strategi Testing (Pest)
1. `GET /tentang` → 200, komponen `About`, props `founders`, `aboutContent`, `settings`.
2. `about_content` NULL → halaman tetap 200 & memakai default (tidak crash).
3. Unit `SiteSettings::getAboutContent()`: locale `en` kosong → fallback `id`.
4. Founder records terkirim dari DB.
5. Jalankan `php artisan test --compact` (68 test existing harus tetap hijau).

### 7.8 Pertimbangan Deployment
- Urutan: **backup DB** → `php artisan migrate` → `npm run build` → clear cache (`config`/`route`/`view`) → smoke test `/tentang`.
- **Tanpa seeder** di produksi. Konten diisi admin via Filament, atau tetap default sampai diedit.
- Tidak ada perubahan `.env`.
- Migrasi aditif → minim downtime.

---

## 8. TAHAPAN IMPLEMENTASI (PROPOSED, BELUM DIEKSEKUSI)

| Tahap | Pekerjaan | Verifikasi |
|---|---|---|
| **3B-1** | Migration 2 kolom nullable | `php artisan migrate` (dev) |
| **3B-2** | `SiteSettings`: fillable, cast, `getAboutContent()` | unit test fallback i18n |
| **3B-3** | `ManageSiteSettings`: Section About + credit field + mount normalization | manual admin check |
| **3B-4** | `WebController::about()` kirim `aboutContent` | feature test props |
| **3B-5** | `About.jsx`: restrukturisasi + verbatim default + Potensi TMCC + Legalitas terpisah + perkecil foto + credit | `npm run build` |
| **3B-6** | `lang/*`: label aditif | cek tidak merusak homepage |
| **3B-7** | Tests (AboutPageTest) | `php artisan test --compact` |
| **3B-8** | Build + tinjauan visual browser (mobile/tablet/desktop) | sign-off visual |

---

## 9. RISIKO & BACKWARD COMPATIBILITY

| Risiko | Mitigasi |
|---|---|
| Migration gagal di produksi | Aditif nullable; backup sebelum migrate; uji di staging |
| `about_content` NULL saat deploy | Fallback default frontend |
| `en` kosong | Fallback `id` |
| Regresi test | Jalankan suite; About belum punya test ketat |
| Key terjemahan bersama terpengaruh | Ubah aditif; jangan hapus key |
| Data pendiri/gambar rusak | Tidak menyentuh tabel `founders`/kolom gambar |
| JSON besar sulit dikelola | Struktur flat + textarea; dokumentasi skema |
| Logo bertulisan belum ada | Placeholder sampai aset klien disediakan (jangan dipalsukan) |
| Angka legal salah | Verifikasi klien sebelum publish |

---

## 10. APPROVAL CHECKLIST SEBELUM CODING

- [ ] Klien menyetujui **arsitektur Opsi A** (JSON `about_content` + `about_lake_image_credit`).
- [ ] Klien menyetujui **hierarki hero** (label "Tentang Kami" / H1 "ADVOKASI LINGKUNGAN LOKAL" / subtitle).
- [ ] Klien menyuplai/menyetujui **logo bertulisan lengkap** (M1) atau menyetujui tampil tanpa itu sementara.
- [ ] Klien memutuskan **caption sumber gambar** (M2) — perlu/tidak.
- [ ] Klien mengonfirmasi **foto hero & danau** (M3).
- [ ] Klien **memverifikasi angka legal** (M4); data legal diisi di admin dan **`about_legal_verified`** tetap `false` sampai diverifikasi.
- [ ] Klien menyetujui **rencana terjemahan EN** (M5) atau setuju fallback ke ID.
- [ ] Project Owner menyetujui **rencana migrasi aditif** & backup DB sebelum `migrate`.
- [ ] Project Owner menyetujui **scope file** Phase 3B (§7.1) — tanpa menyentuh Phase 1/2.

---

## 11. KESIMPULAN

Desain arsitektur selesai. Konten klien lengkap dan dapat diimplementasikan. Rekomendasi **Opsi A** memberi jalur implementasi paling aman (aditif, fallback berlapis, konsisten pola i18n) sambil memenuhi PRD §6.2 (admin-editable).

**Belum ada implementasi.** Menunggu persetujuan checklist §10 sebelum Phase 3B dimulai.
