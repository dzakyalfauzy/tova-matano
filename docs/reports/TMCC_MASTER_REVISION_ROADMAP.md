# TMCC MASTER REVISION ROADMAP

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Peta Jalan Revisi Klien (Semua Halaman)  
**Tanggal:** 11 November 2026  
**Sumber otoritatif:** `docs/revisi web.docx` (terverifikasi)  
**Status:** ROADMAP — perencanaan saja, belum implementasi  
**Prinsip:** Fase terpisah per halaman; tidak menggabungkan semua revisi dalam satu tugas besar; Phase 1 & 2 yang disetujui dipertahankan.

> Kutipan klien ditulis **persis** (verbatim). Status implementasi diverifikasi dari kode & DB lokal (read-only). Hal yang belum tersedia ditandai eksplisit; **tidak ada kebutuhan yang diciptakan**.

---

## 0. RINGKASAN FASE

| Fase | Cakupan | Status |
|---|---|---|
| **Phase 0** | Audit awal | SELESAI |
| **Phase 1** | Navigasi, footer, subtitle Partnership/Program, copy intro | SELESAI |
| **Phase 2** | Homepage UI/UX (marquee, 6 parameter, urutan, copy) | SELESAI & DISETUJUI |
| **Phase 3B** | **Tentang TMCC** (satu-satunya scope 3B) | Direncanakan (menunggu approval) |
| **Phase 4** | Kualitas Air — gambar titik/stasiun sampel | Direncanakan |
| **Phase 5** | Galeri (caption), Anggota (foto), Admin (filter NAMA) | Direncanakan |
| **Phase 6** | Struktur Organisasi — bagan dinamis | Direncanakan |
| **Phase 7** | Berita — "Berita dan Cerita" + kategori Cerita | Direncanakan |
| **Phase 8** | Aset & penyelarasan lintas halaman (logo, nama program, background, copy Kontak, QA running text) | Direncanakan |

---

## 1. BERANDA

### #1 — Pesan berjalan tidak muncul di monitor 100%
- **Klien (verbatim):** `Tolong di cek, benarkah tampilan pesan berjalannya tdk muncul dalam tampilan monitor 100% ?`
- **Status saat ini:** Marquee sudah dirombak Phase 2 (dua track identik, min. 16 item, seamless, `overflow-hidden`). Perlu **verifikasi visual** pada zoom 100% / layar lebar.
- **File terkait:** `resources/js/Pages/Home.jsx`, `tailwind.config.js` (keyframes marquee).
- **File diusulkan:** — (kemungkinan hanya QA visual; ubah `Home.jsx` bila celah ditemukan).
- **Dependensi:** Tidak ada.
- **Risiko:** Rendah (sudah Phase 2).
- **Konfirmasi klien:** Tidak.
- **Fase:** Phase 8 (QA visual) / sudah tercakup Phase 2.

### #2 — Logo bertulisan + copy
- **Klien (verbatim):** `Logo : Bisakah dipakai yang dibawahnya ada tulisan Tova Matano Care Centrer` ; `Perubahan Redaksional : Berdiri untuk pemulihan Danau Matano` ; `Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman degradasi lingkungan, melalui edukasi dan aksi lapangan dengan pelibatan masyarakat sekitar dan kolaborasi dengan para pecinta lingkungan dan pemangku kepentingan.`
- **Status:** Copy **SELESAI** (Phase 2, `AboutSnippet.jsx`). **Logo bertulisan belum ada** asetnya.
- **File terkait:** `resources/js/Components/Public/AboutSnippet.jsx`, `public/image/logo_tova_matano.png`, `Navigation.jsx`, `Footer.jsx`.
- **File diusulkan:** ganti aset logo (klien) di `public/image/` + rujukan di nav/footer.
- **Dependensi:** **Aset logo bertulisan** (klien).
- **Risiko:** Rendah (kosmetik).
- **Konfirmasi klien:** Ya (aset logo).
- **Fase:** Phase 8.

