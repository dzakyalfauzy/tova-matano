# PHASE 2.1 — HOMEPAGE IMPLEMENTATION REPORT

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Laporan Implementasi Homepage Phase 2B  
**Tanggal:** 11 November 2026  
**Status:** IMPLEMENTASI SELESAI (Phase 2B)

---

## 1. RINGKASAN IMPLEMENTASI

Seluruh pekerjaan Phase 2B telah diimplementasikan berdasarkan laporan `docs/reports/PHASE_2_0_HOMEPAGE_READINESS_AUDIT.md`. Perubahan dibatasi **hanya pada 3 file** yang telah disetujui:

1. `app/Http/Controllers/WebController.php`
2. `resources/js/Pages/Home.jsx`
3. `resources/js/Components/Public/AboutSnippet.jsx`

### Hasil Utama:
- **Marquee / Running Text:** Diubah menjadi mekanisme *seamless infinite loop* berbasis dua track identik berdampingan yang menjamin tidak ada ruang kosong pada zoom 100% maupun resolusi ultrawide.
- **Homepage Introduction:** Teks pengenalan diganti persis sesuai dokumen klien.
- **Water Quality:** Keenam parameter kualitas air (pH, Kejernihan, TSS, TDS, DO, Suhu) kini diagregasi secara transparan dengan penanganan nilai NULL yang aman dan ditampilkan dalam dua kelompok metrik responsif.
- **Section Reordering:** Bagian `Program Unggulan` kini tampil **sebelum** `Portal Keanggotaan` lengkap dengan ritme latar belakang yang harmonis.

---

## 2. PERUBAHAN PER FILE

### 2.1 `app/Http/Controllers/WebController.php`

**Tujuan:** Menambahkan agregasi database `AVG` untuk seluruh 6 parameter kualitas air dan memastikan penanganan NULL yang tepat.

**Perubahan yang dilakukan:**
1. Menambahkan satu query agregat efisien sebelum render Inertia:
   ```php
   $waterAvgs = WaterQualityRecord::selectRaw('
       AVG(ph) as avg_ph,
       AVG(clarity_cm) as avg_clarity,
       AVG(tss) as avg_tss,
       AVG(tds) as avg_tds,
       AVG(dissolved_oxygen) as avg_do,
       AVG(temperature) as avg_temp
   ')->first();
   ```
   Ini menggantikan 4 pemanggilan `avg()` terpisah (menghindari multiple database round-trips) menjadi satu query.

2. Memperluas array `homeStats` dengan 4 field baru:
   ```php
   'rata_rata_tss'  => number_format((float) $waterAvgs->avg_tss, 1),  // 1 desimal
   'rata_rata_tds'  => number_format((float) $waterAvgs->avg_tds, 1),  // 1 desimal
   'rata_rata_do'   => number_format((float) $waterAvgs->avg_do, 2),   // 2 desimal
   'rata_rata_suhu' => number_format((float) $waterAvgs->avg_temp, 1), // 1 desimal
   ```

3. **Penanganan NULL yang lebih aman:** Pengecekan eksplisit `!== null` mencegah nilai valid `0` atau `0.0` (misalnya pH atau suhu 0) salah dianggap kosong lalu diganti `" — "`.

**Backward Compatibility:** Props lama (`rata_rata_ph`, `rata_rata_kejernihan`, `relawan_aktif`, `program_terlaksana`, `sampah_terangkut`) tetap dikirim dengan key dan format yang identik.

---

### 2.2 `resources/js/Components/Public/AboutSnippet.jsx`

**Tujuan:** Memperbarui teks pengenalan dan mendesain ulang tampilan statistik menjadi dua kelompok metrik.

**Perubahan yang dilakukan:**
1. **Import baru:** Menambahkan `Droplets` dari `lucide-react` sebagai ikon pendukung subjudul kualitas air.

