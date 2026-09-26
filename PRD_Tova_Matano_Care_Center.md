# PRD — Website Tova Matano Care Center (TMCC)

**Versi:** 1.0
**Status:** Draft — siap development
**Stack:** Laravel + Filament (admin) · Inertia.js + React (publik) · MySQL/PostgreSQL

---

## 1. Latar Belakang

Tova Matano Care Center (TMCC) adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano, Kecamatan Nuha, Kabupaten Luwu Timur, Sulawesi Selatan, dari ancaman pencemaran. Website ini dibangun sebagai **pusat data, edukasi, dan aksi** — media utama TMCC untuk membangun kredibilitas, menggalang relawan & donasi, serta mempublikasikan data lingkungan secara transparan ke publik.

## 2. Tujuan Produk

- Membangun kepercayaan publik lewat transparansi data (struktur organisasi, kualitas air, program kerja)
- Menyederhanakan proses rekrutmen relawan (pendaftaran → approval → publikasi otomatis)
- Memberi admin kendali penuh atas seluruh konten tanpa bantuan developer setelah go-live
- Menjadi kanal edukasi & informasi program lingkungan ke masyarakat luas

## 3. Peran Pengguna

| Role | Akses |
|---|---|
| **Admin** | Login ke dashboard; CRUD penuh atas seluruh entitas (slider, anggota, relawan, program, blog, galeri, data kualitas air, pengaturan situs) |
| **Publik (User)** | Browsing seluruh halaman tanpa akun; mengisi form pendaftaran relawan; mengisi form kontak |

Tidak ada sistem akun/login untuk publik (sesuai keputusan: alur relawan pakai kode pendaftaran + notifikasi, bukan login).

## 4. Ruang Lingkup

### 4.1 Termasuk (In Scope — Fase 1)
- 8 halaman publik + 1 dashboard admin
- Sistem ranking poin anggota (manual oleh admin)
- Alur pendaftaran & approval relawan dengan kode pendaftaran + notifikasi email/WA
- Struktur organisasi 6 lapis (format accordion)
- Dashboard kualitas air dengan input manual berkala
- Search sederhana (full-text, native database)
- Kanal donasi (tampilan info rekening saja, tanpa laporan transparansi dana)

### 4.2 Tidak Termasuk (Out of Scope — dipertimbangkan Fase 2+)
- Sistem login/akun untuk relawan atau anggota
- Laporan transparansi penggunaan dana donasi
- Integrasi sensor otomatis untuk data kualitas air (saat ini input manual)
- Multi-bahasa (ID/EN)
- Native mobile app

## 5. Sitemap

| # | Halaman | Ringkasan |
|---|---|---|
| 1 | Beranda | Hero slider, running text, Portal Keanggotaan, ringkasan tentang, statistik, highlight program |
| 2 | Tentang TMCC | Profil & sejarah, pendiri, visi-misi-tujuan |
| 3 | Struktur Organisasi | Accordion 6 lapis (Pelindung → Divisi) |
| 4 | Anggota | List ranking poin keaktifan |
| 5 | Program TMCC | 4 sub-program: Water Patrol, Edu, Action, Riset |
| 6 | Blog | Artikel edukasi, rilis pers, berita, kebijakan |
| 7 | Galeri & Media | Dokumentasi foto/video |
| 8 | Gabung / Donasi | Form pendaftaran relawan + info rekening donasi |
| 9 | Kontak | Alamat, kontak, medsos, form kontak |
| — | Dashboard Admin | Login + CRUD semua entitas + approval relawan + input data air |

Navigasi global: navbar (5 menu utama) + tombol **Donasi** & **Ayo Bergabung** selalu terlihat.

## 6. Functional Requirements per Halaman

### 6.1 Beranda
- [ ] Hero slider: multi-slide, auto-play + swipe/klik manual, auto-hide kalau tidak ada slide aktif
- [ ] Running text: admin CRUD, auto-hide kalau kosong
- [ ] Portal Keanggotaan: rotating display anggota top-poin (foto, nama, pesan lingkungan), total jumlah anggota, link/QR daftar
- [ ] Ringkasan Tentang TMCC + highlight 4 Program + CTA Donasi/Gabung

### 6.2 Tentang TMCC
- [ ] Profil, sejarah pendirian, legalitas (SK Menkumham, NPWP) — admin-editable rich text
- [ ] Profil pendiri (foto, nama, peran) — CRUD
- [ ] Visi, Misi, Tujuan — admin-editable

