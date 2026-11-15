# PHASE 2.0 — HOMEPAGE UI/UX READINESS AUDIT

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Laporan Audit Kesiapan Implementasi Homepage (Phase 2A)  
**Tanggal:** 11 November 2026  
**Status Audit:** READ-ONLY AUDIT (Tidak ada perubahan kode sumber atau database pada tahap ini)

---

## 1. RINGKASAN KONDISI HOMEPAGE SAAT INI

Halaman Beranda (`/`) TMCC saat ini dibangun menggunakan arsitektur Inertia.js 2 dengan komponen utama React `Home.jsx` didukung sub-komponen `AboutSnippet.jsx` dan layout `PublicLayout.jsx`. 

Secara umum, Homepage tersusun atas beberapa bagian vertikal:
1. **Hero Slider Area (`Home.jsx:34-118`):** Menampilkan rotasi banner hero (`HeroSlide`) dengan transisi opasitas otomatis (interval 8 detik) serta dukungan teks tetap hero (`fixed_text`).
2. **Running Text / Marquee Bar (`Home.jsx:120-134`):** Pita teks berjalan berlatar `brand-navy` dengan teks emas (`brand-gold`) yang menampilkan kutipan dan fakta singkat Danau Matano dari database `running_texts`.
3. **Tentang Kami Snippet (`AboutSnippet.jsx:1-87`):** Bagian pengenalan dua kolom (logo/gambar di kiri, teks di kanan) dan strip indikator statistik (`homeStats`) di bagian bawah dengan 5 metrik.
4. **Portal Keanggotaan (`Home.jsx:140-224`):** Menampilkan podium 3 besar kontributor anggota aktif (Peringkat 2, 1, dan 3) dengan avatar, poin kontribusi, serta tautan menuju direktori anggota lengkap.
5. **Program Unggulan (`Home.jsx:227-264`):** Menampilkan grid 4 kartu program konservasi yang diambil dari model `Program`.
6. **Footer Global (`Footer.jsx`):** Menampilkan deskripsi organisasi, tautan cepat yang telah disinkronkan pada Phase 1B, informasi kontak, dan tautan sosial media.

---

## 2. DAFTAR FILE TERKAIT REVISI HOMEPAGE

| Komponen Revisi | File Terkait | Tipe File |
| :--- | :--- | :--- |
| **Marquee / Running Text** | `resources/js/Pages/Home.jsx`<br>`tailwind.config.js` | React Component<br>Tailwind Config |
| **Logo dan Branding** | `resources/js/Components/Public/AboutSnippet.jsx`<br>`resources/js/Components/Public/Navigation.jsx`<br>`public/image/logo_tova_matano.png` | React Components<br>Static Image Asset |
| **Homepage Introduction** | `resources/js/Components/Public/AboutSnippet.jsx` | React Component |
| **Water Quality Parameters** | `app/Http/Controllers/WebController.php`<br>`resources/js/Components/Public/AboutSnippet.jsx`<br>`config/water_quality.php`<br>`app/Models/WaterQualityRecord.php` | Controller<br>React Component<br>PHP Config / Model |
| **Program Unggulan** | `resources/js/Pages/Home.jsx`<br>`app/Http/Controllers/WebController.php`<br>`app/Models/Program.php` | React Component<br>Controller / Model |
| **Portal Keanggotaan** | `resources/js/Pages/Home.jsx`<br>`app/Models/Member.php` | React Component<br>Model |

---

## 3. KONDISI IMPLEMENTASI SAAT INI

### 3.1 Marquee / Running Text
- **Implementasi:** Menggunakan satu container `<div className="animate-marquee ...">` berisi 3 tag `<p>` dengan string array teks digabung separator bullet.
- **Konfigurasi CSS:** `animation: 'marquee 120s linear infinite'` dengan keyframes `0% { transform: 'translateX(0%)' }` ke `100% { transform: 'translateX(-100%)' }`.
- **Temuan Masalah (Monitor 100% Zoom):**
  - Pada layar lebar (resolusi desktop 1920x1080 atau 1440p) dengan zoom 100%, konten teks bergerak ke kiri hingga keluar seluruhnya dari viewport sebelum siklus animasi selesai.
  - Karena tidak ada track duplikat kedua yang menyambung secara kontinu (*infinite seamless loop*), layar menyisakan ruang kosong (blank bar) yang berlangsung lama (hingga puluhan detik) sebelum teks berulang dari sisi kanan.
  - Terdapat jeda visual yang tidak diinginkan dan memberi kesan running text "menghilang".
