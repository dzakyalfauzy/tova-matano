# Laporan Akhir — Tema Terang Permanen & Perbaikan Kontras Admin TMCC

Panel: `/admin` (Filament 5.8.4, Laravel 13.33.0)
Ruang lingkup: tema, kontras sidebar/header, tombol, form. Tidak ada perubahan skema database, logika bisnis, atau styling situs publik.

## A. Akar Masalah (Root Cause)

### A.1 Sidebar dan header brand
- Filament menetapkan warna label lewat child element: `.fi-sidebar-item-label { color: var(--primary-700) }`. Warna yang dipasang pada parent `<a>` tidak pernah sampai ke label karena deklarasi langsung mengalahkan inheritance.
- Setiap item diberi pil abu-abu terang oleh `.fi-sidebar-item-btn { background-color: var(--gray-100) }`, sehingga teks navy/putih di atas navy menjadi tidak terbaca.
- Hover/fokus memakai selector 4 kelas: `.fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover { background-color: var(--gray-100) }`, sehingga rule hover satu/dua kelas kalah.
- Header sidebar diwarnai putih oleh `.fi-body-has-topbar .fi-sidebar-header { background-color: var(--color-white); padding-inline: 1.5rem; box-shadow: ... }` (2 kelas). Rule `.fi-sidebar-header` satu kelas kalah, sehingga judul brand putih berada di atas header putih (tidak terlihat).
- `--topbar-height` didefinisikan Filament pada `.fi-body` sebesar `4rem`; header sidebar perlu disejajarkan dengan nilai tersebut.

### A.2 Tombol
- Tombol Filament 5 merender utility class `fi-bg-color-600`, `hover:fi-bg-color-500`, `fi-text-color-0`, dan `.fi-color-primary` yang memetakan `--color-600` ke `--primary-600`.
- `Color::hex()` menghasilkan palet turunan, sehingga shade 600 tidak persis `#1E7F8C` (tombol tampak lebih cyan/terang).
- Selector lama `.fi-btn-color-primary` tidak pernah ada di markup Filament, sehingga override CSS lama tidak berefek.

### A.3 Form
- Selector lama `.fi-fo-field-wrp-label`, `.fi-fo-field-wrp-helper-text`, `.fi-validate-error`, dan `.fi-textarea` tidak ada pada Filament 5.8.4.
- Border, background, dan focus ring field berada pada wrapper `.fi-input-wrp`, bukan pada `<input>`. Menata `.fi-input` seolah-olah memiliki border menghasilkan efek ganda/tidak konsisten.
- Kelas validasi yang benar: `.fi-fo-field-wrp-error-message` dan `.fi-fo-field-wrp-error-list`.
- Textarea dirender di dalam `.fi-fo-textarea > textarea`, bukan `.fi-textarea`.

### A.4 Tema gelap
- Panel sebelumnya mewarisi preferensi sistem. Untuk memastikan tampilan identik di Windows/browser mode terang maupun gelap, tema harus dikunci dari sisi panel, HTML, dan CSS.

## B. Resolusi Tema (Terang Permanen)

Perubahan pada `app/Providers/Filament/AdminPanelProvider.php`:
- `->darkMode(false)` — mematikan mode gelap Filament.
- `->defaultThemeMode(ThemeMode::Light)` — nilai default eksplisit.
- `->themeSwitcher(false)` — switcher tidak dirender.
- `PanelsRenderHook::HEAD_END` menyisipkan:
  - `<meta name="color-scheme" content="light only">`
  - stylesheet `css/tmcc-admin.css` dengan cache-busting `?v=<filemtime>`
  - skrip yang menghapus class `dark`, memasang `data-bs-theme="light"`, dan menulis `localStorage.theme = "light"`.

Pada `public/css/tmcc-admin.css`:
- `:root` dan `.fi` memakai `color-scheme: only light`.
- Tidak ada selector `.dark`, `prefers-color-scheme`, atau aturan tema gelap di file ini.

Hasil pemeriksaan HTML: `<html lang="id" dir="ltr" class="fi">` tanpa class `dark`, dan tidak ada `fi-theme-switcher`.

## C. Perubahan Visual

### C.1 Sidebar
- Latar sidebar navy `#123A52`; item default transparan.
- Label item langsung diwarnai putih (`#FFFFFF`) dan ikon emas `#E3A23A`.
- Hover/fokus: latar `#1A4A5F`, label/ikon putih.
- Item aktif: latar teal `#1E7F8C`, label putih semi-bold, ikon putih, garis emas `inset 3px 0 0`, dan tetap teal (teal-dark `#176873`) saat hover/fokus.
- Label grup `#B8CCD5` uppercase dengan ikon grup emas; badge memakai latar putih transparan.
- Sidebar tetap dapat di-scroll (`overflow: hidden auto` dari Filament dipertahankan).

### C.2 Header brand
- Header sidebar putih, `min-height: 4rem` (selaras topbar), padding `0.7rem 1rem`, border bawah hangat, tanpa shadow.
- Logo memakai plate paper `#F6F3EA` berukuran 38px dengan border halus.
- Nama brand navy `#123A52`; tagline emas `#E3A23A` dengan letter-spacing.
- Login/simple layout: kartu putih, latar paper dengan gradien teal/emas halus; brand dan heading navy.

