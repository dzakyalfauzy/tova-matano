# PHASE 3B-2 — SITESETTINGS MODEL IMPLEMENTATION REPORT

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Laporan Implementasi Model `SiteSettings` (Phase 3B-2)  
**Tanggal:** 11 November 2026  
**Status:** SELESAI  
**Lingkup:** Hanya `app/Models/SiteSettings.php` + focused tests. Tidak menyentuh migrasi/Filament/controller/frontend/lang/Phase 1-2.

---

## 1. EXACT MODEL CHANGES

File: `app/Models/SiteSettings.php`

### 1.1 Fillable (aditif — field lama tidak diubah)
```php
protected $fillable = [
    'fixed_text',
    'about_content', 'about_lake_image_credit', 'about_legal_verified', // BARU
    'address', 'phone', 'whatsapp', 'email',
    'instagram_url', 'facebook_url', 'tiktok_url', 'twitter_url',
    'about_hero_image_url', 'about_lake_image_url', 'org_hero_image_url', 'org_chart_image_url',
    'stat_sampah_terangkut', 'stat_sekolah_teredukasi',
];
```

### 1.2 Casts (aditif — cast lama `fixed_text` dipertahankan)
```php
protected $casts = [
    'fixed_text' => 'array',
    'about_content' => 'array',          // BARU
    'about_legal_verified' => 'boolean', // BARU
];
```

### 1.3 Helper baru
- `public function getAboutContent(?string $locale = null): array`
- `private function resolveAboutField(mixed $stored, string $fallback, string $locale): string`
- `private function defaultAboutContent(): array`

Struktur keluaran `getAboutContent()`:
```
[
  'hero'      => ['eyebrow','title','subtitle'],
  'lake'      => ['title','body'],
  'profile'   => ['title','body'],
  'potential' => ['title','body'],
  'legal'     => ['title','items','verified'],
  'vision'    => string,
  'mission'   => string,  // item dipisah "\n"
  'goal'      => string,  // item dipisah "\n"
  'founders'  => ['title','narrative'],
]
```
- Paragraf ganda disimpan/di-resolve sebagai string dengan pemisah `\n\n`.
- `legal.image_credit` **tidak** termasuk di sini (kolom terpisah `about_lake_image_credit`).

---

## 2. DEFAULT CONTENT BEHAVIOR

- Bila `about_content` NULL/kosong/tidak lengkap → field diisi **default verbatim klien** dari `docs/reports/PHASE_3_1_CLIENT_CONTENT_EXTRACTION.md` (tidak diparafrase).
- Default disimpan di method privat `defaultAboutContent()` (Bahasa Indonesia), mis.:
  - Hero: `Tentang Kami` / `ADVOKASI LINGKUNGAN LOKAL` / `Membangun masa depan yang berkelanjutan untuk Danau Matano`
  - `Sekilas Danau Matano`, `Profil & Sejarah`, `Potensi TMCC`, `Legalitas`
  - Visi (1), Misi (4 baris), Tujuan (4 baris)
  - `DEWAN PENDIRI` + narasi lengkap
- Default **tidak** menimpa data tersimpan; hanya dipakai saat field kosong.
- **Tidak ada penerjemahan otomatis**: `en` dibiarkan kosong di default; fallback EN→ID.

---

## 3. LOCALIZATION FALLBACK BEHAVIOR

`resolveAboutField()` menerapkan urutan:
1. Nilai `stored[locale]` (mis. `en`) bila non-kosong.
2. Bila kosong dan locale ≠ `id` → `stored['id']`.
3. Bila tetap kosong → **fallback default (ID)**.
4. Struktur `{id, en}` diresolusi menjadi **string** tunggal; struktur seksi dipertahankan.

- `getAboutContent('en')` dengan `en` kosong → mengembalikan nilai `id`.
- `getAboutContent()` tanpa argumen → memakai `app()->getLocale()` (dibuktikan via Feature test).
- Membaca tidak mengubah/menimpa `about_content` tersimpan (dibuktikan test).

---

## 4. LEGAL DATA HANDLING

- Default `legal.items` = **string kosong** (`''`). **Tidak ada** nomor legal yang difabrikasi dan **tidak ada** placeholder lama (AHU-0012345, NPWP 12.345.678.9, Kesbangpol) yang dimasukkan.
- `legal.verified` = `$this->about_legal_verified === true` → **hanya** bernilai `true` bila boolean tersimpan benar-benar `true`. Nilai `0`/`false`/null → `false`.
- Data legal tersimpan tetap tersedia apa adanya untuk keperluan admin (helper membaca tanpa menambah/mengubah).
- **Gating publik** tidak dilakukan di model — akan diimplementasikan pada Phase 3B-4 (controller mengosongkan `legal.items` bila belum verified). Model menyediakan flag `verified` agar controller dapat melakukan gating dengan aman.