- **Responsivitas:** Pada mobile/tablet, ukuran font mengecil (`text-[11px] sm:text-sm md:text-lg`), namun mekanisme loop tetap mengalami jeda kosong yang sama.

### 3.2 Logo dan Branding
- **Implementasi:** Homepage menggunakan `/image/logo_tova_matano.png` pada kartu kiri `AboutSnippet.jsx:23-26` dan header `Navigation.jsx:40-44`.
- **Kondisi:** Aset gambar fisik saat ini hanya berupa simbol/emblem lingkaran TMCC tanpa tipografi terintegrasi.
- **Konsistensi:** Pada desktop, tulisan "Tova Matano Care Center" disandingkan via elemen teks HTML di sebelah logo (`Navigation.jsx:45-47`), namun pada mobile teks ini disembunyikan (`hidden sm:block`). Pada `AboutSnippet.jsx`, logo ditampilkan di tengah container abu-abu/putih berukuran 4:3.
- **Status Aset:** Klien meminta logo yang menyertakan teks nama lengkap, namun file resmi belum diberikan.

### 3.3 Homepage Introduction
- **Implementasi:** Teks pengenalan di-hardcode dalam `AboutSnippet.jsx:43-52`.
- **Judul Saat Ini:** *"Berdiri untuk pemulihan Danau Matano"* (sudah sesuai permintaan klien).
- **Deskripsi Saat Ini:**
  > *"Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman pencemaran, melalui edukasi, aksi lapangan, dan kolaborasi bersama masyarakat sekitar."*
- **Deskripsi yang Diminta Klien:**
  > *"Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman degradasi lingkungan, melalui edukasi dan aksi lapangan dengan pelibatan masyarakat sekitar dan kolaborasi dengan para pecinta lingkungan dan pemangku kepentingan."*
- **Perbedaan:** Frasa *"ancaman pencemaran"* diganti menjadi *"ancaman degradasi lingkungan"*, serta penambahan klausul *"dengan pelibatan masyarakat sekitar dan kolaborasi dengan para pecinta lingkungan dan pemangku kepentingan."*

### 3.4 Water Quality Parameters
- **Implementasi Saat Ini:**
  - Controller `WebController.php:51-52` hanya menghitung dua parameter rata-rata:
    - `rata_rata_ph` (rata-rata `ph`)
    - `rata_rata_kejernihan` (rata-rata `clarity_cm` dalam meter)
  - Di `AboutSnippet.jsx`, kedua nilai ini digabungkan dalam strip 5 metrik bersama data organisasi:
    1. Relawan Aktif
    2. Program Terlaksana
    3. Sampah Terangkut
    4. Rata-rata pH Air
    5. Rata-rata Kejernihan (m)
- **Kebutuhan Klien:** Menampilkan seluruh parameter pengukuran kualitas air.
- **Data Tersedia di Database:** Tabel `water_quality_records` telah memiliki 6 parameter:
  1. `ph` (pH)
  2. `clarity_cm` (Kejernihan / Secchi Disk, cm)
  3. `tss` (Total Suspended Solids, mg/L)
  4. `tds` (Total Dissolved Solids, mg/L)
  5. `dissolved_oxygen` (Dissolved Oxygen / DO, mg/L)
  6. `temperature` (Suhu Air, °C)

### 3.5 Program Unggulan
- **Implementasi Saat Ini:** Bagian `Highlight Program` berada di baris 227–264 `Home.jsx`, yaitu diletakkan **setelah** bagian `Portal Keanggotaan` (baris 140–224).
- **Kebutuhan Klien:** Urutan ditukar sehingga `Program Unggulan` tampil **di atas** `Portal Keanggotaan`.
- **Data Program:** Diambil via `Program::orderBy('order')->limit(4)->get()`.
- **Penamaan Program:** Sesuai *Decision A*, 4 nama program tidak diubah pada tahap ini karena nama ke-4 (*"Tova Champaign & Event"*) masih menunggu konfirmasi ejaan dari klien.