2. **Struktur data terpisah:**
   - `orgMetrics` — 3 metrik organisasi: Relawan Aktif, Program Terlaksana, Sampah Terangkut (masing-masing dengan label dan sublabel).
   - `waterParameters` — 6 parameter kualitas air: pH, Kejernihan, TSS, TDS, DO, Suhu (masing-masing dengan label, nilai, satuan, dan deskripsi singkat).

3. **Teks pengenalan persis sesuai klien:**
   > *"Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman degradasi lingkungan, melalui edukasi dan aksi lapangan dengan pelibatan masyarakat sekitar dan kolaborasi dengan para pecinta lingkungan dan pemangku kepentingan."*

4. **Tata letak statistik baru:**
   - **Kelompok 1 (Metrik Organisasi):** Grid responsif `grid-cols-1 sm:grid-cols-3` dengan divider vertikal di desktop dan pemisah border bawah.
   - **Kelompok 2 (Parameter Kualitas Air):** Subjudul lengkap dengan tautan "Lihat Data Lengkap" menuju `/kualitas-air`, serta grid kartu:
     - Mobile: `grid-cols-2`
     - Tablet: `md:grid-cols-3`
     - Desktop: `lg:grid-cols-6`
   - Setiap kartu parameter menampilkan label (brand-gold), satuan, nilai besar, dan deskripsi dengan border tipis `border-white/10` berlatarkan `bg-white/5` untuk konsistensi identitas visual TMCC.

5. **Fallback NULL:** Semua nilai menggunakan operator `?? '—'` (em-dash), sehingga data yang belum tersedia tampil sebagai `—`.

---

### 2.3 `resources/js/Pages/Home.jsx`

**Tujuan:** Memperbaiki marquee dan menata ulang urutan seksi Homepage.

**Perubahan yang dilakukan:**

1. **Import:** Menambahkan `useMemo` dari React.

2. **Marquee Seamless Infinite Loop (PERUBAHAN INTI):**
   - Menghapus satu container tunggal dengan 3 `<p>` redundan.
   - Menambahkan `marqueeItems` hasil `useMemo` yang memastikan minimal **16 item** dalam satu track. Tujuan: memastikan konten track selalu lebih lebar dari viewport, bahkan pada ultrawide 21:9, sehingga tidak ada ruang kosong di ujung track.
   - Menambahkan fungsi `renderMarqueeTrack(ariaHidden)` yang menghasilkan satu track:
     ```jsx
     <div
         aria-hidden={ariaHidden}
         className="flex shrink-0 min-w-full items-center gap-8 md:gap-12 pr-8 md:pr-12 animate-marquee group-hover:[animation-play-state:paused]"
     >
         {/* item items */}
     </div>
     ```
   - Container marquee merender **dua track identik** secara berdampingan:
     ```jsx
     <div className="... overflow-hidden flex flex-nowrap whitespace-nowrap group select-none relative">
         {renderMarqueeTrack(false)}
         {renderMarqueeTrack(true)}
     </div>
     ```

3. **Section Reordering:**
   - Urutan JSX diubah dari: Tentang Kami → Portal Keanggotaan → Program Unggulan
   - Menjadi: Tentang Kami → **Program Unggulan** → **Portal Keanggotaan**
   - Data program, slug, URL, gambar, dan deskripsi tidak diubah. Nama keempat program (termasuk *"Tova Champaign & Event"*) dipertahankan persis seperti kondisi saat ini karena masih menunggu konfirmasi klien.

4. **Ritme Latar Belakang:**
   - `Program Unggulan` kini menggunakan `bg-white` (dengan border tipis) agar kartu program putih tetap kontras dengan latar dan persistence visual berjalan.
   - `Portal Keanggotaan` menggunakan `bg-brand-paper` (sebelumnya `bg-white`) sehingga tercipta ritme selang-seling: *paper* (Tentang Kami) → *white* (Program Unggulan) → *paper* (Portal Keanggotaan).
   - Padding vertikal ditingkatkan (`py-16 md:py-24`) dan border konsisten untuk menjaga visual.