### 6.3 Struktur Organisasi
- [ ] 6 kategori accordion: Pelindung, Penasehat, Dewan Pakar, Badan Eksekutif, Pengurus, Divisi (6 sub-divisi di dalamnya)
- [ ] Tiap kategori: admin CRUD nama, role, foto (opsional untuk kategori institusional)
- [ ] Default state: kategori pertama terbuka, sisanya tertutup

### 6.4 Anggota
- [ ] List seluruh anggota terurut otomatis berdasarkan poin (DESC)
- [ ] Admin CRUD anggota: nama, foto, divisi, poin, pesan lingkungan
- [ ] Reset poin per anggota (set ke 0) oleh admin

### 6.5 Program TMCC
- [ ] 4 program tetap: Tova Water Patrol, Tova Edu, Tova Action, Tova Riset
- [ ] Tiap program: grid thumbnail aktivitas → klik → halaman detail
- [ ] Detail aktivitas: judul, gambar, konten, container embed YouTube/Instagram — admin CRUD
- [ ] Dashboard Kualitas Air ditampilkan di dalam Tova Riset: grafik line chart pH & kejernihan dari `WaterQualityRecord`, admin input manual per tanggal

### 6.6 Blog
- [ ] List artikel dengan filter kategori (Edukasi/Rilis Pers/Berita/Kebijakan)
- [ ] Admin CRUD artikel (judul, kategori, cover, konten rich text)

### 6.7 Galeri & Media
- [ ] Grid foto/video, admin CRUD (upload, caption)

### 6.8 Gabung / Donasi
- [ ] Form pendaftaran relawan: nama, email, telepon, alamat, motivasi
- [ ] Setelah submit: sistem generate `registrationCode` unik, tampilkan ke user, kirim email konfirmasi
- [ ] Info rekening donasi: nama bank, no. rekening, atas nama — admin-editable, tanpa laporan dana

### 6.9 Kontak
- [ ] Alamat, telepon/WA, email, link medsos (IG/FB/Tiktok/X) — admin-editable
- [ ] Form kontak sederhana (nama, email, pesan) → kirim ke email admin

### 6.10 Dashboard Admin
- [ ] Login admin (email + password, session-based)
- [ ] Menu CRUD untuk seluruh entitas di atas
- [ ] Halaman khusus **Approval Relawan**: list status Pending, tombol Approve/Reject
  - Approve → status jadi `APPROVED`, trigger email/WA notifikasi ke relawan, otomatis tampil di list relawan publik
  - Reject → status `REJECTED`, relawan tidak tampil publik
- [ ] Halaman input data kualitas air (tanggal, pH, kejernihan, catatan)

## 7. Alur Kunci (User Flow)

### 7.1 Pendaftaran Relawan
```
User isi form pendaftaran
   → Sistem generate registrationCode unik
   → Tampilkan kode ke user + kirim email berisi kode & status "Pending"
   → Admin buka dashboard → tab Approval Relawan
   → Admin klik Approve/Reject
        Approve → email/WA notifikasi "Selamat, Anda diterima" + kode status
                 → data otomatis tampil di halaman publik (list relawan)
        Reject  → email notifikasi penolakan (opsional pesan alasan)
   → User bisa cek status manual di halaman "Cek Status Pendaftaran" pakai registrationCode
```

### 7.2 Ranking Anggota → Portal Keanggotaan
```
Admin input/edit/reset poin anggota di dashboard
   → Data tersimpan di tabel Member
   → Halaman Anggota: query ORDER BY points DESC, tampilkan semua
   → Beranda (Portal Keanggotaan): query sama, LIMIT 5-10, tampilkan rotating
```

### 7.3 Dashboard Kualitas Air
```
Admin input hasil pengukuran berkala (tanggal, pH, kejernihan)
   → Data tersimpan di WaterQualityRecord
   → Halaman Tova Riset render line chart dari seluruh record, urut tanggal
```

## 8. Data Model

Struktur lengkap tersedia di `database/migrations/` dan `app/Models/` (Laravel Eloquent). Ringkasan entitas utama:

`Admin` · `HeroSlide` · `RunningText` · `Founder` · `OrgCategory` · `OrgPerson` · `Member` · `Volunteer` · `Program` · `Activity` · `BlogPost` · `GalleryItem` · `WaterQualityRecord` · `DonationChannel` · `SiteSettings`

## 9. Tech Stack