### #3 — Semua parameter kualitas air
- **Klien (verbatim):** `Tampilkan semua parameter pengukuran uji kwalitas air`
- **Status:** **SELESAI** (Phase 2 — 6 parameter di `AboutSnippet.jsx` + `WebController::home()`).
- **File terkait:** `WebController.php`, `AboutSnippet.jsx`.
- **Dependensi:** Tidak ada.
- **Fase:** Selesai.

### #4 — Urutan Program Unggulan vs Portal Keanggotaan
- **Klien (verbatim):** `CATATAN : Tolong dinaikkan Program Unggulan baru nyusul Portal Keanggotaan`
- **Status:** **SELESAI** (Phase 2 — Program Unggulan di atas Portal).
- **File terkait:** `Home.jsx`.
- **Fase:** Selesai.

### #5 — Nama-nama Program Unggulan
- **Klien (verbatim):** `Program Unggulan sudah saya revisi` → `Tova Water Patrol` / `Tova Edu & Research` / `Tova Care Action` / `Tova Champaign & Event`
- **Status:** **BELUM** — nama program di DB saat ini: `Tova Water Patrol`, `Eco Education`, `Clean Lake Action`, `Biodiversity Watch`. Belum sesuai.
- **File terkait:** tabel `programs` (data), `resources/js/Pages/Programs.jsx`, `ProgramDetail`, `ProgramCategory`, `Home.jsx`.
- **File diusulkan:** data via admin (Filament Program resource) atau migration/seed terkontrol; kemungkinan update slug.
- **Dependensi:** Konfirmasi ejaan **"Champaign" vs "Campaign"** (isu terbuka).
- **Risiko:** Sedang (slug berubah → tautan lama / SEO).
- **Konfirmasi klien:** Ya (ejaan, slug).
- **Fase:** Phase 8 (konten) atau fase konten terpisah.

### #6 — Samakan copy TMCC dengan #2
- **Klien (verbatim):** `Tulisan ini : TMCC / Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman pencemaran, melalui edukasi, aksi lapangan, dan kolaborasi bersama masyarakat sekitar. / Harusnya ikut setelah diedit yang diatas ya? MASUKAN#2 / Kalo tidak, tolong di edit juga dan disamakan yang diatas (MASUKAN#2)`
- **Status:** **SELESAI** (Phase 1 Footer + Phase 2 `AboutSnippet.jsx` menggunakan versi "degradasi lingkungan"). Verifikasi tidak ada sisa copy "pencemaran" di Beranda.
- **Fase:** Selesai (dengan QA kecil).

---

## 2. TENTANG TMCC (#7–#10) — **Phase 3B (satu-satunya scope 3B)**

Konten & desain rinci ada di:
- `docs/reports/PHASE_3_0_ABOUT_READINESS_AUDIT.md`
- `docs/reports/PHASE_3_1_CLIENT_CONTENT_EXTRACTION.md`
- `docs/reports/PHASE_3_1_ABOUT_CONTENT_ARCHITECTURE.md`

Ringkas:
- **#7 Hero:** `Tentang Kami` / `ADVOKASI LINGKUNGAN LOKAL` / `Membangun masa depan yang berkelanjutan untuk Danau Matano`.
- **#8 Sekilas Danau Matano:** teks baru (Nuha, ~590 m, ancaman diperluas) + gambar (sudah diganti) + pertanyaan caption sumber.
- **#9 Profil & Sejarah / Potensi TMCC / Legalitas / Visi-Misi-Tujuan:** konten verbatim; **Potensi TMCC = seksi baru**; **Legalitas blok terpisah** (gated `about_legal_verified`); Visi/Misi(4)/Tujuan(4).
- **#10 Dewan Pendiri:** narasi baru + perkecil foto; 2 pendiri riil (Mukmin Heba, Syarmudin Madri Ganiasi).
- **Status:** konten tersedia; hardcoded kini; akan dijadikan admin-editable (Opsi A). **Belum diimplementasi.**
- **Dependensi:** logo bertulisan, keputusan caption gambar, verifikasi legal, terjemahan EN.
- **Fase:** **Phase 3B**.

