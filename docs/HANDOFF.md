# HANDOFF — Tova Matano Care Center (TMCC)

Dokumen serah terima untuk asisten AI / developer baru.
Disusun dari pembacaan kode pada commit `23a2a79` ("revisi hero slider"), working tree bersih.

**Legenda status**
- **TERVERIFIKASI** — dicek langsung lewat kode, `php artisan route:list`, query DB, atau test run.
- **BELUM DIUJI** — kode ada dan lengkap, tetapi belum dijalankan/diuji otomatis. Tampilan visual **TIDAK DIVERIFIKASI** (tidak ada browser test).
- **BELUM** — tidak ada di kode.

> Catatan verifikasi: pengecekan HTTP manual tidak dilakukan (diminta tidak menjalankan server). Semua status UI di bawah = BELUM DIUJI secara visual. Test suite dijalankan: **40 test, 36 lulus, 4 gagal** (detail di §10).

---

## 1. Ringkasan Project

Website organisasi nirlaba **Tova Matano Care Center (TMCC)**: yayasan pelestarian Danau Matano, Nuha, Luwu Timur, Sulawesi Selatan.

| Aspek | Isi |
|---|---|
| Tujuan | Pusat data, edukasi, dan aksi lingkungan; membangun kepercayaan publik lewat transparansi data kualitas air; rekrutmen relawan & donasi |
| Client | TMCC (organisasi nirlaba) |
| Pengguna | **Admin** (login di `/admin`, kelola seluruh konten) dan **publik** (tanpa login, hanya baca + 2 form: relawan & kontak) |
| Akun publik | Tidak ada. Relawan tidak login; status dicek via kode registrasi + email |
| Bahasa | Indonesia (default) + Inggris (sebagian) |

---

## 2. Tech Stack & Versi

| Komponen | Versi terpasang | Catatan |
|---|---|---|
| PHP | **8.4.26** (runtime, ZTS) | `composer.json` minta `^8.3`; `AGENTS.md` menyebut 8.5 → **tidak konsisten**, TIDAK YAKIN apakah 8.5 wajib |
| Laravel | **13.33.0** | |
| Filament | **5.8.4** (`filament/filament`) | Panel admin |
| Livewire | 4.4.6 | Dipakai Filament |
| Inertia (server) | 2.0.28 | `inertiajs/inertia-laravel` |
| Inertia (client) | 2.3.28 | `@inertiajs/react` |
| React | **18.3.1** | `package.json` `^18.2.0`; `react-is` 19.3.0 (campur mayor) |
| Vite | **8.3.1** | Node 22.21.1, npm 10.9.4 |
| Tailwind CSS | **3.4.19** + `@tailwindcss/forms` 0.5.11 | `@tailwindcss/vite` 4.3.3 ikut terpasang tetapi **tidak dipakai** di `vite.config.js` |
| Database | MySQL | `database_engine: mysql`; test pakai SQLite in-memory |
| Test | Pest 5.2.1 / PHPUnit 13.3.4 | |
| Node | 22.21.1 | Vite 8 butuh Node ≥ 20.19 / 22.12 |

**Package penting (composer.json require)**
`astrotomic/laravel-translatable` (11.17.1) · `mcamara/laravel-localization` (2.4.2) · `tightenco/ziggy` (2.6.4) · `league/flysystem-aws-s3-v3` · `laravel/sanctum` (tidak dipakai untuk API) · `laravel/tinker`.
Export Filament memakai `league/csv` 9.28.0 + `openspout` 4.32.0.

**Package penting (package.json)**
`@headlessui/react` 2.2.10 · `lucide-react` 1.48.0 · `recharts` 3.10.1 (grafik kualitas air) · `framer-motion` 13.4.4 · `clsx` · `tailwind-merge` · `@heroicons/react`.
Script hanya `dev` dan `build` — **tidak ada** script lint/format/test frontend, tidak ada TypeScript (bertentangan dengan PRD).

---

## 3. Cara Menjalankan

```bash
composer install
copy .env.example .env        # Windows; Linux: cp
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev                   # atau: npm run build
php artisan serve
```