---

## 5. TESTS AND RESULTS

### 5.1 File tes
- **Diperbarui:** `tests/Unit/SiteSettingsTest.php` (3 tes lama tetap; +9 tes baru untuk About).
- **Baru:** `tests/Feature/SiteSettingsAboutContentTest.php` (1 tes locale aplikasi).

### 5.2 Cakupan (sesuai requirement)
| Kasus | Tes |
|---|---|
| `about_content` NULL | `it returns approved default about content when about_content is null` |
| Nested JSON valid (ID) | `it resolves nested about content for the indonesian locale` |
| Locale EN | `it resolves nested about content for the english locale` |
| EN kosong → ID | `it falls back to indonesian when english about content is empty` |
| Struktur nested tidak lengkap / tipe salah | `it handles incomplete nested about structures safely` |
| Legal tidak difabrikasi | `it does not fabricate legal data in defaults` |
| `about_legal_verified` casting & gating flag | `it only marks legal verified when the stored boolean is true` + `it casts about_legal_verified to boolean` |
| Tidak memutasi data | `reading about content does not mutate the stored value` |
| Locale aplikasi (null arg) | Feature: `getAboutContent uses the current application locale when none is provided` |

### 5.3 Hasil
| Perintah | Hasil |
|---|---|
| `php -l` (model + 2 file tes) | ✅ No syntax errors |
| `php artisan test tests/Unit/SiteSettingsTest.php tests/Feature/SiteSettingsAboutContentTest.php` | ✅ **13 passed, 39 assertions** |
| `php artisan test --compact` (full suite) | ✅ **78 passed, 434 assertions** (sebelumnya 68) |
| `vendor/bin/pint` (file yang dimodifikasi) | ✅ applied (`new_with_parentheses` pada 2 file tes) |

> 10 tes baru (. 9 unit + 1 feature). Suite naik dari 68 → 78 tes, semuanya lulus.

---

## 6. GIT WORKING-TREE CHANGES

Perubahan dari Phase 3B-2 (sesuai scope):
```
 M app/Models/SiteSettings.php
 M tests/Unit/SiteSettingsTest.php
?? tests/Feature/SiteSettingsAboutContentTest.php
?? docs/reports/PHASE_3B_2_MODEL_REPORT.md
```
Dipertahankan (tidak disentuh oleh Phase 3B-2):
```
 M app/Http/Controllers/WebController.php          (Phase 2B — pre-existing)
 M resources/js/Components/Public/AboutSnippet.jsx  (Phase 2B — pre-existing)
 M resources/js/Pages/Home.jsx                      (Phase 2B — pre-existing)
?? database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php  (Phase 3B-1)
?? docs/reports/*                                   (laporan sebelumnya)
?? "docs/revisi web.docx"                           (dokumen klien)
```
**Tidak ada** perubahan pada: migrasi, `ManageSiteSettings.php`, `WebController.php` (Phase 3B), `About.jsx`, `lang/*`, atau file Phase 1/2.

---

## 7. UNRESOLVED ISSUES / NOTES

| # | Item | Catatan |
|---|---|---|
| N1 | Gating legal publik | Belum diimplementasi di model (sesuai instruksi); akan ditangani Phase 3B-4. Model hanya menyediakan `legal.verified`. |
| N2 | `about_lake_image_credit` | Ditambahkan ke fillable/cast? Kolom string; **tidak** perlu cast; helper tidak menyertakannya (dibaca langsung dari `settings`). |
| N3 | Default EN kosong | Sesuai keputusan: EN fallback ke ID sampai terjemahan tersedia. |
| N4 | Filament belum menulis nested JSON | Akan memakai pola `fixed_text` (Phase 3B-3). |
| N5 | Migrasi produksi belum dijalankan | Kolom sudah ada di DB lokal; produksi menunggu backup + deploy. |

---

## 8. KEPATUHAN SCOPE

- ✅ Hanya `SiteSettings.php` + focused tests + laporan.
- ✅ Fillable/casts aditif; tidak menghapus/mengubah field lama.
- ✅ Tidak menjalankan migrasi/seeder; tidak mengubah record DB.
- ✅ Tidak menyentuh `.env`, produksi, Filament, controller, `About.jsx`, `lang`, Phase 1/2.
- ✅ Tidak commit/push/deploy.

**STOP — Phase 3B-2 selesai.** Menunggu review & persetujuan Project Owner sebelum Phase 3B-3 (Filament Admin).