---

## 3. STRUKTUR ORGANISASI (#11)

- **Klien (verbatim):** `AKAN DIUBAH DI ADMIN` ; `CATATAN : PSAYA SUDAH MELAKUKAN PERUBAHAN DI STRUKTUR ORGANISASI TAPI TIDAK MERUBAH STRUKTUR DI BAGAN`
- **Status saat ini:** Bagian akordeon kategori dinamis dari DB, tetapi **bagan atas masih statis/hardcoded** (`Organization.jsx:99` "Static Organization Chart"). Perubahan struktur oleh klien tidak tercermin di bagan.
- **File terkait:** `resources/js/Pages/Organization.jsx`, `app/Http/Controllers/WebController.php` (`organization()`), `OrgCategory`, people model, `org_chart_image_url` (settings).
- **File diusulkan:** `Organization.jsx` (bagan dari data DB atau gambar `org_chart_image_url`), kemungkinan controller + data.
- **Dependensi:** Keputusan klien: bagan digambar dinamis dari DB vs unggah gambar bagan resmi.
- **Risiko:** Sedang (struktur bagan kompleks; 6 kategori).
- **Konfirmasi klien:** Ya (sumber bagan & struktur final).
- **Fase:** **Phase 6**.

---

## 4. ANGGOTA DAN RELAWAN (#12)

- **Klien (verbatim):** `HANYA MAU MEMASTIKAN PHOTO ANGGOTA MUNCUL`
- **Status saat ini:** `Members.jsx` merender `member.photo_url` (`Members.jsx:139`) & `volunteer.photo_url` (`:234`). Perlu **verifikasi** bahwa semua anggota berfoto & tampil (bisa jadi masalah data/kosong).
- **File terkait:** `resources/js/Pages/Members.jsx`, `Member` model, `app/Http/Controllers/WebController.php`, Filament member resource.
- **File diusulkan:** `Members.jsx` (fallback/inisial), data anggota.
- **Dependensi:** Data foto anggota (klien/admin).
- **Risiko:** Rendah.
- **Konfirmasi klien:** Tidak (QA data).
- **Fase:** **Phase 5**.

---

## 5. PARTNERSHIP (#13)

- **Klien (verbatim):** `Perubahan Redaksional: Partnership` / `Mitra dan pendukung yang telah bekerjasama dan berkontribusi bersama Tova Matano Care Center.` / `Pertanyaan: memang tdk ada photo background kah`
- **Status saat ini:** Judul & subtitle **SUDAH SESUAI** (`Partnerships.jsx:28-31`, Phase 1). **Pertanyaan background** belum dijawab (belum ada foto background seksi header).
- **File terkait:** `resources/js/Pages/Partnerships.jsx`.
- **File diusulkan:** `Partnerships.jsx` (tambah background bila klien mau), `site_settings` (kolom gambar, bila perlu).
- **Dependensi:** Keputusan & aset background (klien).
- **Risiko:** Rendah.
- **Konfirmasi klien:** Ya (background).
- **Fase:** **Phase 8**.

---

## 6. PROGRAM (#14)