- Admin: `http://127.0.0.1:8000/admin` (Filament login).
- Publik: `http://127.0.0.1:8000/`.
- Seeder membuat **1 user admin**. Kredensial default ada di `database/seeders/DatabaseSeeder.php` — **jangan disalin ke dokumen/chat; ganti sebelum produksi.**

**Variabel `.env` yang dibutuhkan (nama saja)**
`APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, `APP_LOCALE` (set `id`), `APP_FALLBACK_LOCALE`,
`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`,
`SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`, `FILESYSTEM_DISK`,
`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`,
`VITE_APP_NAME`.
Tidak ada key khusus aplikasi (mis. R2/Fonnte) di `.env.example` — PRD menyebut Fonnte & Cloudflare R2, **tidak diimplementasikan**.

**Masalah lingkungan yang diketahui**
| Masalah | Status | Keterangan |
|---|---|---|
| PHP 8.4 vs 8.5 | TERVERIFIKASI | `AGENTS.md` menulis PHP 8.5, runtime 8.4.26, composer `^8.3`. Kode berjalan di 8.4 |
| Ekstensi PHP | TERVERIFIKASI | `intl`, `gd`, `zip`, `mbstring`, `pdo_mysql`, `openssl`, `fileinfo`, `curl` tersedia lokal |
| `composer run setup` memakai `npm install --ignore-scripts` | TIDAK YAKIN | Berisiko melewati native binding Vite 8 (`rolldown`, `@tailwindcss/oxide`) di Windows; jika build gagal, jalankan `npm install` tanpa flag |
| Vite 8 | TERVERIFIKASI | `node_modules/vite` = 8.3.1; Node 22.21.1 memenuhi syarat |
| Manifest build | TERVERIFIKASI | `public/build/manifest.json` dan `public/storage` sudah ada |
| `public/build` di OneDrive | TIDAK YAKIN | Repo berada di folder OneDrive; file lock/sinkronisasi bisa mengganggu `npm run dev` |
| Mail di lokal | TERVERIFIKASI | `QUEUE_CONNECTION=sync`; email dikirim sinkron. Tanpa SMTP valid, submit form akan error |

---

## 4. Struktur Folder

| Path | Fungsi |
|---|---|
| `app/Models/` | 18 model Eloquent (lihat §5) |
| `app/Http/Controllers/WebController.php` | Semua halaman publik + submit form relawan/kontak |
| `app/Http/Controllers/LocaleController.php` | Switch bahasa (session) |
| `app/Http/Controllers/Auth/` | Breeze (register/login/reset). Tidak dipakai publik |
| `app/Http/Controllers/Api/` | 6 controller kosong, **tanpa route** (dead code) |
| `app/Http/Middleware/SetLocale.php` | Set locale dari session (`id`/`en`) |
| `app/Http/Middleware/HandleInertiaRequests.php` | Share `auth`, `locale`, `translations` |
| `app/Filament/Resources/<Model>/` | Pola v5: `XResource.php`, `Pages/`, `Schemas/XForm.php`, `Tables/XTable.php` |
| `app/Filament/Pages/ManageSiteSettings.php` | Halaman pengaturan situs (singleton) |
| `app/Filament/Exports/` | `MemberExporter`, `WaterQualityRecordExporter` |
| `app/Providers/Filament/AdminPanelProvider.php` | Panel `admin`, warna Amber |
| `app/Mail/` | `VolunteerRegistrationMail`, `ContactFormMail` |
| `resources/js/Pages/` | 13 halaman publik + Auth/ + Profile/ |
| `resources/js/Components/Public/` | `Navigation`, `Footer`, `AboutSnippet` |
| `resources/js/Layouts/` | `PublicLayout` (dipakai publik), `AuthenticatedLayout`, `GuestLayout` (Breeze) |
| `resources/js/app.jsx` | Entry Inertia (`import.meta.glob('./Pages/**/*.jsx')`) |
| `routes/web.php`, `routes/auth.php` | Routing publik + auth |
| `lang/id/site.php`, `lang/en/site.php` | 33 key terjemahan UI |
| `resources/views/filament/pages/` | Blade `ManageSiteSettings` |
| `resources/views/emails/` | Template markdown email |
| `public/image/`, `public/storage/` | Aset statis (`/image/...`) dan upload (`/storage/...`) |
| `docs/` | Dokumen ini (sebelumnya tidak ada) |

---

## 5. Database

Sumber: `database/migrations/` + schema MySQL aktual (71 baris `water_quality_records` di DB lokal).

**Tabel domain**

| Tabel | Kolom penting | Relasi / catatan |
|---|---|---|
| `hero_slides` | `image_url`, `cta_url`, `order`, `is_active` | Teks pindah ke `hero_slide_translations` (migrasi menghapus `title/subtitle/cta_label`) |
| `hero_slide_translations` | `hero_slide_id`, `locale`, `title`, `subtitle`, `cta_label` | unique(`hero_slide_id`,`locale`); **translatable (baris per locale, bukan JSON)** |
| `running_texts` | `text`, `order`, `is_active` | Marquee beranda |
| `founders` | `name`, `role`, `org_name`, `photo_url`, `order` | Satu bahasa |
| `org_categories` | `name`, `description`, `display_type` enum `ROLE_LIST`/`PERSON_GRID`, `order` | `hasMany` org_people |
| `org_people` | `org_category_id`, `name` (nullable), `role_title`, `photo_url`, `divisi_name`, `order` | FK cascade |
| `members` | `name`, `photo_url`, `divisi_name`, `points` (index), `message`, `joined_at`, `is_featured` | Scope `topByPoints()` |
| `volunteers` | `registration_code` (unique), `full_name`, `email`, `phone`, `address`, `motivation`, `status` enum `PENDING/APPROVED/REJECTED`, `photo_url`, `submitted_at`, `reviewed_at`, `reviewed_by`, `notified_at` | `belongsTo` users; kode otomatis `TMCC-XXXXXX` |
| `programs` | `slug` (unique), `name`, `description`, `icon_url`, `order` | `hasMany` activities |
| `activities` | `program_id`, `title`, `slug` (unique), `cover_image_url`, `content` longtext, `youtube_url`, `instagram_url`, `published_at`, `is_published` | FK cascade |
| `blog_posts` | `title`, `slug` (unique), `category` (string), `cover_image_url`, `content` longtext nullable, `pdf_url`, `published_at`, `is_published` | Kategori: `EDUKASI`, `RILIS_PERS`, `BERITA`, `KEBIJAKAN`, `BASELINE_STUDY` |
| `gallery_items` | `type` enum `PHOTO/VIDEO`, `url`, `caption` | `url` = path file atau URL video |
| `water_sampling_points` | `name`, `description`, `coordinates` (string), `is_active` | `hasMany` records |
| `water_quality_records` | `recorded_at`, `tss`, `ph`, `tds`, `dissolved_oxygen`, `temperature`, `clarity_cm` (semua double; `tss`/`tds`/`dissolved_oxygen`/`temperature` nullable), `notes`, `recorded_by`, `water_sampling_point_id` | FK users + sampling point (nullOnDelete). Ambang batas terpusat di `config/water_quality.php` |
| `donation_channels` | `bank_name`, `account_no`, `account_name`, `is_active` | Satu bahasa |
| `partnerships` | `name`, `logo_url`, `website_url`, `description`, `type` (`partner`/`sponsor`/`media`), `is_active`, `order` | |
| `site_settings` | `fixed_text` **JSON** `{id,en}`, `address`, `phone`, `whatsapp`, `email`, 4 sosmed, 4 URL gambar (`about_hero_image_url`, `about_lake_image_url`, `org_hero_image_url`, `org_chart_image_url`), `stat_sampah_terangkut`, `stat_sekolah_teredukasi` | Singleton (`SiteSettings::first()`), 1 baris |
| `users` | `name`, `email`, `password` | Admin Filament + Breeze |

Tabel framework/Filament: `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens`, `notifications`, `imports`, `exports`, `failed_import_rows`.
`imports`/`exports`/`failed_import_rows` ada (dibuat untuk fitur Filament) tetapi **fitur import tidak dipakai**.

**Multi-bahasa di DB**
- Translatable: `hero_slide_translations` (Astrotomic, `id` + `en`).
- JSON: `site_settings.fixed_text` (`{id, en}`).
- **Masih satu bahasa:** `programs`, `activities`, `blog_posts`, `founders`, `org_categories`, `org_people`, `members`, `partnerships`, `gallery_items`, `water_sampling_points`, `running_texts`, `donation_channels`.

---

## 6. Admin Panel (Filament)

Panel: `/admin`, guard default, login Filament, primary `Color::Amber`, dashboard default. **Tidak ada widget kustom** (`app/Filament/Widgets/` tidak ada). Resource ditemukan otomatis.

| Resource (model) | Yang dikelola | Fitur khusus |
|---|---|---|
| Activities (Activity) | Kegiatan per program | upload cover (`public/activities`), RichEditor, auto-slug, toggle publish |
| BlogPosts (BlogPost) — label "Tova Blog" | Artikel | badge kategori, upload cover (`blog-posts`) + PDF (`blog-pdfs`), RichEditor, auto-slug |
| DonationChannels | Rekening donasi | toggle aktif |
| Founders | Pendiri | **reorderable `order`**, upload foto |
| GalleryItems | Galeri foto/video | form kondisional PHOTO/VIDEO, badge tipe |
| HeroSlides | Slider beranda | **reorderable**, upload gambar (`hero-slides`), tab terjemahan ID/EN |
| Members | Anggota & poin | badge poin, filter/toggle featured, **export (RUSAK, §10)** |
| OrgCategories | Kategori struktur | **reorderable**, badge `display_type` |
| OrgPeople | Personel struktur | **reorderable**, relasi kategori, upload foto |
| Partnerships | Mitra | **reorderable**, badge aktif, select tipe |
| Programs | Program | **reorderable**, auto-slug, upload ikon |
| RunningTexts | Teks berjalan | **reorderable**, toggle aktif |
| Volunteers | Pendaftar relawan | badge status berwarna (`PENDING` warning, `APPROVED` success, `REJECTED` danger) |
| WaterQualityRecords | Catatan kualitas air | **satu-satunya resource dengan filter nyata**: select titik + rentang tanggal, layout filter di atas tabel, **export (RUSAK)** |
| WaterSamplingPoints | Titik sampling | toggle aktif |

**Page kustom:** `ManageSiteSettings` (`/admin/manage-site-settings`) — form: teks tetap hero (ID/EN), statistik beranda, 4 gambar header, kontak & sosmed. Menggunakan `SiteSettings::firstOrCreate([])` dan Blade `filament.pages.manage-site-settings`.

Semua resource punya 3 page standar (List/Create/Edit). Hapus massal lewat `DeleteBulkAction`; hapus satuan di halaman Edit.

---

## 7. Halaman Publik

| URL | Controller@method | Komponen React | Data props utama | Status |
|---|---|---|---|---|
| `/` | `WebController@home` | `Home` | `slides`, `runningTexts`, `topMembers` (3), `programs` (4), `settings`, `fixedText`, `heroFixedText`, `homeStats` | BELUM DIUJI (kode lengkap) |
| `/tentang` | `WebController@about` | `About` | `founders`, `settings` (+shared `translations`) | BELUM DIUJI (kode lengkap) |
| `/struktur-organisasi` | `WebController@organization` | `Organization` | `categories` (+`people`), `settings` | BELUM DIUJI (kode lengkap) |
| `/anggota` | `WebController@members` | `Members` | `members` (points desc), `settings` | BELUM DIUJI (kode lengkap) |
| `/partnership` | `WebController@partnerships` | `Partnerships` | `partnerships` (aktif, urut), `settings` | BELUM DIUJI (kode lengkap) |
| `/program` | `WebController@programs` | `Programs` | `programs` (+3 aktivitas terbaru), `settings` | BELUM DIUJI (kode lengkap) |
| `/program/{slug}` | `WebController@programCategory` | `ProgramCategory` | `program`, `activities` (paginate 12), `settings` | BELUM DIUJI (kode lengkap) |
| `/program/{program}/{activity}` | `WebController@programDetail` | `ProgramDetail` | `program`, `activity`, `settings` | BELUM DIUJI (kode lengkap) |
| `/blog` | `WebController@blog` | `Blog` | `posts` (paginate 9), `filters.category`, `baselineStudyPosts`, `settings` | BELUM DIUJI; filter kategori via query string |
| `/blog/{slug}` | `WebController@blogDetail` | `BlogDetail` | `post`, `settings` | BELUM DIUJI |
| `/galeri` | `WebController@gallery` | `Gallery` | `items` (paginate 12), `settings` | **RUSAK saat paginasi** (`router` tidak di-import, §10) |
| `/kualitas-air` | `WebController@waterQuality` | `WaterQuality` | `records` (dataset 6 chart, semua record terfilter), `paginatedRecords`, `summary`, `waterQualityConfig`, `filters`, `perPageOptions`, `samplingPoints`, `settings` | SELESAI (kode) — 6 chart + KPI + paginasi server-side; visual TIDAK DIVERIFIKASI |
| `/gabung-donasi` | `WebController@joinAndDonate` | `JoinDonate` | `donationChannels`, `settings` | BELUM DIUJI; sukses baca `flash.registration_code` |
| `/kontak` | `WebController@contact` | `Contact` | `settings` | BELUM DIUJI; sukses pakai state lokal, bukan flash |
| `POST /gabung-donasi/daftar` | `WebController@submitVolunteer` | — | validasi 5 field, buat Volunteer + kirim email | BELUM DIUJI (butuh SMTP) |
| `POST /kontak/kirim` | `WebController@submitContact` | — | validasi 3 field, kirim email ke `MAIL_FROM_ADDRESS` | BELUM DIUJI (butuh SMTP) |
| `POST /locale` | `LocaleController@switch` | — | `locale` (`id`/`en`), simpan di session | TERVERIFIKASI (kode) |
| `/login`, `/register`, `/profile` | Breeze | `Auth/*`, `Profile/Edit` | standar Breeze | **RUSAK** (`route('dashboard')` tidak ada) |

`Welcome.jsx` dan `Dashboard.jsx` adalah sisa scaffolding Breeze, **tidak punya route**.
Semua path gambar upload dirender sebagai `/storage/<path>`; aset statis dari `/image/...`.

---

## 8. Fitur Multi-Bahasa

- **Mekanisme:** manual, berbasis **session**, bukan URL prefix.
  - `SetLocale` (append middleware `web`) membaca `session('locale')`, fallback `config('app.locale')`, whitelist `id`/`en`.
  - `POST /locale` (`locale.switch`) menyimpan pilihan; `Navigation.jsx` mengirim form ke route ini.
  - `HandleInertiaRequests` membagikan `locale` + `translations` = `trans('site')` ke semua halaman.
- **File terjemahan:** `lang/id/site.php`, `lang/en/site.php` — 33 key datar (nav, about, blog, water, umum).
- **Konten:** `HeroSlide` memakai `astrotomic/laravel-translatable` (tab ID/EN di Filament). `SiteSettings.fixed_text` JSON ID/EN.
- `mcamara/laravel-localization` terpasang + `config/laravellocalization.php` (`id`, `en`), tetapi **tidak dipakai untuk routing ber-prefix** (`/en/...` tidak ada).
- **Belum diterjemahkan:** hampir seluruh isi halaman publik — `Home` (judul section, "poin", "Portal Keanggotaan"), `Organization` (seluruh bagan & label), `Members`, `Partnerships`, `Programs`, `ProgramCategory`, `ProgramDetail`, `Gallery`, `WaterQuality`, `Contact`, `JoinDonate`, `Footer`, dan semua `<Head title>` (contoh `"Beranda"`). Hanya `Navigation`, `About`, dan `Blog` yang memakai `t.*`.
- Konten DB selain hero slide hanya satu bahasa (Indonesia).

---

## 9. Status Pekerjaan vs PRD

`PRD_Tova_Matano_Care_Center.md` (v1.0, status "Draft — siap development") **masih menandai semua checkbox `[ ]` kosong** di §6 dan §11. Dokumen PRD **tidak mencerminkan kondisi kode**. PRD juga menyebut multi-language sebagai out-of-scope, padahal sudah diimplementasikan sebagian; PRD menyebut TypeScript, padahal proyek JSX.

| Fitur | PRD | Status nyata | Catatan |
|---|---|---|---|
| Beranda: hero slider | §6.1 | **SELESAI (kode)** | Autoplay 8 dtk, prev/next, dots; fallback bila tidak ada slide |
| Revisi: hero tanpa overlay gelap | — | **SELESAI (kode)** | Tidak ada layer overlay; keterbacaan memakai `text-shadow`. BELUM DIUJI visual |
| Revisi: teks tetap hero | — | **SELESAI (kode)** | `site_settings.fixed_text` JSON ID/EN; dirender di luar loop slide (`Home.jsx`), fallback ke `id` |
| Running text | §6.1 | SELESAI | Marquee CSS, teks diulang 3×, pause saat hover |
| Portal keanggotaan (top 3) | §6.1 | SELESAI | Podium rank 1–3; **belum ada QR/registrasi anggota** seperti PRD |
| Tentang TMCC | §6.2 | SEBAGIAN | Founders dari DB; profil/visi/misi **hardcoded** di React, tidak bisa diedit admin |
| Struktur organisasi | §6.3 | SELESAI (kode) | **Bagan struktur statis** (hardcoded) + akordeon kategori dari DB (`ROLE_LIST`/`PERSON_GRID`). Gambar bagan bisa diunggah (`org_chart_image_url`) tetapi TIDAK YAKIN apakah ditampilkan |
| Anggota + poin | §6.4 | SELESAI | Urut poin desc; **reset poin per anggota belum ada** (hanya edit manual) |
| Program (4) + kegiatan | §6.5 | SELESAI | 4 program seed; detail + embed YouTube/Instagram |
| Grafik kualitas air | §6.5 | SELESAI (kode) | 6 chart terpisah (TSS, pH, TDS, DO, Suhu, Kejernihan), KPI rata-rata, ambang terpusat di `config/water_quality.php`; ambang PP 22/2021 belum diverifikasi resmi |
| Blog + kategori | §6.6 | SELESAI + tambahan | 5 kategori termasuk `BASELINE_STUDY`, lampiran PDF |
| Galeri & media | §6.7 | SELESAI (kode) | Grid + lightbox; bug paginasi |
| Relawan (form + kode) | §6.8 | SEBAGIAN | Form + kode + email ada; **approval/notifikasi WA otomatis belum**; status dicek manual lewat admin |
| Donasi | §6.8 | SELESAI (info rekening) | Tanpa laporan dana (sesuai PRD) |
| Kontak | §6.9 | SELESAI | Form → email admin |
| Dashboard admin | §6.10 | SELESAI | Filament; **tanpa widget ringkasan** |
| Partnership | (tidak di PRD) | SELESAI | Resource + halaman publik `/partnership` |
| Kualitas Air (sampling point) | (tidak di PRD) | SELESAI | Tabel + resource + halaman publik |
| Tova Blog (rebrand) | (tidak di PRD) | SELESAI (kode) | Grup "Konten Website", label "Tova Blog" |
| Export Excel | (tidak di PRD) | SEBAGIAN | Export WaterQualityRecords sudah diperbaiki & diuji render; export Members masih memakai namespace Filament lama |
| Multi-bahasa ID/EN | out-of-scope PRD | SEBAGIAN | Lihat §8 |
| Pencarian full-text | §4, Fase 5 | **BELUM** | Tidak ada search publik |
| SEO (meta dinamis, sitemap, OG) | §10 | **BELUM** | Hanya `<Head title>` |
| Notifikasi WhatsApp (Fonnte) | §9 | **BELUM** | Tidak ada kode/integrasi |
| Upload ke R2/S3 | §9 | **BELUM** | Upload lokal `public` |
| API publik | — | BELUM | Controller API kosong, tanpa route |

---

## 10. Masalah & Utang Teknis

**Bug terverifikasi**

| # | Masalah | Lokasi | Dampak |
|---|---|---|---|
| 1 | `route('dashboard')` tidak ada (tidak ada route `dashboard`) | `AuthenticatedLayout.jsx:28,66,132`, `ConfirmablePasswordController.php:39`, controller auth lain, `Welcome.jsx` | **4 test gagal/error**; halaman `/profile` & alur auth 500 |
| 2 | `Filament\Tables\Actions\ExportAction` tidak ada di v5 | `MembersTable.php` (sudah diperbaiki di `WaterQualityRecordsTable.php`) | Halaman daftar Members **fatal**; export Members belum bisa dipakai. Perbaikan: `Filament\Actions\ExportAction` |
| 3 | `Filament\Forms\Get` tidak ada di v5 | `GalleryItemForm.php:9` | Form galeri **fatal**. Perbaikan: `Filament\Schemas\Components\Utilities\Get` |
| 4 | `router.visit()` tanpa import `router` | `Gallery.jsx:72` | Klik paginasi → `ReferenceError` |
| 5 | Filter status relawan memakai `'ACCEPTED'`, padahal enum `APPROVED` | `WebController@home` (`homeStats.relawan_aktif`) | Statistik relawan aktif salah/0 |
| 6 | `$settings->stat_sampah_terangkut` tanpa null-check | `WebController@home` | Jika tabel `site_settings` kosong → 500 |
| 7 | Upload partnership tanpa `->disk('public')` | `PartnershipForm` | File masuk disk default (`local`, privat) sehingga `/storage/...` tidak ditemukan |
| 8 | Tombol tutup lightbox `absolute` tanpa parent `relative` | `Gallery.jsx` | Posisi tombol salah |
| 9 | Kontak mengabaikan `flash.success` | `Contact.jsx` | Status sukses hanya lokal, hilang saat reload |
| 10 | `DB::table` tanpa import facade | `2026_10_01_072315_create_hero_slides_translations_table.php` | Berjalan karena alias global; rapuh |
| 11 | Dua prop sama (`fixedText` + `heroFixedText`) | `WebController@home` | Redundan |

**Hasil test (TERVERIFIKASI, `php artisan test --compact`)**
40 test, 36 lulus, 1 gagal + 3 error — keempatnya karena `Route [dashboard] not defined` (pre-existing). Test kualitas air: 12 test di `tests/Feature/WaterQualityDashboardTest.php` (dashboard, paginasi, filter, KPI, admin create/update/validasi).

**Utang teknis lain**
- PRD usang/tidak sinkron dengan implementasi (§9).
- README masih README skeleton Laravel; tidak ada dokumentasi proyek sebelum dokumen ini.
- `database/factories/` hanya `UserFactory` → butuh factory domain sebelum menulis feature test.
- Controller `app/Http/Controllers/Api/*` kosong dan tidak terdaftar.
- Tidak ada rate limiting pada form publik; tidak ada sanitasi eksplisit (konten RichEditor dirender via `dangerouslySetInnerHTML`).
- Tidak ada `sitemap.xml`, meta OG, canonical.
- Seeder membuat user admin dengan password default (ganti di produksi); sebagian seeder tidak idempoten (`WaterQuality`, `BlogPosts`, dll. membuat ulang).
- `GalleryItem` record name di Filament memakai `title` yang **tidak ada** di kolom tabel (record title bisa kosong).
- `resources/js/Components/Public/Navigation.jsx` dan `Footer.jsx` hardcode teks Indonesia.
- `package.json` mencampur React 18 dengan `react-is` 19; Tailwind 3 dengan plugin v4 terpasang.
- `AGENTS.md` menyebut PHP 8.5 (runtime 8.4).

---

## 11. Konvensi Kode

**PHP / Laravel**
- Controller publik ada di satu `WebController`; tiap method mengembalikan `Inertia::render('Nama', [...])`.
- Model memakai `$fillable` (bukan `$guarded`); cast tanggal/boolean eksplisit; relasi diberi return type (`BelongsTo`, `HasMany`).
- Format uang/angka tidak diformat di backend kecuali `homeStats`.
- Nama method/relasi deskriptif (`samplingPoint`, `topByPoints`, `isApproved`).
- Komentar kode berbahasa Indonesia dan minim.
- Jalankan `vendor/bin/pint --dirty --format agent` setelah mengubah PHP.

**Filament v5 (WAJIB diikuti)**
- Satu folder per resource: `app/Filament/Resources/<Plural>/` berisi `<Singular>Resource.php`, `Pages/List|Create|Edit`, `Schemas/<Singular>Form.php`, `Tables/<Singular>Table.php`.
- Import aksi dari `Filament\Actions\*` (bukan `Filament\Tables\Actions\*`); utility `Get` dari `Filament\Schemas\Components\Utilities\Get`.
- Table: `->recordActions([EditAction::make()])`, `->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])`.
- Urutan manual: `->defaultSort('order')` + `->reorderable('order')` (kolom `order` ada di tabel).
- Upload: `FileUpload::make(...)->image()->disk('public')->directory('<nama>')`; simpan **path relatif**, render `/storage/<path>`.
- Badge status: `TextColumn::make('status')->badge()->color(fn ($state) => match (...))`.
- Teks UI admin berbahasa Indonesia (`->label('...')`).

**Menambah halaman publik baru**
1. `Route::get('/slug', [WebController::class, 'method'])->name('...')` di `routes/web.php`.
2. Method di `WebController` yang mengirim `settings` + data yang dibutuhkan.
3. Buat `resources/js/Pages/Nama.jsx`, bungkus `<PublicLayout settings={settings}>`, `<Head title="..." />`.
4. Gunakan token warna `brand-*` dan font `font-heading`; jangan buat skema warna baru.
5. Tambahkan menu di `Components/Public/Navigation.jsx` + key `nav_*` di kedua file `lang/*/site.php`.
6. Jika perlu dikelola admin: buat resource Filament + model + migrasi (via `php artisan make:*`).

**Design tokens** (`tailwind.config.js`)
| Token | Nilai |
|---|---|
| `brand-navy` | `#123A52` |
| `brand-teal` | `#1E7F8C` |
| `brand-gold` | `#E3A23A` |
| `brand-paper` | `#F6F3EA` |
| `font-sans` | Work Sans (Google/Bunny Fonts) |
| `font-heading` | Fraunces |
| Animasi | `animate-marquee` (80s linear infinite) |