5. **Peningkatan visual kecil yang tetap berada dalam scope file:** Kartu program mendapat `rounded-2xl`, `hover:shadow-lg` dan `hover:border-brand-teal/40`; teks heading/subjudul seksi disesuaikan ke `md:text-4xl`/`text-base md:text-lg` agar konsisten dengan AboutSnippet. Tidak ada perubahan query atau relasi data.

---

## 3. PENJELASAN MEKANISME MARQUEE

### Masalah Sebelumnya
Animasi lama menggunakan satu track yang bergerak `translateX(0%) → translateX(-100%)` selama 120 detik. Setelah track bergeser melewati tepi kiri viewport, area kosong muncul sampai siklus animasi selesai dan track kembali ke posisi awal. Pada monitor lebar/zoom 100%, celah ini terlihat sangat jelas.

### Mekanisme Baru
1. Dua track identik dirender berdampingan secara horizontal dalam container `overflow-hidden` dan `flex-nowrap`.
2. Masing-masing track diberi kelas `animate-marquee` dengan keyframes yang sama: `translateX(0%) → translateX(-100%)`.
3. Karena kedua track identik dan berdampingan, ketika Track 1 bergerak keluar layar ke kiri sebesar lebar penuhnya, **Track 2 tepat menggantikan posisi awal Track 1**.
4. Ketika animasi mengulang (loop), Track 1 berada di posisi `0%` lagi tepat saat Track 2 mencapai `-100%`, sehingga tidak terjadi lompatan visual.
5. `marqueeItems` dijamin minimal 16 item (dari data `running_texts`), memastikan lebar konten setiap track lebih besar dari lebar viewport pada resolusi 1920×1080, 1440p, hingga 21:9 ultrawide. Ini mencegah trailing blank space di ujung track.
6. `group-hover:[animation-play-state:paused]` pada setiap track membuat pause-on-hover berfungsi serempak pada kedua track.
7. Container `overflow-hidden` dan `whitespace-nowrap` memastikan tidak ada horizontal scrollbar pada halaman.

---

## 4. PENJELASAN AGREGASI PARAMETER

| Parameter | Kolom DB | Format Desimal | Satuan | Contoh Tampilan |
| :--- | :--- | :--- | :--- | :--- |
| pH | `ph` | 2 | pH | `7.12` |
| Kejernihan | `clarity_cm` ÷ 100 | 2 | m | `4.35` |
| TSS | `tss` | 1 | mg/L | `3.4` |
| TDS | `tds` | 1 | mg/L | `101.7` |
| DO | `dissolved_oxygen` | 2 | mg/L | `6.28` |
| Suhu | `temperature` | 1 | °C | `27.9` |

- Semua menggunakan agregasi database `AVG` dalam satu query `selectRaw`.
- Formula lama untuk `ph` dan `clarity_cm` dipertahankan (kejernihan dikonversi cm → m).
- Jika kolom berisi seluruhnya NULL atau tabel kosong, MySQL mengembalikan NULL → ditampilkan sebagai `—`.
- Nilai `0` yang sah (misalnya TSS = 0) tidak lagi terkonversi menjadi `—` karena pengecekan `!== null` menggantikan pengecekan truthy/falsy lama.

---

## 5. HASIL TESTING

### 5.1 Test Suite
- **Perintah:** `php artisan test --compact`
- **Hasil:** **PASSED** — 68 tests, 401 assertions, 0 failures.
- **Catatan:** Tidak ada test khusus untuk Homepage (belum ada), namun seluruh test suite tidak terpengaruh.

### 5.2 Production Build
- **Perintah:** `npm run build`
- **Hasil:** **SUCCESS** — `✓ built in 6.96s`. 3450 modules transformed.
- **Bundle baru:** `Home-WDygzQ3q.js` (17.43 kB) — sedikit lebih besar dari sebelumnya (14.28 kB) akibat logika marquee dan reordering.

