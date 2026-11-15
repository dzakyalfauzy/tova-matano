# PHASE 3B-1 — ABOUT CONTENT DATABASE MIGRATION REPORT

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Laporan Implementasi Migrasi Phase 3B-1  
**Tanggal:** 11 November 2026  
**Status:** SELESAI — migrasi dijalankan di DB LOKAL  
**Lingkup:** Hanya migrasi aditif (Phase 3B-1). Tidak ada perubahan model/Filament/controller/frontend.

---

## 1. MIGRATION FILENAME
```
database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php
```
Dibuat dengan `php artisan make:migration add_about_content_to_site_settings_table --table=site_settings --no-interaction`. **Satu file migrasi baru**; tidak ada migrasi existing yang diubah.

---

## 2. EXACT SCHEMA CHANGES

`up()`:
```php
Schema::table('site_settings', function (Blueprint $table) {
    $table->json('about_content')->nullable()->after('fixed_text');
    $table->string('about_lake_image_credit')->nullable()->after('about_lake_image_url');
    $table->boolean('about_legal_verified')->default(false)->after('about_content');
});
```

SQL yang benar-benar dijalankan (dari `migrate --pretend`):
```sql
alter table `site_settings` add `about_content` json null after `fixed_text`
alter table `site_settings` add `about_lake_image_credit` varchar(255) null after `about_lake_image_url`
alter table `site_settings` add `about_legal_verified` tinyint(1) not null default '0' after `about_content`
```

`down()` (aman untuk rollback dev):
```php
Schema::table('site_settings', function (Blueprint $table) {
    $table->dropColumn([
        'about_content',
        'about_lake_image_credit',
        'about_legal_verified',
    ]);
});
```

| Kolom | Tipe | Null? | Default | Terverifikasi |
|---|---|---|---|---|
| `about_content` | `json` | YES | NULL | ✅ |
| `about_lake_image_credit` | `varchar(255)` | YES | NULL | ✅ |
| `about_legal_verified` | `tinyint(1)` (boolean) | **NO** | **0 (FALSE)** | ✅ |

**Sifat:** murni `ADD COLUMN` (non-destruktif). Tidak ada drop/rename/alter tipe pada kolom lama.

---

## 3. CONFIRMED DATABASE ENVIRONMENT

| Item | Nilai |
|---|---|
| `app.env` | `local` |
| `database.default` | `mysql` |
| `database.connections.mysql.host` | `127.0.0.1` |
| `database.connections.mysql.database` | `tova_matano` |
| `database.connections.mysql.port` | `3306` |

**Kesimpulan:** Target migrasi = **database development lokal** (`127.0.0.1`). Tidak ada koneksi ke produksi. `.env` tidak diubah.

> Catatan: instruksi mensyaratkan "local testing and database backup". Migrasi dijalankan di DB lokal pengembangan. Disarankan `mysqldump` sebelum menjalankan migrasi yang sama di **produksi** (belum dilakukan; di luar scope 3B-1).

---

## 4. MIGRATION EXECUTION RESULT

- `php artisan migrate --pretend` → menampilkan 3 `ALTER TABLE ... ADD COLUMN` (sesuai desain).
- `php artisan migrate --no-interaction` → **DONE**, `2026_11_11_143330_add_about_content_to_site_settings_table` (222.81ms).
- `php artisan migrate:status` → `2026_11_11_143330_add_about_content_to_site_settings_table .. [7] Ran`.

**Hasil:** BERHASIL.

---

## 5. EXISTING DATA PRESERVATION CHECKS

Baseline direkam **sebelum** migrasi, dibandingkan **sesudah**:

| Item | Sebelum | Sesudah | Status |
|---|---|---|---|
| `site_settings` row count | 1 | 1 | ✅ tidak berubah |
| `fixed_text` (id=1) | `{"en": "Protect Lake Matano, Protect Life", "id": "Jaga Danau Matano, Jaga Kehidupan"}` | identik | ✅ tidak berubah |
| `about_hero_image_url` | `settings/01M6BBX7HR70S6395VP3WMXCE6.jpeg` | identik | ✅ tidak berubah |
| `about_lake_image_url` | `settings/01M6BBX7JDTVSP4KK6PBK87WX0.jpeg` | identik | ✅ tidak berubah |
| `stat_sampah_terangkut` | `2,4 Ton` | identik | ✅ tidak berubah |
| Kolom existing (18) | ada | tetap ada (21 total) | ✅ tidak ada yang hilang |