Path alias `@` → `resources/js` (bawaan `jsconfig.json`/Vite). Ikon memakai `lucide-react`; grafik `recharts`.

---

## 12. Langkah Berikutnya (prioritas)

| # | Pekerjaan | Alasan |
|---|---|---|
| 1 | Perbaiki namespace `Get` di `GalleryItemForm` dan `ExportAction` di `MembersTable` | Form galeri & daftar Members masih fatal (WaterQualityRecords sudah diperbaiki) |
| 2 | Tambah route `dashboard` (atau arahkan redirect Breeze ke `/admin`) | 4 test gagal, `/profile` 500 |
| 3 | Import `router` di `Gallery.jsx`; rapikan lightbox | Bug paginasi galeri |
| 4 | Perbaiki `homeStats`: status `APPROVED`, null-safe `$settings` | Statistik beranda salah / potensi 500 |
| 5 | `->disk('public')` pada upload partnership; verifikasi semua path gambar | Logo mitra tidak tampil |
| 6 | Tambah test fitur: halaman publik (200 + props), submit relawan/kontak (Mail::fake), resource Filament utama | Regresi saat ini tidak tertangkap |
| 7 | Instance factory untuk model domain + seed idempoten | Prasyarat test & deploy berulang |
| 8 | Lengkapi alur relawan: approval di admin → email notifikasi (+ WA/Fonnte bila disetujui) | PRD §7.1 belum utuh |
| 9 | Terjemahkan sisa halaman publik + `Footer` + `<Head title>`; pindahkan teks hardcoded ke `lang/*/site.php` | Multi-bahasa baru sebagian |
| 10 | SEO: meta dinamis per halaman, OG image, `sitemap.xml`, canonical | PRD §10 |
| 11 | Rate limit form publik + sanitasi HTML konten | Keamanan |
| 12 | Tambah widget dashboard (statistik relawan, poin, kualitas air) | Dashboard masih default |
| 13 | Bersihkan dead code: `Api/*`, `Welcome.jsx`, `Dashboard.jsx`; perbarui README/PRD | Kebersihan repo |
| 14 | Ganti password admin default; siapkan `.env` produksi (SMTP, `APP_URL`, `FILESYSTEM_DISK`) | Go-live |
| 15 | Verifikasi visual seluruh halaman di mobile (375) & desktop (1280) | Belum pernah diuji UI |
