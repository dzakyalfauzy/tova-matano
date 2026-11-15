# Phase 4B-1 — Database & Model Report

**Feature:** TMCC Water Quality Station Map
**Scope:** Database migration and `SiteSettings` model foundation only. No admin UI, upload handling, public page, controller, or deployment changes.
**Date:** 2026-11-12
**Status:** Implemented and verified locally. Awaiting approval before Phase 4B-2.

---

## 1. Files Created & Changed

| Action | File |
| --- | --- |
| Created | `database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php` |
| Created | `tests/Feature/SiteSettingsWaterQualityMapTest.php` |
| Modified | `app/Models/SiteSettings.php` — added two entries to `$fillable` only |
| Created | `docs/reports/PHASE_4B_1_DATABASE_MODEL_REPORT.md` |

Not changed: `app/Filament/Pages/ManageSiteSettings.php`, `app/Http/Controllers/WebController.php`, `resources/js/Pages/WaterQuality.jsx`, `.env`, water-quality models/tables, sampling-point data, historical records, charts, filters, thresholds, pagination, or assets.

---

## 2. New Columns

| Column | Type | Nullable | Default | Position |
| --- | --- | --- | --- | --- |
| `water_quality_map_image_url` | `varchar(255)` | YES | `NULL` | after `org_chart_image_url` |
| `water_quality_map_image_credit` | `varchar(255)` | YES | `NULL` | after `water_quality_map_image_url` |

- No default values.
- No backfill.
- No existing columns changed or removed.
- `down()` drops only these two columns.
- No new table.
- Verified actual MySQL column order:
  `... org_chart_image_url, water_quality_map_image_url, water_quality_map_image_credit, stat_sampah_terangkut, ...`

---

## 3. Local Migration Result

Pre-flight checks confirmed the target database was local:

```
app.env = local
database.default = mysql
database.connections.mysql.host = 127.0.0.1
database.connections.mysql.database = tova_matano
database.connections.mysql.port = 3306
```

`migrate:status` showed all prior migrations already applied; only the new migration was pending.

Command:

```powershell
php artisan migrate --path=database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php --no-interaction
```

Result:

```
INFO Running migrations.
2026_11_12_004250_add_water_quality_map_to_site_settings_table .. 141.90ms DONE
```

`migrate:status` now reports:

```
2026_11_12_004250_add_water_quality_map_to_site_settings_table .. [8] Ran
```

Only this migration was run; no unrelated migration was applied.

---

## 4. Existing Data Verification

A read-only snapshot of the existing `site_settings` row was taken immediately before migration (excluding the not-yet-existing columns).

| Check | Before | After |
| --- | --- | --- |
| `site_settings` row count | 1 | 1 |
| Existing-column data hash | `d274d0e8c2cb05f41f09eec066a317c9` | `d274d0e8c2cb05f41f09eec066a317c9` |
| Existing data match | — | **YES** |
| `water_quality_records` count | 131 | 131 |
| `water_sampling_points` count | 6 | 6 |

The existing settings row was not updated, backfilled, or otherwise modified. The 131 historical water-quality records and six sampling stations were untouched.

No seeder was run and no dummy local data was added.

---

## 5. Tests & Pint

### New test coverage
`tests/Feature/SiteSettingsWaterQualityMapTest.php` (4 tests, 9 assertions):

1. Both new fields allow `null`.
2. Map path and credit persist correctly.
3. Map fields can be cleared back to `null`.
4. Updating the map fields preserves existing settings (`phone`, `fixed_text`, `about_content`, `about_legal_verified`, `about_lake_image_credit`, `org_chart_image_url`).

Tests run against the project's SQLite in-memory test database with `RefreshDatabase`; the new migration is applied by the test suite.

### Results

```text
Targeted:
php artisan test --compact \
  tests/Feature/SiteSettingsWaterQualityMapTest.php \
  tests/Unit/SiteSettingsTest.php \
  tests/Feature/SiteSettingsAboutContentTest.php \
  tests/Feature/ManageSiteSettingsTest.php
  => 23 passed, 81 assertions

New test file alone:
php artisan test --compact tests/Feature/SiteSettingsWaterQualityMapTest.php
  => 4 passed, 9 assertions

Full suite:
php artisan test --compact
  => 103 passed, 741 assertions
```

Baseline before this phase was 99 passed / 732 assertions; the 4 new tests add 4 tests and 9 assertions.

### Pint

```powershell
vendor/bin/pint \
  app/Models/SiteSettings.php \
  database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php \
  tests/Feature/SiteSettingsWaterQualityMapTest.php \
  --format agent
```

Result: **passed**.

---

## 6. Git Working Tree

Existing Phase 3B-6 changes were preserved. Nothing was reset, cleaned, deleted, committed, or pushed.

Modified files still present from Phase 3B-6:

```
M app/Filament/Pages/ManageSiteSettings.php
M app/Http/Controllers/WebController.php
M app/Models/SiteSettings.php
M lang/en/site.php
M lang/id/site.php
M resources/js/Components/Public/AboutSnippet.jsx
M resources/js/Pages/About.jsx
M resources/js/Pages/Home.jsx
M tests/Unit/SiteSettingsTest.php
```

New files for Phase 4B-1:

```
?? database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php
?? tests/Feature/SiteSettingsWaterQualityMapTest.php
?? docs/reports/PHASE_4B_1_DATABASE_MODEL_REPORT.md
```

Existing untracked Phase 3B-6 files (`docs/reports/`, `docs/revisi web.docx`, `tests/Feature/AboutPageTest.php`, etc.) remain untouched.

`app/Models/SiteSettings.php` contains both the prior Phase 3B-6 changes and the two new `$fillable` entries; no other model behavior was changed.

---

## 7. Production Confirmation

- All commands ran with `APP_ENV=local`.
- The database was explicitly verified as local MySQL `127.0.0.1:3306`, database `tova_matano`.
- No production credentials, hosts, or database connections were used.
- Migration was executed with an explicit `--path` to the new migration only.
- No `.env` change was made.
- No production migration was run.

---

## 8. Deployment Confirmation

- No deployment was performed.
- No commit, push, tag, or release was created.
- No build or server operation was run for deployment.
- Phase 4B-2 (admin upload/UI) was not started.

**Stop point:** Phase 4B-1 complete. Await approval before Phase 4B-2.