### 5.3 Warning Build (Pre-existing, bukan regresi baru)
1. `warning: esbuild option was specified by "vite:react-babel" plugin. This option is deprecated, please use oxc instead.` — peringatan dari konfigurasi Vite plugin, **sudah ada sebelum Phase 2B**.
2. `/image/pattern.svg referenced in /image/pattern.svg didn't resolve at build time, it will remain unchanged to be resolved at runtime` — aset pattern SVG memang tidak ada di `public/image/`; ini juga **sudah ada sebelum Phase 2B** (muncul di Phase 1B build) dan tidak termasuk scope Phase 2.

Tidak ada warning atau error baru yang disebabkan oleh implementasi Phase 2B.

---

## 6. HASIL PEMERIKSAAN RESPONSIVITAS (Analisis Statis Berbasis Kode)

| Viewport | Marquee | Parameter Air | Urutan Seksi |
| :--- | :--- | :--- | :--- |
| **Desktop 1920×1080 (100%)** | 2 track × ≥16 item; konten jauh melebihi 1920px; loop seamless tanpa blank space | 6 kolom (`lg:grid-cols-6`) | Program Unggulan di atas Portal Keanggotaan |
| **Desktop/Laptop 125% zoom** | Layout identik, content width tetap melebihi viewport relatif | 6 kolom sampai breakpoint `lg`, di bawahnya 3 kolom | Tetap konsisten |
| **Tablet (~768–1024px)** | Konten track tetap lebih lebar dari viewport; seamless | 3 kolom (`md:grid-cols-3`) | Tetap konsisten |
| **Mobile (<640px)** | Track bergerak; konten jauh lebih panjang dari viewport; seamless | 2 kolom (`grid-cols-2`) | Tetap konsisten |

Catatan: Verifikasi visual browser tetap direkomendasikan pada tahap review Project Owner sebelum deployment production. Analisis di atas berdasarkan breakpoint Tailwind dan struktur markup aktual.

---

## 7. DAFTAR FILE YANG BERUBAH

```text
app/Http/Controllers/WebController.php
resources/js/Components/Public/AboutSnippet.jsx
resources/js/Pages/Home.jsx
```

Total: **3 file** — sesuai batas otorisasi Phase 2B. Perubahan Phase 1 (`Navigation.jsx`, `Footer.jsx`, `Partnerships.jsx`, `Programs.jsx`) **tidak tersentuh dan tetap utuh**.

---

## 8. RISIKO ATAU PEKERJAAN TERSISA

1. **Pekerjaan Tersisa (di luar scope Phase 2B):**
   - Nama program ke-4 (*"Tova Champaign & Event"*) masih menunggu konfirmasi ejaan klien.
   - Aset logo nama lengkap masih *blocked by client asset*.
   - `/image/pattern.svg` yang dirujuk di kartu podium Portal Keanggotaan belum tersedia di `public/image/` — bukan bagian scope Phase 2, namun dapat dipertimbangkan untuk fase aset berikutnya.
2. **Risiko Rendah:** Jumlah item marquee dinamis (minimal 16) bergantung pada panjang teks aktual dari database `running_texts`. Jika admin mengosongkan semua running text, marquee akan disembunyikan (perilaku sama seperti sebelumnya). Jika jumlah running text sangat sedikit namun sangat panjang, track tetap akan sangat lebar—tetap seamless karena kedua track identik.

---

## 9. KONFIRMASI KEPATUHAN

> **Konfirmasi Integritas:**
> - Tidak ada migration dibuat atau dijalankan.
> - Tidak ada seeder dijalankan.
> - Tidak ada record database yang diubah.
> - Tidak ada `.env`, konfigurasi production, atau storage configuration yang disentuh.
> - Tidak ada akses atau modifikasi terhadap lingkungan production.
> - Tidak ada deployment, commit, push, atau destructive Git command.
> - Hanya 3 file aplikasi yang dimodifikasi, sesuai otorisasi Phase 2B.

**Phase 2B selesai.** Menunggu review dan persetujuan Project Owner sebelum melanjutkan ke Phase 3.