### 3.6 Portal Keanggotaan
- **Implementasi Saat Ini:** Menampilkan podium 3 besar (Rank 2 perak, Rank 1 emas, Rank 3 perunggu) diikuti tombol "Lihat Semua Anggota".
- **Dampak Perubahan Posisi:**
  - Saat `Program Unggulan` dipindahkan ke atas `Portal Keanggotaan`, ritme latar belakang visual berubah:
    - Tentang Kami (`bg-brand-paper`) -> Program Unggulan (`bg-brand-paper`) -> Portal Keanggotaan (`bg-white`).
  - Penempatan dua seksi berturut-turut dengan `bg-brand-paper` memerlukan penyesuaian visual latar atau pembatas kontras agar transisi antar bagian tetap estetik dan nyaman dibaca.

---

## 4. PERUBAHAN YANG DIPERLUKAN

### 4.1 Marquee / Running Text
1. Mengubah struktur markup running text di `Home.jsx:121-133` menjadi **dua track identik** (`track 1` dan `track 2` yang berdampingan) dengan properti `flex-shrink-0 min-w-full flex items-center`.
2. Menghapus ketergantungan pengulangan manual 3 `<p>` tag dan menggantikannya dengan struktur looping CSS murni: saat track 1 bergeser 0% ke -100%, track 2 yang berada tepat di belakangnya bergerak dari 100% ke 0%, menghasilkan siklus berputar tanpa jeda (*infinite continuous loop*).
3. Memastikan properti `group-hover:[animation-play-state:paused]` tetap berfungsi pada kedua track.
4. Memastikan teks tidak terpotong pada monitor ultra-wide (21:9) maupun laptop 100% zoom.

### 4.2 Logo dan Branding
1. Mempertahankan aset fisik `public/image/logo_tova_matano.png` dan rendering teks logo yang ada.
2. Memberikan rekomendasi teknis kepada klien mengenai spesifikasi format logo resmi baru (format SVG / PNG transparan resolusi tinggi rasio horizontal) agar siap diintegrasikan ketika diserahkan.

### 4.3 Homepage Introduction
1. Memperbarui paragraf deskripsi di `AboutSnippet.jsx:48-52` dengan teks revisi klien:
   > *"Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman degradasi lingkungan, melalui edukasi dan aksi lapangan dengan pelibatan masyarakat sekitar dan kolaborasi dengan para pecinta lingkungan dan pemangku kepentingan."*

### 4.4 Water Quality Parameters
1. **Backend (`WebController.php:home`):**
   Menambahkan agregasi 4 parameter kualitas air yang belum dihitung:
   - `rata_rata_tss`: rata-rata kolom `tss` (format 1 desimal, satuan `mg/L`)
   - `rata_rata_tds`: rata-rata kolom `tds` (format 1 desimal, satuan `mg/L`)
   - `rata_rata_do`: rata-rata kolom `dissolved_oxygen` (format 2 desimal, satuan `mg/L`)
   - `rata_rata_suhu`: rata-rata kolom `temperature` (format 1 desimal, satuan `°C`)
2. **Frontend (`AboutSnippet.jsx`):**
   Mendesain ulang tata letak metrik statistik:
   - Memisahkan **Metrik Gerakan Organisasi** (3 item: Relawan Aktif, Program Terlaksana, Sampah Terangkut) dengan **Parameter Kualitas Air Danau Matano** (6 item: pH, Kejernihan, TSS, TDS, DO, Suhu).
   - Menata parameter kualitas air dalam sub-grid responsif (misal grid 2 kolom di mobile, 3 kolom di tablet, 6 kolom di desktop) lengkap dengan label nama dan satuan resmi, sehingga tidak berjejal dan tetap informatif.

