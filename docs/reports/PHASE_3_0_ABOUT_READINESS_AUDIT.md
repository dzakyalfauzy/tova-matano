# PHASE 3.0 — ABOUT TMCC READINESS AUDIT (UPDATED)

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Laporan Audit Kesiapan Implementasi Halaman Tentang Kami (Phase 3A — diperbarui Phase 3A.1)  
**Tanggal:** 11 November 2026  
**Status:** READ-ONLY AUDIT — SIAP DITINJAU  
**Sumber otoritatif klien:** `docs/revisi web.docx` (terverifikasi, berhasil diekstrak)  
**Tipe:** Pemeriksaan statis kode + verifikasi konten klien + verifikasi DB read-only

---

## 0. KOREKSI PENTING TERHADAP AUDIT SEBELUMNYA (Phase 3A → 3A.1)

> **KOREKSI #1 — Dokumen klien TERSEDIA.**
> Audit Phase 3A sebelumnya menyimpulkan dokumen revisi klien *tidak tersedia* di repository. **Kesimpulan itu keliru.** Dokumen tersedia di **`docs/revisi web.docx`** (4.088.593 byte, 196 paragraf, 20 gambar tersemat, 0 tabel, 10 penanda `PAGE`). Ekstraksi berhasil diverifikasi. Seluruh konten halaman *Tentang TMCC* (MASUKAN #7–#10) kini tersedia dan dijadikan acuan.
>
> **KOREKSI #2 — Data pendiri BUKAN placeholder.**
> Audit sebelumnya menyebut pendiri masih data contoh (*seed placeholder*) dengan foto `NULL`. **Kesimpulan itu keliru untuk state DB saat ini.** Verifikasi read-only terhadap database lokal (`founders` table) menunjukkan **2 pendiri riil dengan foto terunggah**:
> - (id 1) **Mukmin Heba** — Direktur Utama TMCC — `TOVA MATANO CARE CENTER` — `founders/01M6QCKTGFZRGWK2NAMYZC20XQ.png`
> - (id 2) **Syarmudin Madri Ganiasi** — Koordinator Dewan Eksekutif Organisasi — `TOVA MATANO CARE CENTER` — `founders/01M6QCPC02B7EM47KK6FDAX0PJ.png`
>
> Ini konsisten dengan narasi klien yang menyebut TMCC *"didirikan oleh dua anak pesisir Danau Matano"*. **Catatan:** verifikasi dilakukan terhadap DB lokal; DB produksi perlu spot-check (diharapkan identik, karena ini data riil).
>
> **KOREKSI #3 — Nomor legal kini DISEDIAKAN klien (menggantikan placeholder).**
> Data legal placeholder di `About.jsx` harus diganti dengan data resmi dari klien:
> - `Surat Keputusan Menteri Hukum Republik Indonesia : Nomor AHU-01482.AH.02.01.TAHUN 2025`
> - `Akta Notaris No. 07 Tanggal 15 April 2026`
> - `NPWP: 1000.0000.0956.7880`
>
> **Catatan verifikasi:** format NPWP 16 digit (`1000.0000.0956.7880`) sesuai format NPWP baru berbasis NIK (16 digit). Direkomendasikan klien mengonfirmasi angka final. Item lama "Tanda Daftar Organisasi Kemasyarakatan" **tidak ada** dalam daftar klien → dihapus.
>
> **KOREKSI #4 — Gambar hero & danau SUDAH ADA di DB.**
> `site_settings` memiliki path gambar terunggah: `about_hero_image_url = settings/01M6BBX7HR70S6395VP3WMXCE6.jpeg`, `about_lake_image_url = settings/01M6BBX7JDTVSP4KK6PBK87WX0.jpeg`. Fallback `/image/danau-matano.jpg` yang tidak ada menjadi **tidak relevan** selama kolom DB terisi.

---

## 1. EXECUTIVE SUMMARY

Halaman **Tentang Kami** (`/tentang`, `resources/js/Pages/About.jsx`) saat ini adalah halaman **React hardcoded** untuk seluruh narasi (latar belakang danau, profil & sejarah, legalitas, visi/misi/tujuan). **Hanya seksi Pendiri yang bersumber dari database.** Ini bertentangan dengan PRD §6.2 (konten profil/legal/visi/misi **admin-editable**).

Dengan tersedianya `docs/revisi web.docx`, kebutuhan klien untuk halaman About **kini jelas dan konkret** dan dapat diimplementasikan.

Temuan kunci (status terbaru):

| # | Temuan | Status |
|---|---|---|
| 1 | Dokumen klien tersedia & terverifikasi (`docs/revisi web.docx`) | **PASS** |
| 2 | Seksi **"Potensi TMCC"** diminta klien namun **belum ada** di halaman | **GAP (perlu dibuat)** |
| 3 | **"Sekilas Danau Matano"** adalah nama baru untuk seksi "Latar Belakang Danau Matano" | **MAPPING + ubah judul** |
| 4 | **"Legalitas"** diminta sebagai blok terpisah (kini sub-blok di Profil & Sejarah) | **RESTRUCTURE** |
| 5 | Data legal placeholder di kode → **diganti data resmi klien** | **PERLU GANTI** |
| 6 | Pendiri: DB berisi **2 pendiri riil + foto** (bukan placeholder) | **PASS (data)** |
| 7 | Instruksi klien: **perkecil foto/logo pendiri & logo profil**, dan pakai logo bertulisan | **PERLU UI/ASSET** |
| 8 | Konten profil/visi/misi/legal masih **hardcoded**, belum admin-editable | **GAP (PRD)** |
| 9 | Hero: tambah label "Tentang Kami", judul "ADVOKASI LINGKUNGAN LOKAL", subtitle baru | **PERUBAHAN** |
| 10 | Visi/Misi/Tujuan ditulis ulang total (Misi 4 poin, Tujuan 4 poin) | **PERUBAHAN** |
| 11 | Teks intro "degradasi lingkungan" (Phase 2) belum diterapkan di halaman About | **INKONSISTENSI** |
| 12 | Atribusi sumber gambar: klien **bertanya** apakah perlu caption kecil kanan-bawah | **PERLU KEPUTUSAN KLIEN** |

**Kesimpulan:** Konten halaman About **sudah tersedia dan siap diimplementasikan.** Blocker utama tinggal **aset logo bertulisan** dan **kejelasan atribusi gambar**, serta keputusan arsitektur admin-editable.

---

## 2. CURRENT ABOUT PAGE STRUCTURE

**File:** `resources/js/Pages/About.jsx` (216 baris)  
**Controller:** `WebController::about()` → props `founders` (`Founder::orderBy('order')->get()`) + `settings` (`SiteSettings::first()`).  
**Route:** `/tentang`.

| # | Seksi (kode) | Baris | Sumber | Gambar |
|---|---|---|---|---|
| 1 | `Page Header` (hero) | 12–28 | `t.about_title`, `t.about_subtitle` | `settings.about_hero_image_url` (ADA) |
| 2 | `Latar Belakang Danau Matano` | 30–59 | Hardcoded 3 paragraf | `settings.about_lake_image_url` (ADA) / fallback file |
| 3 | `Profil & Sejarah` (+ sub-blok Legalitas) | 61–92 | Hardcoded | logo `/image/logo_tova_matano.png` |
| 4 | `Visi, Misi, Tujuan` | 94–148 | Hardcoded | — |
| 5 | `Founders Section` | 150–192 | **DB** (2 pendiri riil) | `/storage/{photo_url}` |
| 6 | `CTA Section` | 194–212 | Hardcoded | — |

**Urutan target klien** (dari MASUKAN #7–#10): Hero → **Sekilas Danau Matano** → **Profil & Sejarah** → **Potensi TMCC** (BARU) → **Legalitas** (blok terpisah) → **Visi/Misi/Tujuan** → **Dewan Pendiri** → CTA.

---

## 3. CLIENT FEEDBACK MAPPING (dari `docs/revisi web.docx`)

| Label brief | Seksi aktual | MASUKAN | Status |
|---|---|---|---|
| **A. About Hero** | `Page Header` | #7 | Perubahan redaksional + struktur (label/judul/subtitle) |
| **B. Sekilas Danau Matano** | `Latar Belakang Danau Matano` | #8 | Ubah judul + teks + catatan gambar |
| **C. Profil & Sejarah TMCC** | `Profil & Sejarah` | #9 (blok 1) | Tulis ulang 3 paragraf + perkecil logo |
| **D. Potensi TMCC** | **(tidak ada)** | #9 (blok 1) | **Seksi baru** |
| **E. Legalitas Organisasi** | sub-blok Legalitas | #9 (blok 1) | Blok terpisah + ganti data resmi |
| **F. Vision, Mission & Objectives** | `Visi, Misi, Tujuan` | #9 (blok 2) | Tulis ulang total |
| **G. Founders Section** | `Dewan Pendiri` | #10 | Tambah narasi + perkecil foto |

> Catatan: dokumen klien memakai label **"MASUKAN#9" dua kali** (Profil/Sejarah dan Visi/Misi/Tujuan) — duplikasi penomoran pada berkas klien, bukan kesalahan ekstraksi.

---

## 4. RELEVANT FILES AND COMPONENTS

**Frontend**
- `resources/js/Pages/About.jsx` — target utama
- `resources/js/Layouts/PublicLayout.jsx` — pendukung (tidak diubah)
- `resources/js/Components/Public/AboutSnippet.jsx` — rujukan konsistensi copy (homepage)

**Backend**
- `app/Http/Controllers/WebController.php` (`about()` 70–76; `settings()` 30–33)
- `app/Models/Founder.php` (fillable: name, role, org_name, photo_url, order)
- `app/Models/SiteSettings.php` (fillable termasuk `about_hero_image_url`, `about_lake_image_url`; cast `fixed_text` array)
- `app/Filament/Pages/ManageSiteSettings.php` (Section "Tampilan Header & Gambar")
- `app/Filament/Resources/Founders/*` (CRUD pendiri)

**Database**
- `founders` (8 kolom) — berisi 2 record riil
- `site_settings` (singleton) — `about_hero_image_url`, `about_lake_image_url` terisi

**Translations**
- `lang/id/site.php`, `lang/en/site.php` — key About (`about_title`, `about_subtitle`, `lake_background`, `profile_history`, `vision`, `mission`, `goal`, `founders`)

**Assets**
- `public/image/logo_tova_matano.png` — ADA
- `public/image/danau-matano.jpg` — TIDAK ADA (tidak relevan; DB image terisi)
- `storage/app/public/settings/*.jpeg` — 4 berkas (termasuk hero & lake via DB)
- `storage/app/public/founders/*.png` — foto 2 pendiri ADA

---

## 5. EXISTING CONTENT vs REQUESTED CONTENT (VERBATIM KLIEN)

> Seluruh teks di bawah dikutip **persis** dari `docs/revisi web.docx`. Tidak ada parafrase.

### A. About Hero (MASUKAN #7)
**Requested (verbatim):**
- `Tentang Kami`
- `ADVOKASI LINGKUNGAN LOKAL`
- `Membangun masa depan yang berkelanjutan untuk Danau Matano`

**Existing:** judul `Tentang Kami`; subtitle `Membangun masa depan yang berkelanjutan untuk Danau Matano, satu tindakan pada satu waktu.`
**Delta:** tambah label "Tentang Kami"; jadikan "ADVOKASI LINGKUNGAN LOKAL" heading utama; subtitle drop ", satu tindakan pada satu waktu".
**Hierarki visual (usulan, perlu konfirmasi):** label kecil = "Tentang Kami" → H1 = "ADVOKASI LINGKUNGAN LOKAL" → subtitle = "Membangun…".

### B. Sekilas Danau Matano (MASUKAN #8)
**Catatan gambar (verbatim):** *"Gambar : sudah kami ganti (tapi bisakah disiapkan di kanan bawah gambar, cukup yg kecil saja tempat keterangan sumber gambar atau tdk perlu ya?"*
**Requested (verbatim):**
- Judul: `Sekilas Danau Matano`
- P1: `Danau Matano merupakan danau tektonik purba terdalam di Indonesia dan Asia Tenggara, serta terdalam ke-8 di dunia. Danau ini terletak Kecamatan Nuha, Kabupaten Luwu Timur, Sulawesi Selatan, dengan kedalaman sekitar 590 meter.`
- P2: `Sebagai "Danau Purba", Matano memiliki keanekaragaman hayati endemik yang luar biasa, termasuk ikan-ikan unik dan biota air tawar yang tidak ditemukan di tempat lain di dunia. Kualitas airnya yang jernih dan ekosistem yang rapuh membuatnya sangat penting untuk dilestarikan.`
- P3: `Ancaman terhadap kelestarian Danau Matano terus meningkat seiring dengan aktivitas manusia, mulai dari pencemaran, limbah domestik, perkebunan, tata kelola wisata yang lemah hingga dampak penambangan termasuk bertambahnya jumlah penduduk dipicu keberadaan sektor industri ini.  Oleh karena itu, langkah nyata untuk menjaga dan memulihkan kondisi danau ini menjadi sangat krusial.`

**Delta vs existing:** judul berubah; P1 menambah "Kecamatan Nuha" dan "sekitar 590 meter" (bukan "maksimal mencapai"); P2 sama; P3 diperluas (perkebunan, tata kelola wisata lemah, penambangan, pertambahan penduduk).

### C. Profil & Sejarah (MASUKAN #9 blok 1)
**Catatan (verbatim):** *"Photo/logo : diperkecil dan gunakan yang ada tulisan tova Matano care center"*
**Requested (verbatim):**
- Judul: `Profil & Sejarah`
- P1: `Tova Matano Care Center (TMCC) lahir dari kepedulian yang mendalam terhadap pentingnya menjaga Danau Matano agar tetap lestari, dengan segala pesona keindahan, keunikan, dan keistimewaannya.`
- P2: `Dalam bahasa Sorowako, "Tova" atau "Towa" Matano berarti Danau Matano. Sementara itu, care center merupakan wadah bagi masyarakat lokal yang hidup secara turun-temurun di pesisir Danau Matano untuk mendiskusikan dan menyebarkan informasi yang jujur mengenai kondisi aktual Danau Matano, sekaligus menjadi motivator Aksi Gerakan Penyelamatan Danau.`
- P3: `TMCC adalah tempat bertemunya keindahan dan fakta kritis. Selain menyajikan keindahan pesona Danau Matano, TMCC juga melakukan inventarisasi permasalahan yang dinilai perlu disampaikan dan disuarakan dalam bentuk data dan informasi yang jujur mengenai perubahan kualitas lingkungan yang sesungguhnya sedang terjadi. Hal ini dilakukan untuk menumbuhkan rasa peduli, serta mendorong masyarakat untuk mendukung, bergabung, dan turut serta berbuat sesuatu dalam upaya memperbaiki dan menjaga kelestarian Danau Matano.`

### D. Potensi TMCC (MASUKAN #9 blok 1 — seksi baru)
**Requested (verbatim):**
- Judul: `Potensi TMCC`
- P1: `Sebagai organisasi yang didirikan oleh masyarakat lokal, pegiat TMCC tumbuh dengan "local wisdom" yang kuat sejak masa lalu. Dengan "indigenous knowledge" yang dimilikinya, pegiat TMCC dipastikan berpengalaman, memahami, sekaligus memiliki keterampilan mengenai perilaku dan interaksi manusia secara turun-temurun dengan Danau Matano, dari masa lalu hingga saat ini. TMCC merupakan narasumber primer dan saksi atas setiap perubahan yang terjadi di Danau Matano.`
- P2: `Kami percaya bahwa Danau Matano bukan sekadar genangan air, melainkan rumah bagi keanekaragaman hayati endemik dan anugerah Allah SWT yang harus dijaga untuk generasi mendatang. Yang tidak kalah penting, sebagai masyarakat yang lahir, hidup, dan besar di pesisir danau ini, Danau Matano merupakan kebanggaan dan bagian dari cerita perjalanan hidup kami. Oleh karena itu, interaksi TMCC dengan Danau Matano lebih dari sekadar menikmati pesonanya, tetapi juga upaya untuk mengukur kehidupan leluhur di masa lalu dan membayangkan bagaimana kami harus mewariskan Danau Matano kepada masa depan.`

> Catatan: pada DOCX, judul "Potensi TMCC" tergabung dengan awal P1 (`Potensi TMCCSebagai organisasi...`). Dipisah menjadi judul + paragraf pada laporan ini; teks tidak diubah.

### E. Legalitas (MASUKAN #9 blok 1)
**Requested (verbatim):**
- Judul: `Legalitas`
- `Surat Keputusan Menteri Hukum Republik Indonesia : Nomor AHU-01482.AH.02.01.TAHUN 2025`
- `Akta Notaris No. 07 Tanggal 15 April 2026`
- `NPWP: 1000.0000.0956.7880`

**Existing (placeholder di kode):** SK Kemenkumham `AHU-0012345.AH.01.07.TAHUN 2020`; NPWP `12.345.678.9-001.000`; Tanda Daftar `123/Kesbangpol/LT/2021`.
**Delta:** ganti seluruhnya dengan 3 baris resmi klien; hapus "Tanda Daftar Kesbangpol".

### F. Visi, Misi & Tujuan (MASUKAN #9 blok 2)
**Requested (verbatim):**
- **Visi:** `Terwujudnya kelestarian Danau Matano sebagai ekosistem yang sehat, berkelanjutan, dan memberi kehidupan bagi generasi kini dan mendatang`
- **Misi:**
  1. `Menggerakkan dan berperan aktif dalam upaya penyelamatan dan pelestarian Danau Matano.`
  2. `Mendorong kebijakan dan advokasi lingkungan yang berkeadilan, inklusif, dan berpihak pada keberlanjutan ekosistem Danau Matano.`
  3. `Meningkatkan kesadaran masyarakat mengenai pentingnya pelestarian Danau Matano.`
  4. `Membangun jejaring kemitraan dan pemberdayaan komunitas untuk memperkuat gerakan penyelamatan lingkungan.`
- **Tujuan:**
  1. `Mendukung program pemerintah dan swasta di bidang lingkungan hidup, khususnya pelestarian Danau Matano.`
  2. `Menyajikan informasi dan data yang akurat mengenai kondisi Danau Matano serta perubahan kualitas lingkungannya.`
  3. `Melakukan upaya penyelamatan dan pelestarian Danau Matano melalui program revitalisasi, konservasi, rehabilitasi, restorasi dan pengelolaan lingkungan yang berkelanjutan.`
  4. `Mendorong terbukanya ruang diskusi dan seminar untuk membahas kelestarian Danau Matano, serta aktif melakukan kampanye lingkungan`

**Delta:** Visi berubah; Misi 3→4 poin; Tujuan 1 paragraf→4 poin.

### G. Dewan Pendiri (MASUKAN #10)
**Catatan (verbatim):** *"Photo : tolong diperkecil"*
**Requested (verbatim):**
- Judul: `DEWAN PENDIRI`
- Narasi: `TMCC didirikan oleh dua anak pesisir Danau Matano yang menyadari pentingnya ruang bagi masyarakat lokal untuk bergerak secara konsisten, terencana, fokus, dan peduli terhadap kondisi Danau Matano. Kelahiran TMCC dilatarbelakangi oleh kejenuhan karena kerusakan Danau Matano hanya menjadi topik yang menarik untuk dibahas dalam berbagai pertemuan, tanpa diikuti aksi nyata. Selain itu, terdapat rasa bosan dan putus asa karena menunggu kehadiran para pemangku kepentingan untuk benar-benar melakukan aksi dan gerakan penyelamatan di Danau Matano. TMCC juga hadir untuk menjawab kegelisahan atas pemberitaan yang hanya berfokus pada eksposur pesona Danau Matano. Sementara itu, para pendiri menyadari bahwa di danau ini, kerusakan lingkungan sebenarnya sedang terjadi secara terus-menerus dan semakin parah dari waktu ke waktu.`

**Existing:** subtitle singkat "Merekalah yang meletakkan batu pertama…"; kartu pendiri `max-w-[280px]`, foto rasio `3/4`.
**Delta:** tambah narasi panjang; perkecil foto; pertahankan 2 record DB (Mukmin Heba, Syarmudin Madri Ganiasi).

---

## 6. IMAGE ASSETS AND SOURCE ATTRIBUTION VERIFICATION

| Aset | Rujukan | Status | Verifikasi |
|---|---|---|---|
| Logo TMCC | `/image/logo_tova_matano.png` | ADA (68.877 byte) | Apakah sudah bertulisan? **PERLU KONFIRMASI** |
| Logo bertulisan "Tova Matano Care Center" | diminta klien (#9) | **BELUM TERVERIFIKASI** | **Aset klien diperlukan** |
| Foto hero About | `settings.about_hero_image_url` = `settings/01M6BBX7HR70S6395VP3WMXCE6.jpeg` | ADA (DB) | Sumber/atribusi **belum diketahui** |
| Foto Danau Matano | `settings.about_lake_image_url` = `settings/01M6BBX7JDTVSP4KK6PBK87WX0.jpeg` | ADA (DB) | Klien: *"sudah kami ganti"* → konfirmasi file ini = gambar baru |
| Fallback `/image/danau-matano.jpg` | `About.jsx:37` | TIDAK ADA | Tidak relevan bila DB terisi; `onError` tidak terpicu |
| Foto pendiri | `storage/app/public/founders/*.png` | ADA (2 file) | Milik pendiri riil; jangan diganti/fabrikasi |
| Kredit/atribusi gambar | diminta (opsional) | **BELUM ADA** | Klien bertanya apakah perlu caption kecil kanan-bawah |

**Temuan atribusi:** Tidak ada metadata/kredit sumber gambar terverifikasi. Klien **menanyakan** apakah perlu caption sumber kecil di kanan-bawah — ini **keputusan klien**, bukan diasumsikan. Tidak ada atribusi yang dikarang.

---

## 7. MISSING INFORMATION AND CLIENT DEPENDENCIES (TERBARU)

**A. Konten — SUDAH TERSEDIA (verbatim di §5).** ✅
**B. Data pendiri — SUDAH ADA di DB (2 riil).** ✅

**C. Masih memerlukan konfirmasi / aset klien:**
1. **Logo versi bertulisan lengkap** "Tova Matano Care Center" (diminta di #2 & #9) — **aset belum tersedia**.
2. **Keputusan atribusi/kredit gambar** — perlu caption kecil kanan-bawah atau tidak (#8).
3. **Konfirmasi foto hero & foto danau** yang terunggah = versi disetujui klien ("sudah kami ganti").
4. **Verifikasi nomor legal** (SK AHU-01482.AH.02.01.TAHUN 2025, Akta Notaris No. 07 15 April 2026, NPWP 1000.0000.0956.7880) — angka final.
5. **Konten bahasa Inggris** — klien hanya memberi versi Indonesia; perlu terjemahan yang disetujui.
6. **Hierarki visual hero** (label vs H1) — konfirmasi penempatan.
7. **Keputusan arsitektur admin-editable** — lihat dokumen `PHASE_3_1_ABOUT_CONTENT_ARCHITECTURE.md`.

---

## 8. TECHNICAL RISKS AND POTENTIAL REGRESSIONS

| # | Risiko | Dampak | Mitigasi (belum dieksekusi) |
|---|---|---|---|
| R1 | Nomor legal placeholder lama masih tampil | Reputasi/legal | Ganti dengan data resmi klien (sudah tersedia) |
| R2 | ~~Nama/foto pendiri placeholder~~ | — | **TIDAK BERLAKU** — DB sudah berisi 2 pendiri riil |
| R3 | Logo tanpa tulisan | Tidak sesuai permintaan klien | Tunggu aset logo bertulisan |
| R4 | Atribusi gambar tidak jelas | Risiko hak cipta | Klien putuskan caption/kredit |
| R5 | Konten hardcoded tak sesuai PRD (admin tak bisa edit) | Ketidaksesuaian PRD | Opsi A/B/C (lihat dok arsitektur) |
| R6 | Inkonsistensi copy homepage vs About ("degradasi lingkungan") | Pesan tak seragam | Selaraskan saat implementasi |
| R7 | Mengubah `WebController::about()` → regresi test (68 test) | Regresi | Jalankan `php artisan test --compact` |
| R8 | Ubah `lang/*` memengaruhi homepage | Regresi label | Aditif; key lama dipertahankan |
| R9 | Klaim faktual (590 m, ke-8 dunia, dst.) | Kredibilitas | Klien sudah menetapkan teks; verifikasi opsional |
| R10 | Foto klien hak pakai belum jelas | Hukum | Klien konfirmasi |

---

## 9. RECOMMENDED IMPLEMENTATION SEQUENCE (Phase 3B — belum dieksekusi)

1. Konfirmasi aset logo bertulisan + keputusan atribusi gambar + verifikasi legal + terjemahan EN.
2. Terapkan konten verbatim §5 ke halaman About (Hero, Sekilas Danau, Profil & Sejarah, **Potensi TMCC baru**, Legalitas terpisah, Visi/Misi/Tujuan, Dewan Pendiri).
3. Perkecil logo profil & foto pendiri; tambah narasi Dewan Pendiri.
4. Selaraskan copy "degradasi lingkungan" (R6).
5. Implementasi admin-editable sesuai keputusan arsitektur (dok `PHASE_3_1_ABOUT_CONTENT_ARCHITECTURE.md`).
6. Verifikasi: `npm run build`, `php artisan test --compact`, tinjauan visual browser.
7. Approval Project Owner sebelum deploy.

---

## 10. EXACT FILES PROPOSED FOR PHASE 3B MODIFICATION

> **BELUM DIUBAH pada Phase 3A/3A.1.**

**Wajib (inti):**
- `resources/js/Pages/About.jsx` — restrukturisasi & konten verbatim klien.
- `lang/id/site.php`, `lang/en/site.php` — label baru (aditif; key lama dipertahankan).

**Bila admin-editable dipilih (Opsi A — JSON di site_settings):**
- `app/Models/SiteSettings.php` — fillable + cast `about_content` (array).
- `app/Filament/Pages/ManageSiteSettings.php` — Section editor konten About.
- `app/Http/Controllers/WebController.php` — `about()` menyuplai konten (fallback ke default).
- `database/migrations/*_add_about_content_to_site_settings_table.php` — **migration baru** (aditif, nullable).

**Bila Opsi B (tabel khusus) dipilih:** tambah model `AboutContent`, migration, Filament resource/page, controller.

**Tidak boleh diubah:** Phase 1 & 2 (`Navigation.jsx`, `Footer.jsx`, `Partnerships.jsx`, `Programs.jsx`, `Home.jsx`, `AboutSnippet.jsx`, `WebController::home()`).

---

## 11. COMPLEXITY ESTIMATES

| Skenario | Deskripsi | Kompleksitas | Estimasi |
|---|---|---|---|
| **3B-Minimal** | Terapkan konten verbatim + perkecil foto (hardcoded) | Rendah–Sedang | ~0.5–1 hari |
| **3B-Standar** | Minimal + struktur seksi baru (Potensi TMCC, Legalitas terpisah) + fallback | Sedang | ~1–2 hari |
| **3B-Full (PRD)** | Admin-editable via JSON `about_content` (migration + Filament + controller + test) | Sedang–Tinggi | ~2–3 hari |

---

## 12. DATABASE / BACKEND DEPENDENCY ASSESSMENT

**Kondisi saat ini (terverifikasi):**
- `founders`: 2 record riil + foto (tidak perlu migration).
- `site_settings`: `about_hero_image_url` & `about_lake_image_url` terisi; **tidak ada** kolom teks About.
- Konten naratif About: **hardcoded di React** (perlu Opsi A/B untuk sesuai PRD §6.2).
- Pola i18n dinamis yang ada: `fixed_text` (JSON `{id,en}` + fallback ke `id`) di `SiteSettings`.

**Kebutuhan backend (tergantung Opsi):**

| Opsi | Perubahan DB | Backend | Catatan |
|---|---|---|---|
| **A — JSON `about_content` di site_settings** | 1 kolom `json nullable` (+opsional `about_lake_image_credit`) | Model fillable/cast + Filament Section + `about()` | **Paling aman/aditif**; sesuai pola `fixed_text` |
| **B — Tabel `about_contents`** | Tabel baru (kolom typed) | Model + Resource/Page + controller | Lebih rapi jangka panjang; lebih banyak kode |
| **C — Tabel generik `page_sections`** | Tabel baru | Generik | Untuk banyak halaman lain (Partnership/Program/Blog/Kontak juga minta redaksional) |

**Rekomendasi:** **Opsi A** untuk Phase 3B (risiko paling rendah, aditif, cocok pola `fixed_text`), dengan **Opsi B/C** sebagai evolusi mendatang bila banyak halaman perlu editable. Detail di `docs/reports/PHASE_3_1_ABOUT_CONTENT_ARCHITECTURE.md`.

**Backward compatibility:** migration Opsi A hanya menambah kolom nullable; bila kosong, `About.jsx` jatuh ke konten default (verbatim) yang ditanam di frontend. Tidak ada data lama yang diubah/dihapus.

---

## 13. KESIMPULAN

**Status Phase 3A.1: SELESAI (read-only).**

| Area | Status |
|---|---|
| Dokumen klien | **PASS** — tersedia & terverifikasi |
| About Hero | **Siap** (teks tersedia; hierarki perlu konfirmasi) |
| Sekilas Danau Matano | **Siap** (teks tersedia; atribusi perlu keputusan) |
| Profil & Sejarah | **Siap** (teks tersedia) |
| Potensi TMCC | **Siap dibuat** (teks tersedia) |
| Legalitas | **Siap** (data resmi tersedia; verifikasi angka) |
| Visi/Misi/Tujuan | **Siap** (teks tersedia) |
| Founders | **PASS (data)** — 2 pendiri riil; perkecil foto |
| Aset logo tulisan | **BLOCKED** — perlu aset klien |
| Admin-editable (PRD) | **Perlu keputusan arsitektur** |

**Tidak ada perbaikan yang dilakukan. Tidak ada file aplikasi, DB, `.env`, migration, seeder, atau Git yang diubah. Awaiting approval sebelum Phase 3B.**
