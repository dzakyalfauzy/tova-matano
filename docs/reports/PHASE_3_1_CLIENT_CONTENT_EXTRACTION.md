# PHASE 3.1 — CLIENT CONTENT EXTRACTION REPORT

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Laporan Ekstraksi Konten Revisi Klien  
**Tanggal:** 11 November 2026  
**Sumber otoritatif:** `docs/revisi web.docx`  
**Status:** READ-ONLY — ekstraksi terverifikasi

---

## 1. FILE & METODE EKSTRAKSI

| Item | Nilai |
|---|---|
| Path | `docs/revisi web.docx` |
| Ukuran | 4.088.593 byte |
| Terakhir diubah | 11/11/2026 16:13 |
| Format | Microsoft Word DOCX (OpenXML / ZIP) |
| **Metode** | DOCX dibuka sebagai arsip ZIP; `word/document.xml` diurai via PowerShell + .NET `XmlDocument` (namespace `w:`) |
| Alat alternatif | `python-docx` **tidak terpasang** (Python 3.10.6/3.14.3 tersedia) → fallback ZIP/XML dipakai |
| Integritas file asli | **Tidak diubah** (disalin ke temp sebelum ekstraksi) |

**Metrik ekstraksi:**
- **Paragraf:** 196
- **Tabel:** 0
- **Gambar tersemat:** 20 (`word/media/image1.png` … `image20.png`)
- **Penanda `PAGE`:** 10

---

## 2. STRUKTUR HALAMAN (PAGE MARKERS)

| # | PAGE | Paragraf | MASUKAN |
|---|---|---|---|
| 1 | BERANDA | P1–P40 | #1–#6 |
| 2 | TENTANG TMCC | P43–P110 | #7–#10 |
| 3 | STRUKTUR ORGANISASI | P112–P119 | #11 |
| 4 | ANGGOTA DAN RELAWAN | P123–P128 | #12 |
| 5 | PARTNERSHIP | P130–P137 | #13 |
| 6 | PROGRAM | P139–P146 | #14 |
| 7 | KUALITAS AIR | P149–P154 | #15 |
| 8 | GALERI | P156–P159 | (tanpa nomor) |
| 9 | BERITA | P161–P169 | #16 |
| 10 | KONTAK | P171–P178 | #17 |
| — | ADMIN | P182–P194 | — |

---

## 3. INVENTARIS GAMBAR

| Image | Paragraf | Dimensi | Ukuran | Konteks seksi |
|---|---|---|---|---|
| image1.png | P7 | 1256×493 | 180 KB | BERANDA #2 (logo/hero) |
| image2.png | P15 | 1242×309 | 32 KB | BERANDA #3 (parameter air) |
| image3.png | P21 | 1011×607 | 195 KB | BERANDA #4 (urutan program/portal) |
| image4.png | P25 | 1287×489 | 551 KB | BERANDA #5 (program) |
| image5.png | P33 | 679×294 | 30 KB | BERANDA #6 (copy TMCC) |
| **image6.png** | **P46** | **1208×366** | **397 KB** | **ABOUT #7 — Hero** |
| **image7.png** | **P55** | **1288×569** | **638 KB** | **ABOUT #8 — Sekilas Danau** |
| **image8.png** | **P66** | **1291×616** | **230 KB** | **ABOUT #9 — Profil & Sejarah** |
| **image9.png** | **P88** | **1253×665** | **86 KB** | **ABOUT #9 — Visi/Misi/Tujuan** |
| **image10.png** | **P105** | **1182×702** | **466 KB** | **ABOUT #10 — Dewan Pendiri** |
| image11.png | P115 | 1149×343 | 372 KB | STRUKTUR ORGANISASI #11 |
| image12.png | P126 | 1190×614 | 84 KB | ANGGOTA #12 |
| image13.png | P133 | 1312×383 | 41 KB | PARTNERSHIP #13 |
| image14.png | P142 | 1123×290 | 41 KB | PROGRAM #14 |
| image15.png | P152 | 1159×664 | 93 KB | KUALITAS AIR #15 |
| image16.png | P158 | 1146×595 | 388 KB | GALERI |
| image17.png | P164 | 586×298 | 21 KB | BERITA #16 |
| image18.png | P174 | 1114×263 | 38 KB | KONTAK #17 |
| image19.png | P185 | 1320×210 | 38 KB | ADMIN (filter) |
| image20.png | P191 | 1062×85 | 36 KB | ADMIN (navigasi) |