### 4.5 Program Unggulan
1. Memindahkan blok JSX section `Highlight Program` (`Home.jsx:227-264`) ke atas blok JSX section `Portal Keanggotaan` (`Home.jsx:140-224`).
2. Menjaga seluruh tautan `/program/${program.slug}`, gambar ikon program, dan ringkasan deskripsi program tetap utuh tanpa merusak relasi data.

### 4.6 Portal Keanggotaan
1. Menempatkan `Portal Keanggotaan` setelah `Program Unggulan`.
2. Menyesuaikan warna latar belakang atau padding vertikal agar transisi seksi dari `Program Unggulan` (`bg-white` atau `bg-brand-paper`) menuju `Portal Keanggotaan` (`bg-brand-paper` atau `bg-white`) kontras dan harmonis.

---

## 5. RISIKO TEKNIS DAN POTENSI REGRESI

| Risiko / Potensi Regresi | Tingkat Keparahan | Kemungkinan | Strategi Mitigasi |
| :--- | :--- | :--- | :--- |
| **Marquee stuttering atau lompat (jump)** | Sedang | Sedang | Gunakan styling 2-track flex container yang identik dengan lebar `min-w-full shrink-0` dan animasi CSS linear tanpa transisi easing. |
| **Kepadatan UI pada Strip Statistik** | Sedang | Tinggi | Menampilkan 9 metrik (3 organisasi + 6 air) dalam 1 baris akan memicu layout rusak di mobile/tablet. Pisahkan menjadi 2 grup metrik visual yang jelas dan responsif. |
| **Nilai Rata-rata Null pada Parameter Baru** | Rendah | Sedang | Bila database memiliki record lama yang null untuk `tss`/`tds`/`do`/`temperature`, query `avg()` harus menangani null secara aman dengan fallback string `"—"`. |
| **Ritme Latar Belakang Homepage Menjadi Monoton** | Rendah | Tinggi | Karena Program Unggulan dan AboutSnippet saat ini sama-sama menggunakan `bg-brand-paper`, pertukaran posisi dengan Portal Keanggotaan harus memodifikasi salah satu latar (misal Program Unggulan menggunakan `bg-white`) agar ritme latar selang-seling tetap terjaga. |

---

## 6. KETERGANTUNGAN TERHADAP BACKEND, DATABASE, DAN ASET

1. **Backend / Database:**
   - **TIDAK MEMERLUKAN MIGRATION ATAU PERUBAHAN SKEMA DATABASE.**
   - Tabel `water_quality_records` telah memiliki semua kolom yang diperlukan (`tss`, `tds`, `dissolved_oxygen`, `temperature`, `ph`, `clarity_cm`).
   - Query hanya memerlukan pemanggilan read-only agregat (`WaterQualityRecord::avg()`) di controller.
2. **Aset Eksternal:**
   - Tidak ada kebutuhan aset eksternal baru untuk perbaikan marquee, penataan urutan seksi, maupun penambahan parameter kualitas air.
   - Logo nama lengkap tetap menunggu aset dari klien (*Blocked by Client Asset*).

---

## 7. REKOMENDASI URUTAN IMPLEMENTASI (PHASE 2B)

1. **Langkah 1 — Backend Data Extension (`WebController.php`):**
   Perluas array `homeStats` pada method `home()` untuk menghitung rata-rata dari seluruh 6 parameter kualitas air (`ph`, `clarity_cm`, `tss`, `tds`, `dissolved_oxygen`, `temperature`).
2. **Langkah 2 — Marquee / Running Text Infinite Loop (`Home.jsx`):**
   Perbaiki struktur track marquee di `Home.jsx` agar menggunakan sistem 2-track looping seamless tanpa celah kosong pada layar lebar 100% zoom.
3. **Langkah 3 — Homepage Introduction Copy & Water Quality Display (`AboutSnippet.jsx`):**
   - Perbarui paragraf pengenalan TMCC.
   - Tata ulang tampilan statistik agar 3 metrik organisasi dan 6 parameter kualitas air tampil elegan dan responsif.