| Layer | Pilihan |
|---|---|
| Backend & Admin Panel | Laravel + Filament (admin CRUD auto-generated dari Eloquent model) |
| Frontend Publik | Inertia.js + React + TypeScript + Tailwind CSS |
| Database | MySQL/PostgreSQL + Eloquent ORM |
| Auth (admin only) | Laravel default auth (dipakai Filament) — publik tidak butuh akun |
| Storage gambar/video | Laravel Filesystem (S3-compatible: Cloudflare R2) |
| Chart | Recharts (dalam komponen React via Inertia) |
| Email notifikasi | Laravel Mail (Nodemailer setara di ekosistem Laravel) |
| Notifikasi WA | Custom Notification Channel → Fonnte API |
| Search | MySQL/PostgreSQL full-text search, atau Laravel Scout kalau perlu upgrade nanti |
| Hosting | VPS (spesifikasi dicek ulang di tahap deployment, minimal setara paket M — 2GB RAM) |

> Struktur data lengkap: lihat `database/migrations/` dan `app/Models/` (Laravel) — hasil konversi dari rancangan awal.

## 10. Non-Functional Requirements

- **Responsif** — mobile-first, breakpoint utama di 375px, 768px, 1280px
- **Performa** — Lighthouse score target ≥ 85 (Performance & SEO)
- **SEO dasar** — meta tag dinamis per halaman, sitemap.xml, open graph image
- **Keamanan** — hash password admin (bcrypt), rate limit form publik (pendaftaran & kontak) untuk cegah spam, sanitasi input
- **Aksesibilitas** — kontras warna memadai, alt text gambar, navigasi keyboard untuk accordion & slider

## 11. Roadmap & Tahapan Development

> Checklist ini jadi acuan progres — centang tiap item selesai.

### Fase 0 — Setup & Fondasi (Minggu 1)
- [ ] Init project Laravel + install Filament + Inertia.js + React starter
- [ ] Setup database, jalankan migration awal (`database/migrations/`)
- [ ] Setup auth admin (Laravel default auth, dipakai Filament panel)
- [ ] Setup struktur folder & design system (token warna/font dari prototipe)
- [ ] Deploy skeleton ke VPS (CI/CD dasar)

### Fase 1 — Dashboard Admin Inti (Minggu 2)
- [ ] CRUD: HeroSlide, RunningText, Founder, SiteSettings
- [ ] CRUD: OrgCategory + OrgPerson (Struktur Organisasi)
- [ ] CRUD: Member (Anggota + poin)
- [ ] Upload gambar ke Cloudflare R2 (dipakai di semua modul CRUD berikutnya)

### Fase 2 — Halaman Publik Utama (Minggu 3)
- [ ] Beranda: slider, running text, Portal Keanggotaan, ringkasan
- [ ] Tentang TMCC
- [ ] Struktur Organisasi (accordion)
- [ ] Anggota (ranking list)

### Fase 3 — Program, Blog, Galeri (Minggu 4)
- [ ] CRUD & halaman publik: Program + Activity (4 sub-program)
- [ ] CRUD & halaman publik: BlogPost
- [ ] CRUD & halaman publik: GalleryItem

### Fase 4 — Relawan, Donasi, Dashboard Air (Minggu 5)
- [ ] Form pendaftaran relawan + generate registrationCode
- [ ] Integrasi email (Nodemailer) & WA (Fonnte) notifikasi
- [ ] Halaman Approval Relawan di admin
- [ ] Halaman Cek Status Pendaftaran (publik)
- [ ] CRUD DonationChannel + tampilan info rekening
- [ ] Input & visualisasi WaterQualityRecord (Recharts)

### Fase 5 — Search, Kontak, Polish (Minggu 6)
- [ ] Fitur search (full-text)
- [ ] Halaman & form Kontak
- [ ] SEO: meta tag, sitemap.xml, OG image
- [ ] Testing responsif lintas device
- [ ] Testing performa (Lighthouse) & perbaikan
- [ ] Training admin cara pakai dashboard

### Fase 6 — Go-Live
- [ ] Setup domain `.org.id` + SSL
- [ ] Deployment production final
- [ ] Handover dokumentasi teknis + akun admin ke client

## 12. Asumsi & Pertanyaan Terbuka

- Data lengkap Divisi Pelestarian & Penyelamatan Danau (perwakilan Desa Nuha & Matano) masih menunggu dari client
- Foto asli (hero, anggota, kegiatan) menyusul dari client, sementara pakai placeholder
- Volume traffic diasumsikan skala NGO menengah — perlu dipantau pasca-launch untuk evaluasi kapasitas hosting

## 13. Definition of Done (per Fase)

Sebuah fase dianggap selesai jika:
1. Seluruh checklist item di fase tersebut ter-centang
2. Fitur sudah diuji di localhost dengan data dummy
3. Tidak ada error di console/build
4. Sudah di-review sendiri lintas ukuran layar (mobile & desktop)