> Gambar-gambar tersemat adalah **tangkap layar situs + anotasi klien** (mis. panah/teks penunjuk), bukan berkas aset final gambar halaman. Berkas aset final **tidak** diekstrak sebagai gambar siap-pakai.

---

## 4. KONTEN ABOUT TMCC (VERBATIM — `docs/revisi web.docx`)

> Dikutip persis. Tidak ada parafrase. Nomor `[P..]` merujuk paragraf di `document.xml`.

### MASUKAN #7 — About Hero
- `[P48]` Perubahan redaksional :
- `[P49]` **Tentang Kami**
- `[P51]` **ADVOKASI LINGKUNGAN LOKAL**
- `[P52]` **Membangun masa depan yang berkelanjutan untuk Danau Matano**

### MASUKAN #8 — Sekilas Danau Matano
- `[P57]` Gambar : sudah kami ganti (tapi bisakah disiapkan di kanan bawah gambar, cukup yg kecil saja tempat keterangan sumber gambar atau tdk perlu ya?
- `[P60]` **Sekilas Danau Matano**
- `[P61]` Danau Matano merupakan danau tektonik purba terdalam di Indonesia dan Asia Tenggara, serta terdalam ke-8 di dunia. Danau ini terletak Kecamatan Nuha, Kabupaten Luwu Timur, Sulawesi Selatan, dengan kedalaman sekitar 590 meter.
- `[P62]` Sebagai "Danau Purba", Matano memiliki keanekaragaman hayati endemik yang luar biasa, termasuk ikan-ikan unik dan biota air tawar yang tidak ditemukan di tempat lain di dunia. Kualitas airnya yang jernih dan ekosistem yang rapuh membuatnya sangat penting untuk dilestarikan.
- `[P63]` Ancaman terhadap kelestarian Danau Matano terus meningkat seiring dengan aktivitas manusia, mulai dari pencemaran, limbah domestik, perkebunan, tata kelola wisata yang lemah hingga dampak penambangan termasuk bertambahnya jumlah penduduk dipicu keberadaan sektor industri ini.  Oleh karena itu, langkah nyata untuk menjaga dan memulihkan kondisi danau ini menjadi sangat krusial.

### MASUKAN #9 (blok 1) — Profil & Sejarah / Potensi TMCC / Legalitas
- `[P68]` Photo/logo : diperkecil dan gunakan yang ada tulisan tova Matano care center
- `[P71]` **Profil & Sejarah**
- `[P72]` Tova Matano Care Center (TMCC) lahir dari kepedulian yang mendalam terhadap pentingnya menjaga Danau Matano agar tetap lestari, dengan segala pesona keindahan, keunikan, dan keistimewaannya.
- `[P73]` Dalam bahasa Sorowako, "Tova" atau "Towa" Matano berarti Danau Matano. Sementara itu, care center merupakan wadah bagi masyarakat lokal yang hidup secara turun-temurun di pesisir Danau Matano untuk mendiskusikan dan menyebarkan informasi yang jujur mengenai kondisi aktual Danau Matano, sekaligus menjadi motivator Aksi Gerakan Penyelamatan Danau.
- `[P75]` TMCC adalah tempat bertemunya keindahan dan fakta kritis. Selain menyajikan keindahan pesona Danau Matano, TMCC juga melakukan inventarisasi permasalahan yang dinilai perlu disampaikan dan disuarakan dalam bentuk data dan informasi yang jujur mengenai perubahan kualitas lingkungan yang sesungguhnya sedang terjadi. Hal ini dilakukan untuk menumbuhkan rasa peduli, serta mendorong masyarakat untuk mendukung, bergabung, dan turut serta berbuat sesuatu dalam upaya memperbaiki dan menjaga kelestarian Danau Matano.
- `[P77]` **Potensi TMCC** + Sebagai organisasi yang didirikan oleh masyarakat lokal, pegiat TMCC tumbuh dengan "local wisdom" yang kuat sejak masa lalu. Dengan "indigenous knowledge" yang dimilikinya, pegiat TMCC dipastikan berpengalaman, memahami, sekaligus memiliki keterampilan mengenai perilaku dan interaksi manusia secara turun-temurun dengan Danau Matano, dari masa lalu hingga saat ini. TMCC merupakan narasumber primer dan saksi atas setiap perubahan yang terjadi di Danau Matano.
- `[P79]` Kami percaya bahwa Danau Matano bukan sekadar genangan air, melainkan rumah bagi keanekaragaman hayati endemik dan anugerah Allah SWT yang harus dijaga untuk generasi mendatang. Yang tidak kalah penting, sebagai masyarakat yang lahir, hidup, dan besar di pesisir danau ini, Danau Matano merupakan kebanggaan dan bagian dari cerita perjalanan hidup kami. Oleh karena itu, interaksi TMCC dengan Danau Matano lebih dari sekadar menikmati pesonanya, tetapi juga upaya untuk mengukur kehidupan leluhur di masa lalu dan membayangkan bagaimana kami harus mewariskan Danau Matano kepada masa depan.
- `[P81]` **Legalitas**
- `[P82]` Surat Keputusan Menteri Hukum Republik Indonesia : Nomor AHU-01482.AH.02.01.TAHUN 2025
- `[P83]` Akta Notaris No. 07 Tanggal 15 April 2026
- `[P84]` NPWP: 1000.0000.0956.7880

> Catatan: judul "Potensi TMCC" tergabung dengan awal P1 di DOCX (`Potensi TMCCSebagai organisasi...`); dipisah pada pelaporan, teks tidak diubah.

### MASUKAN #9 (blok 2) — Visi, Misi, Tujuan
- `[P92]` **Visi**
- `[P93]` Terwujudnya kelestarian Danau Matano sebagai ekosistem yang sehat, berkelanjutan, dan memberi kehidupan bagi generasi kini dan mendatang
- `[P94]` **Misi**
- `[P95]` Menggerakkan dan berperan aktif dalam upaya penyelamatan dan pelestarian Danau Matano.
- `[P96]` Mendorong kebijakan dan advokasi lingkungan yang berkeadilan, inklusif, dan berpihak pada keberlanjutan ekosistem Danau Matano.
- `[P97]` Meningkatkan kesadaran masyarakat mengenai pentingnya pelestarian Danau Matano.
- `[P98]` Membangun jejaring kemitraan dan pemberdayaan komunitas untuk memperkuat gerakan penyelamatan lingkungan.
- `[P99]` **Tujuan**
- `[P100]` Mendukung program pemerintah dan swasta di bidang lingkungan hidup, khususnya pelestarian Danau Matano.
- `[P101]` Menyajikan informasi dan data yang akurat mengenai kondisi Danau Matano serta perubahan kualitas lingkungannya.
- `[P102]` Melakukan upaya penyelamatan dan pelestarian Danau Matano melalui program revitalisasi, konservasi, rehabilitasi, restorasi dan pengelolaan lingkungan yang berkelanjutan.
- `[P103]` Mendorong terbukanya ruang diskusi dan seminar untuk membahas kelestarian Danau Matano, serta aktif melakukan kampanye lingkungan

### MASUKAN #10 — Dewan Pendiri
- `[P107]` Photo : tolong diperkecil
- `[P109]` **DEWAN PENDIRI**
- `[P110]` TMCC didirikan oleh dua anak pesisir Danau Matano yang menyadari pentingnya ruang bagi masyarakat lokal untuk bergerak secara konsisten, terencana, fokus, dan peduli terhadap kondisi Danau Matano. Kelahiran TMCC dilatarbelakangi oleh kejenuhan karena kerusakan Danau Matano hanya menjadi topik yang menarik untuk dibahas dalam berbagai pertemuan, tanpa diikuti aksi nyata. Selain itu, terdapat rasa bosan dan putus asa karena menunggu kehadiran para pemangku kepentingan untuk benar-benar melakukan aksi dan gerakan penyelamatan di Danau Matano. TMCC juga hadir untuk menjawab kegelisahan atas pemberitaan yang hanya berfokus pada eksposur pesona Danau Matano. Sementara itu, para pendiri menyadari bahwa di danau ini, kerusakan lingkungan sebenarnya sedang terjadi secara terus-menerus dan semakin parah dari waktu ke waktu.

---

## 5. RINGKASAN REVISI NON-ABOUT (untuk konteks lintas fase)

| PAGE | MASUKAN | Ringkas |
|---|---|---|
| BERANDA | #1 | Cek pesan berjalan tidak muncul di monitor 100% |
| BERANDA | #2 | Logo bertulisan; copy "Berdiri untuk pemulihan…" + paragraf "degradasi lingkungan…" |
| BERANDA | #3 | Tampilkan semua parameter kualitas air |
| BERANDA | #4 | Naikkan Program Unggulan di atas Portal Keanggotaan |
| BERANDA | #5 | Program: Tova Water Patrol, Tova Edu & Research, Tova Care Action, Tova Champaign & Event |
| BERANDA | #6 | Samakan copy TMCC dengan #2 |
| STRUKTUR ORG | #11 | Akan diubah di admin (bukan bagan) |
| ANGGOTA | #12 | Pastikan foto anggota muncul |
| PARTNERSHIP | #13 | Subtitle; tanya background |
| PROGRAM | #14 | Subtitle "Program Kami / Empat pilar…"; tanya background |
| KUALITAS AIR | #15 | Gambar titik/posisi stasiun sampel |
| GALERI | — | Keterangan gambar tampil |
| BERITA | #16 | "Tova Blog / Berita dan Cerita"; tambah kolom Cerita |
| KONTAK | #17 | "Hubungi Kami / Ada saran dan masukan…" |
| ADMIN | — | Filter kolom NAMA (sort alfabet) untuk daftar anggota |
| NAV | — | Urutan: Home–About–Program–Water Q–Galeri–News–Org–Member–Partnership–contact |

---

## 6. VERIFIKASI SILANG DENGAN DATABASE (READ-ONLY)

| Item | Hasil verifikasi lokal |
|---|---|
| Pendiri (2 record) | Mukmin Heba (Direktur Utama TMCC); Syarmudin Madri Ganiasi (Koordinator Dewan Eksekutif Organisasi); `org_name` = TOVA MATANO CARE CENTER; foto ada |
| `about_hero_image_url` | `settings/01M6BBX7HR70S6395VP3WMXCE6.jpeg` |
| `about_lake_image_url` | `settings/01M6BBX7JDTVSP4KK6PBK87WX0.jpeg` |

> Verifikasi pada DB lokal. DB produksi diharapkan identik (data riil) namun disarankan spot-check.

---

## 7. CATATAN INTEGRITAS

- DOCX asli **tidak diubah**.
- Tidak ada file aplikasi/DB/`.env` yang dimodifikasi.
- Seluruh kutipan dipertahankan sesuai ejaan & tanda baca klien (termasuk spasi ganda pada P63: `…sektor industri ini.  Oleh karena itu…`).