### C.3 Tombol
- Warna diambil dari palet panel, bukan override CSS mati:
  - Primary `600 = #1E7F8C`, hover `500 = #176873`, teks putih.
  - Success `600 = #2F7D5C`, hover `500 = #276A4E`.
  - Warning `600 = #E3A23A`, hover `500 = #C9861F`.
  - Danger `600 = #B4453F`, hover `500 = #983832`.
  - Info `600 = #123A52`, hover `500 = #265F78`.
- `.fi-btn` hanya menyeragamkan radius (9px) dan weight (500); padding/ukuran bawaan Filament tidak ditimpa agar varian ukuran tetap bekerja.
- Fokus primary diberi outline emas `2px`.

### C.4 Form
- `.fi-input` dan `.fi-select-input`: tinggi 44px, padding `0.5rem 0.75rem`, font 0.875rem, warna teks `--tmcc-ink`; border/background/focus ring tetap dari `.fi-input-wrp`.
- `.fi-input-wrp` radius 9px; focus ring memakai `--primary-600` (teal persis); invalid ring memakai `--danger-600` (merah persis).
- `.fi-fo-textarea textarea`: warna, padding, dan `resize: vertical`; autosize Filament tetap berlaku.
- Label field navy 0.875rem; required mark merah; pesan validasi memakai kelas Filament 5 yang benar.
- Section: kartu putih, border hangat, radius 14px, judul navy Fraunces.

### C.5 Tabel dan dashboard
- Tabel/kartu putih dengan border `#E3DDCF`, radius 14px, shadow halus.
- Header tabel paper `#F6F3EA`, label navy uppercase.
- Baris hover `#FBF9F4`.
- Dashboard mempertahankan data dan query yang ada; hanya tampilan yang disesuaikan.

## D. Perubahan Teknis

| File | Perubahan |
|------|-----------|
| `app/Providers/Filament/AdminPanelProvider.php` | Palet eksplisit 50–950 untuk primary/info/success/warning/danger; `darkMode(false)`; `defaultThemeMode(ThemeMode::Light)`; `themeSwitcher(false)`; meta `color-scheme`; CSS dengan cache-busting; skrip kunci tema terang. |
| `public/css/tmcc-admin.css` | Selector sidebar/header/tombol/form diperbaiki sesuai markup Filament 5; `color-scheme: only light`; token TMCC; aturan fokus/validasi. |
| `docs/VISUAL_REFINEMENT_REPORT.md` | Laporan ini (menggantikan laporan lama yang memakai selector tidak valid). |

Catatan teknis:
- Stylesheet kustom dimuat setelah `css/filament/filament/app.css` (diverifikasi lewat posisi byte pada HTML: 4081 vs 5326).
- Cache-busting memakai `filemtime`, sehingga perubahan CSS tidak tertahan cache browser.
- Tidak ada dependency baru, upgrade paket, perubahan route, atau perubahan database.

## E. Verifikasi

| Pemeriksaan | Hasil |
|-------------|-------|
| `php -l AdminPanelProvider.php` | Lulus, tanpa syntax error |
| `vendor/bin/pint --dirty --format agent` | Lulus |
| `php artisan view:clear` | Lulus |
| `npm run build` | Lulus, 3450 modul, 47.17 detik (hanya peringatan deprecation Vite) |
| `php artisan route:list --path=admin` | Route dashboard dan resource admin tersedia |
| Render internal `/admin/login` | HTTP 200; meta `color-scheme` ada; CSS kustom ada; skrip light ada; tanpa class `dark`; tanpa theme switcher; brand ada |
| Render internal `/admin` (user admin) | HTML 156.937 byte; sidebar, item aktif, header sidebar, dan brand ada; tanpa class `dark`; tanpa theme switcher |
| Urutan CSS | CSS kustom setelah CSS Filament |
| Peta warna tombol | `bg = 600 (#1e7f8c)`, `hover = 500 (#176873)`, `text = putih` |
| Variabel palet pada HTML | `--primary-600`, `--primary-500`, `--danger-600`, `--success-600`, `--warning-600` terdefinisi (OKLCH ekuivalen dari hex TMCC) |
| Keseimbangan brace CSS | 166 buka / 166 tutup |
| `php artisan test --compact` | **TIDAK TERVERIFIKASI** — proses melewati 180 detik dan dihentikan; tidak ada test khusus panel admin |

Catatan: verifikasi dilakukan dengan merender request Laravel secara internal dan memeriksa HTML yang dihasilkan. Tidak ada server HTTP yang dijalankan dan tidak ada pemeriksaan piksel di browser.

## F. Sisa Pekerjaan / Pemeriksaan Manual

1. Buka `/admin` di browser pada OS mode terang dan gelap; pastikan tampilan identik dan tidak ada kilatan tema gelap saat reload.
2. Periksa hover, fokus keyboard (Tab), dan item aktif sidebar secara visual.
3. Periksa form create/edit salah satu resource: label, input, select, textarea, pesan validasi, dan tombol simpan.
4. Periksa responsif: sidebar collapse di desktop, drawer di mobile, tabel lebar.
5. Jalankan suite Pest penuh secara terpisah; sebelumnya proses tidak selesai dalam 180 detik.

Tidak ada klaim bahwa pemeriksaan browser atau suite Pest penuh telah lulus.
