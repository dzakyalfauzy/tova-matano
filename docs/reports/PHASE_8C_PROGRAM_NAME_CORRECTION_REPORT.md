# PHASE 8C — PROGRAM NAME CORRECTION REPORT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 8C — Program #4 naming correction  
**Date:** November 15, 2026  
**Status:** COMPLETE — AWAITING REVIEW

---

## 1. Inspection Results

### Data Source
Program names are **100% database-driven**. No program names are hardcoded in frontend JSX, localization files, or PHP controllers. The `WebController` queries `Program::orderBy('order')` and passes the result to Inertia pages.

### Current Database Record (Local)

| Field | Value |
|---|---|
| `id` | 4 |
| `slug` | `biodiversity-watch` |
| `name` | `Biodiversity Watch` |
| `order` | 4 |
| Activities | 2 (`survei-populasi-ikan-butini-2026`, `dokumentasi-flora-air-danau-matano-2026`) |

### Name Occurrences in Source Code

| Location | Context | Action |
|---|---|---|
| `database/seeders/DatabaseSeeder.php:190` | Seed data `'name' => 'Biodiversity Watch'` | **Updated** to `'Tova Campaign & Event'` |
| `database/seeders/DatabaseSeeder.php:277` | Comment `// Biodiversity Watch` | **Updated** to `// Tova Campaign & Event` |
| `app/Console/Commands/SeedMissingData.php:121` | Variable lookup `$p4 = Program::where('slug', 'biodiversity-watch')` | **Unchanged** — slug-based, correct |
| `app/Console/Commands/SeedMissingData.php:165` | Activity HTML content `"Tim Biodiversity Watch bersama..."` | **Unchanged** — narrative content inside activity body |
| `database/seeders/DatabaseSeeder.php:283` | Activity HTML content `"Tim Biodiversity Watch bersama..."` | **Unchanged** — narrative content inside activity body |
| `docs/reports/*.md` (multiple) | Documentation references | **Unchanged** — historical documentation |

### Frontend Verification
- `Programs.jsx`, `ProgramCategory.jsx`, `ProgramDetail.jsx` — all render `program.name` from DB props. No hardcoded names. Zero frontend changes needed.
- No localization keys exist for program names — they come directly from the database.

---

## 2. Changes Made

### Seeder Update (for fresh environments)

**`database/seeders/DatabaseSeeder.php`**

Before:
```php
'name' => 'Biodiversity Watch',
```

After:
```php
'name' => 'Tova Campaign & Event',
```

Comment updated from `// Biodiversity Watch` to `// Tova Campaign & Event`.

**Important:** The seeder uses `Program::firstOrCreate(['slug' => $d['slug']], $d)` — this means existing database records are **never** overwritten by rerunning the seeder. The slug `biodiversity-watch` is the lookup key and remains unchanged.

### No Code Changes Required
- No frontend files modified.
- No migration needed.
- No controller changes.
- No localization changes.
- No Filament resource changes.

---

## 3. Existing Slug & URL Preservation

| Route Pattern | URL | Status |
|---|---|---|
| Programs listing | `/program` | Unchanged |
| Program category | `/program/biodiversity-watch` | Slug preserved |
| Activity detail | `/program/biodiversity-watch/survei-populasi-ikan-butini-2026` | Slug preserved |
| Activity detail | `/program/biodiversity-watch/dokumentasi-flora-air-danau-matano-2026` | Slug preserved |

All 9 activity URLs across all 4 programs remain intact.

---

## 4. Required Admin Panel Operation

Since the live database record still shows `Biodiversity Watch`, the name must be updated via the **Filament Admin Panel** during the production synchronization/deployment stage:

1. Log in to the Admin Panel.
2. Navigate to **Programs** resource.
3. Edit the program with slug `biodiversity-watch` (currently named "Biodiversity Watch").
4. Change the **Name** field to: `Tova Campaign & Event`
5. **Do NOT change the slug** — leave it as `biodiversity-watch`.
6. Save.

This is a single-field update on a single record. No other programs should be modified.

---

## 5. Test Results

### Focused Tests

```
php artisan test tests/Feature/ProgramNameCorrectionTest.php --compact
→ 7 passed / 71 assertions
```

Tests cover:
1. Fourth program displays `Tova Campaign & Event` on programs page.
2. Other three program names (`Tova Water Patrol`, `Eco Education`, `Clean Lake Action`) unchanged.
3. Program category page resolves via existing slug `biodiversity-watch`.
4. Activity detail page resolves under existing program slug.
5. Program ID and slug unchanged after name update.
6. Activity relationship with renamed program intact.
7. Seeder source contains correct name and no longer contains `Biodiversity Watch`.

### Full Suite

```
php artisan test --compact
→ 204 passed / 1794 assertions (49.87s)
```

Baseline 197/1723 → +7 tests / +71 assertions, zero regressions.

### Pint

```
vendor/bin/pint --dirty --format agent
→ passed
```

### Build

No `npm run build` needed — no frontend files were modified.

---

## 6. Files Changed

| File | Change |
|---|---|
| `database/seeders/DatabaseSeeder.php` | Program #4 name updated; comment updated |
| `tests/Feature/ProgramNameCorrectionTest.php` | New test file (7 tests / 71 assertions) |
| `docs/reports/PHASE_8C_PROGRAM_NAME_CORRECTION_REPORT.md` | This report |

Not modified: frontend JSX, controllers, models, migrations, Filament resources, localization, activity data, slugs, other program records, or any Phase 1–8B.2 work.

---

## 7. Safety Confirmation

- No production database accessed or modified.
- No destructive operations (`migrate:fresh`, `db:wipe`, direct SQL writes).
- Local database record **not** changed (requires Admin Panel update per §4).
- All existing slugs and URLs preserved.
- No commit, push, or deployment.
- All previous phase work preserved.

---

**STOP — awaiting review.**