4. **Langkah 4 — Penataan Urutan Seksi Homepage (`Home.jsx`):**
   - Pindahkan `Program Unggulan` ke atas `Portal Keanggotaan`.
   - Sesuaikan alternasi latar belakang warna (`bg-white` vs `bg-brand-paper`) agar transisi visual tetap dinamis.
5. **Langkah 5 — Verifikasi & Testing:**
   - Jalankan `php artisan test --compact` untuk memastikan controller & endpoint passing.
   - Jalankan `npm run build` untuk memvalidasi kompilasi bundle Vite.
   - Verifikasi tampilan responsif pada desktop (zoom 100%, 125%), tablet, dan mobile.

---

## 8. DAFTAR PERTANYAAN / KONFIRMASI DARI KLIEN

1. **Ejaan Program Ke-4:**
   - Apakah nama program resmi adalah *"Tova Champaign & Event"* atau *"Tova Campaign & Event"*? *(Catatan: Penataan posisi di Phase 2 tidak mengubah nama program, namun konfirmasi diperlukan untuk fase berikutnya).*
2. **Ketersediaan File Logo Nama Lengkap:**
   - Apakah klien memiliki file logo resmi versi horizontal yang memuat nama *"Tova Matano Care Center"* dalam format vector (.svg) atau PNG beresolusi tinggi?
3. **Penyajian Metrik Kualitas Air di Homepage:**
   - Apakah seluruh 6 parameter kualitas air ingin ditampilkan dalam satu kartu metrik ringkasan terintegrasi di section Tentang Kami, atau dibuatkan widget visual khusus dengan keterangan baku mutu/referensi?

---

## 9. DAFTAR FILE YANG DIRENCANAKAN DIUBAH PADA PHASE 2B

Hanya 3 file berikut yang akan dimodifikasi pada saat otorisasi Phase 2B diberikan:

1. `app/Http/Controllers/WebController.php` — Penambahan data agregasi 6 parameter kualitas air untuk props `homeStats`.
2. `resources/js/Pages/Home.jsx` — Perbaikan track infinite continuous marquee dan pemindahan posisi `Program Unggulan` sebelum `Portal Keanggotaan`.
3. `resources/js/Components/Public/AboutSnippet.jsx` — Pembaruan teks pengenalan TMCC dan desain ulang layout metrik statistik 6 parameter air.

*(Tidak ada file lain, migration, konfigurasi, atau aset yang disentuh).*

---

## 10. ESTIMASI TINGKAT KOMPLEKSITAS

| Pekerjaan | File | Kompleksitas | Rationale |
| :--- | :--- | :--- | :--- |
| Perbaikan Marquee 100% Zoom | `Home.jsx` | **Rendah (Low)** | Memperbaiki struktur container flex 2-track seamless loop CSS murni. |
| Pembaruan Deskripsi Pengenalan | `AboutSnippet.jsx` | **Rendah (Low)** | Penyesuaian string statis sesuai dokumen klien. |
| Agregasi 6 Parameter Kualitas Air | `WebController.php` | **Rendah (Low)** | Menambahkan 4 baris `avg()` pada kolom yang sudah ada di tabel database. |
| Redesain Tampilan Parameter Air | `AboutSnippet.jsx` | **Sedang (Medium)** | Mengatur grid kartu metrik agar estetis, tidak padat di mobile, dan jelas satuannya. |
| Pertukaran Posisi Seksi Homepage | `Home.jsx` | **Rendah (Low)** | Reordering blok JSX dan penyesuaian alternasi warna background. |

---

## 11. PERNYATAAN KEPATUHAN KESELAMATAN

> **Konfirmasi Integritas Kode & Lingkungan:**  
> Audit kesiapan Phase 2A ini dilakukan secara penuh tanpa memodifikasi kode sumber aplikasi, skema database, record data, file `.env`, maupun konfigurasi sistem. File laporan ini disimpan di `docs/reports/PHASE_2_0_HOMEPAGE_READINESS_AUDIT.md` sebagai artefak dokumentasi resmi. Implementasi Phase 2B tidak akan dimulai sebelum mendapatkan persetujuan eksplisit dari Project Owner.