- **Klien (verbatim):** `Perubahan Redaksional: Program Kami` / `Empat pilar aksi nyata TMCC dalam menjaga kelestarian Danau Matano.` / `Pertanyaan: memang tdk ada photo background kah`
- **Status saat ini:** Judul & subtitle **SUDAH SESUAI** (`Programs.jsx:13-15`, Phase 1). **Background** belum dijawab. Nama program (#5) belum sesuai.
- **File terkait:** `resources/js/Pages/Programs.jsx`, `ProgramDetail.jsx`, `ProgramCategory.jsx`, data `programs`.
- **File diusulkan:** `Programs.jsx` (background), data program (#5).
- **Dependensi:** Keputusan background (klien); ejaan program (#5).
- **Risiko:** Rendah–sedang (perubahan slug).
- **Konfirmasi klien:** Ya (background, nama/slug).
- **Fase:** **Phase 8**.

---

## 7. KUALITAS AIR (#15)

- **Klien (verbatim):** `NOTE : Masukkan gambar titik/posisi sampel station`
- **Status saat ini:** `WaterQuality.jsx` lengkap dengan grafik 6 parameter, tetapi **belum ada gambar titik/stasiun sampel**.
- **File terkait:** `resources/js/Pages/WaterQuality.jsx`, `WebController::waterQuality()`, `WaterQualityRecord`, `config/water_quality.php`.
- **File diusulkan:** `WaterQuality.jsx` (+ komponen peta/gambar), model/lokasi stasiun (bila perlu), data GPS.
- **Dependensi:** **Koordinat GPS stasiun sampel** (belum tersedia) atau gambar peta resmi klien.
- **Risiko:** Sedang (butuh data spasial; bisa sebatas gambar statis).
- **Konfirmasi klien:** Ya (koordinat/gambar peta).
- **Fase:** **Phase 4**.

---

## 8. GALERI

- **Klien (verbatim):** `Tolong beri keterangan gambar yang tampil bersamaan gambar`
- **Status saat ini:** Caption hanya muncul **saat hover** (`Gallery.jsx:43-47`). Klien ingin keterangan **tampil bersamaan** gambar (selalu terlihat).
- **File terkait:** `resources/js/Pages/Gallery.jsx`, `GalleryItem` model/controller.
- **File diusulkan:** `Gallery.jsx` (caption selalu tampil), kemungkinan lightbox.
- **Dependensi:** Data caption galeri (sudah ada 5 item di DB).
- **Risiko:** Rendah.
- **Konfirmasi klien:** Tidak.
- **Fase:** **Phase 5**.

---

## 9. BERITA (#16)

- **Klien (verbatim):** `Perubahan Redaksional: Tova Blog` / `Berita dan Cerita` / `Kemudian, bisakah dibawahnya ditambahkan kolom Cerita selain Edukasi, rilis pers, berita,  dan kebijakan.` / `Pertanyaan: memang tdk ada photo background kah`
- **Status saat ini:** Judul `t.blog_title` = "Tova Blog"; subtitle "Edukasi, rilis pers, dan kabar terbaru dari lapangan." (bukan "Berita dan Cerita"). Kategori: `EDUKASI, RILIS_PERS, BERITA, KEBIJAKAN` (`Blog.jsx:5-10`) — **belum ada "Cerita"**.
- **File terkait:** `resources/js/Pages/Blog.jsx`, `BlogDetail.jsx`, `lang/*/site.php`, model `Post`/kategori, Filament blog.
- **File diusulkan:** `Blog.jsx`, `lang/*`, penambahan kategori `CERITA` (mungkin enum DB → cek migrasi/enum + Filament).
- **Dependensi:** Keputusan jenis kategori (enum vs tabel), background.
- **Risiko:** Sedang (perubahan enum/schema kategori bila enum).
- **Konfirmasi klien:** Ya (background; perilaku kategori Cerita).
- **Fase:** **Phase 7**.

---

## 10. KONTAK (#17)

- **Klien (verbatim):** `Perubahan Redaksional: Hubungi Kami` / `Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar` / `Pertanyaan: memang tdk ada photo background kah`
- **Status saat ini:** Judul "Hubungi Kami" sesuai; subtitle saat ini `Ada pertanyaan atau ingin berkolaborasi? Kami siap mendengar.` (`Contact.jsx:33`) — **belum persis** (kurang "saran dan masukan,").
- **File terkait:** `resources/js/Pages/Contact.jsx`.
- **File diusulkan:** `Contact.jsx` (copy), background (bila klien mau).
- **Dependensi:** Keputusan background (klien).
- **Risiko:** Rendah.
- **Konfirmasi klien:** Ya (background).
- **Fase:** **Phase 8**.

---

## 11. ADMIN (Panel)

- **Klien (verbatim):** `Bisakah daftar anggota ini dipasangi filter di kolom NAMA bukan hanya di POINT untuk mempermudah mencari ybs kalaa ada mau melakukan perubahan di poinnya seseorang. Kalo pakai search saja susah mesti berkali-kali kalo banyak yg mau di edit. Tapi kalo sort alphabet lebih mudah mencarinya`
- **Status saat ini:** Daftar anggota Filament — perlu tambah kolom `name` `->sortable()`/searchable (kolom poin sudah sortable). Verifikasi di `MembersTable`.
- **File terkait:** `app/Filament/Resources/Members/Tables/MembersTable.php` (dan resource terkait).
- **File diusulkan:** `MembersTable.php`.
- **Dependensi:** Tidak ada.
- **Risiko:** Rendah.
- **Konfirmasi klien:** Tidak.
- **Fase:** **Phase 5**.

---

## 12. NAVIGASI (ADMIN note)

- **Klien (verbatim):** `Home – About – Program – Water Q – Galeri - News - Org - Member - Partnership – contact`
- **Status saat ini:** Phase 1 telah menyusun ulang navigasi (Program, Kualitas Air, Galeri, Berita, Struktur Organisasi, Anggota, Partnership, Kontak), dengan Beranda & Tentang di awal. **Perlu verifikasi** agar persis sesuai urutan klien.
- **File terkait:** `resources/js/Components/Public/Navigation.jsx`.
- **File diusulkan:** `Navigation.jsx` (bila ada selisih).
- **Dependensi:** Tidak ada.
- **Risiko:** Rendah.
- **Konfirmasi klien:** Tidak.
- **Fase:** Phase 8 (verifikasi).

---

## 13. MATRIKS RINGKAS (Klien — Status — Fase)

| Halaman | MASUKAN | Status | Fase |
|---|---|---|---|
| Beranda | #1 running text | Perlu QA visual | 8 |
| Beranda | #2 logo bertulisan | Copy selesai; **aset logo belum** | 8 |
| Beranda | #3 param air | Selesai | — |
| Beranda | #4 urutan | Selesai | — |
| Beranda | #5 nama program | **Belum** (DB lama) | 8 |
| Beranda | #6 samakan copy | Selesai | — |
| Tentang | #7–#10 | **Belum** (konten siap) | **3B** |
| Struktur Org | #11 | Bagan statis | 6 |
| Anggota | #12 | Perlu verifikasi foto | 5 |
| Partnership | #13 | Subtitle selesai; background? | 8 |
| Program | #14 | Subtitle selesai; background?; nama #5 | 8 |
| Kualitas Air | #15 | **Belum** (butuh GPS/peta) | 4 |
| Galeri | caption | Hover-only → perlu selalu tampil | 5 |
| Berita | #16 | **Belum** (judul/subtitle + kategori Cerita) | 7 |
| Kontak | #17 | Copy perlu diperbarui | 8 |
| Admin | filter NAMA | **Belum** | 5 |
| Navigasi | urutan | Phase 1; verifikasi | 8 |

---

## 14. KEPUTUSAN & DEPENDENSI KLIEN (LINTAS HALAMAN)

1. Aset **logo bertulisan lengkap** (Beranda #2, Tentang #9).
2. Ejaan **"Champaign" vs "Campaign"** + slug program (#5).
3. Keputusan **background photo** untuk Partnership, Program, Berita, Kontak.
4. **Koordinat GPS / gambar peta** stasiun sampel kualitas air (#15).
5. Struktur organisasi final & sumber bagan (DB vs gambar) (#11).
6. Daftar **kategori Berita** final termasuk "Cerita" (#16).
7. Verifikasi **data legal** (Tentang, gated).
8. Terjemahan **EN** untuk konten dinamis (fallback ID sampai tersedia).