Nilai kolom baru pada baris existing:
- `about_content` = `NULL`
- `about_lake_image_credit` = `NULL`
- `about_legal_verified` = `0` (FALSE) ✅ **default FALSE terkonfirmasi**

---

## 6. TESTS PERFORMED & RESULTS

| Uji | Perintah | Hasil |
|---|---|---|
| Dry-run SQL | `php artisan migrate --pretend` | ✅ 3 ADD COLUMN |
| Eksekusi migrasi lokal | `php artisan migrate --no-interaction` | ✅ DONE |
| Status migrasi | `php artisan migrate:status` | ✅ Ran [7] |
| Verifikasi kolom & default | skrip read-only (`SHOW COLUMNS`) | ✅ sesuai |
| Row count & nilai existing | skrip read-only (before/after) | ✅ tidak berubah |
| Default `about_legal_verified` | query row id=1 | ✅ `0` (FALSE) |
| Test suite (SQLite in-memory, menjalankan semua migrasi + RefreshDatabase) | `php artisan test --compact` | ✅ **68 passed, 401 assertions** |

> `down()` **tidak** dijalankan (sesuai instruksi: tidak rollback DB berisi data penting). Implementasi `down()` hanya untuk rollback dev.

---

## 7. GIT WORKING-TREE CHANGES

Perubahan dari Phase 3B-1 (hanya file yang diizinkan):
```
?? database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php
?? docs/reports/PHASE_3B_1_MIGRATION_REPORT.md
```
Dipertahankan (tidak disentuh):
```
 M app/Http/Controllers/WebController.php          (Phase 2B — pre-existing)
 M resources/js/Components/Public/AboutSnippet.jsx  (Phase 2B — pre-existing)
 M resources/js/Pages/Home.jsx                      (Phase 2B — pre-existing)
?? docs/reports/*                                   (laporan sebelumnya)
?? "docs/revisi web.docx"                           (dokumen klien)
```
**Tidak ada** perubahan pada `SiteSettings`, Filament, `WebController`, `About.jsx`, file lokal `lang/*`, atau file Phase 1/2.

---

## 8. UNRESOLVED RISKS

| # | Risiko | Catatan |
|---|---|---|
| R1 | Migrasi belum dijalankan di **produksi** | Perlu `mysqldump` backup + jadwal deploy sebelum `php artisan migrate` produksi |
| R2 | `down()` belum diuji terhadap data | Sengaja tidak dijalankan (melindungi data); hanya untuk rollback dev |
| R3 | `after('about_lake_image_url')` spesifik MySQL | Aman untuk MySQL; SQLite (test) mengabaikan `after()` → test tetap lulus |
| R4 | Model belum di-cast `about_content`/`about_legal_verified` | Akan ditangani Phase 3B-2 (di luar scope 3B-1). Saat ini kolom belum dibaca aplikasi |
| R5 | Nilai default `about_legal_verified = 0` | Sesuai desain keamanan legal (tersembunyi sampai diverifikasi) |

---

## 9. KEPATUHAN SCOPE

- ✅ Satu migrasi baru, aditif, non-destruktif.
- ✅ `down()` tersedia untuk rollback dev.
- ✅ Tidak mengubah migrasi existing, model, Filament, controller, `About.jsx`, `lang/*`, Phase 1/2.
- ✅ Tidak `migrate:fresh` / `db:wipe` / seeder.
- ✅ Tidak mengubah record DB (hanya menambah kolom).
- ✅ DB lokal terverifikasi (`127.0.0.1/tova_matano`), bukan produksi.
- ✅ Tidak commit/push/deploy.

**STOP — Phase 3B-1 selesai.** Menunggu review & persetujuan Project Owner sebelum Phase 3B-2 (model `SiteSettings`).
