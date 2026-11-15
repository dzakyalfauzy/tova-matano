# PHASE 0.2 — GIT PRE-COMMIT REVIEW & DEPLOYMENT ROLLBACK CORRECTION

**Project:** Tova Matano Care Center (TMCC) Website  
**Working Directory:** `C:\Users\ameli\OneDrive\Documents\Tova_Matano`  
**Target Repository:** `origin/main` (`https://github.com/dzakyalfauzy/tova-matano.git`)  
**Base Commit:** `e5e8886` (*feat: implement TMCC phase 1 client revisions*)  
**Proposed Commit Message:** `feat: consolidate TMCC client revisions (phases 2 through 8c)`  
**Audit Reference:** `docs/reports/FINAL_CONSOLIDATION_PHASE_0_AUDIT.md`, `docs/reports/FINAL_CONSOLIDATION_PHASE_0_1_CORRECTION.md`  
**Date:** November 15, 2026  
**Mode:** STRICT READ-ONLY PRE-COMMIT REVIEW  

---

## 1. FILE INVENTORY & CATEGORIZATION

An exact audit of all 22 modified tracked files and 21 untracked items was performed, categorized by role:

### Group A: Backend Application Code (9 files, modified tracked)
- `app/Filament/Pages/ManageSiteSettings.php` — Header image uploads (Blog, Partnership, Program, Contact), Water Quality map upload, About content JSON schema.
- `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php` — Added `CERITA` category to options.
- `app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php` — Added `CERITA` category badge & filter.
- `app/Filament/Resources/Members/Tables/MembersTable.php` — Made `name` column sortable.
- `app/Http/Controllers/WebController.php` — Passes sanitized `SiteSettings` to all Inertia pages.
- `app/Models/SiteSettings.php` — Extended `$fillable` with 9 new attributes.
- `database/seeders/DatabaseSeeder.php` — Program #4 name updated to `Tova Campaign & Event`.
- `lang/en/site.php` — Subtitle and category localization keys for Blog.
- `lang/id/site.php` — Subtitle and category localization keys for Blog.

### Group B: Frontend Application Code (12 files, modified tracked)
- `resources/js/Components/Public/AboutSnippet.jsx` — Updated About snippet copy & responsive layout.
- `resources/js/Components/Public/Navigation.jsx` — Reverted to emblem+text horizontal lockup, xl tablet breakpoint.
- `resources/js/Pages/About.jsx` — Full stacked logo in Profil, mobile lake image-first ordering.
- `resources/js/Pages/Blog.jsx` — Hero background image rendering, `CERITA` filter tabs.
- `resources/js/Pages/BlogDetail.jsx` — `CERITA` category badge rendering.
- `resources/js/Pages/Contact.jsx` — Hero background rendering, exact client subtitle wording.
- `resources/js/Pages/Gallery.jsx` — Permanent captions, lazy loading, Inertia router visit fix.
- `resources/js/Pages/Home.jsx` — Responsive hero slider, marquee animation, program card ordering.
- `resources/js/Pages/Organization.jsx` — Image chart rendering with accessible modal lightbox.
- `resources/js/Pages/Partnerships.jsx` — Hero background rendering with solid navy fallback.
- `resources/js/Pages/Programs.jsx` — Hero background rendering with solid navy fallback.
- `resources/js/Pages/WaterQuality.jsx` — Map image rendering with credit line and null-safe defensive checks.

### Group C: Database Migrations (4 files, untracked)
- `database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php` (3 columns: `about_content`, `about_lake_image_credit`, `about_legal_verified`)
- `database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php` (2 columns: `water_quality_map_image_url`, `water_quality_map_image_credit`)
- `database/migrations/2026_11_14_100000_add_blog_hero_image_to_site_settings_table.php` (1 column: `blog_hero_image_url`)
- `database/migrations/2026_11_14_110000_add_page_hero_images_to_site_settings_table.php` (3 columns: `partnership_hero_image_url`, `program_hero_image_url`, `contact_hero_image_url`)

### Group D: Automated Test Suites (15 files: 1 modified tracked, 14 untracked)
- `tests/Unit/SiteSettingsTest.php` (modified tracked)
- `tests/Feature/AboutPageTest.php` (untracked)
- `tests/Feature/BlogPageTest.php` (untracked)
- `tests/Feature/GalleryPageTest.php` (untracked)
- `tests/Feature/ManageSiteSettingsMapUploadTest.php` (untracked)
- `tests/Feature/ManageSiteSettingsTest.php` (untracked)
- `tests/Feature/MemberTableSortingTest.php` (untracked)
- `tests/Feature/MembersPageTest.php` (untracked)
- `tests/Feature/OrganizationPageTest.php` (untracked)
- `tests/Feature/PageHeroBackgroundsTest.php` (untracked)
- `tests/Feature/ProgramNameCorrectionTest.php` (untracked)
- `tests/Feature/SiteSettingsAboutContentTest.php` (untracked)
- `tests/Feature/SiteSettingsWaterQualityMapTest.php` (untracked)
- `tests/Feature/WaterQualityMapTest.php` (untracked)
- `tests/Unit/SiteLocalizationTest.php` (untracked)

### Group E: Static Assets (1 file, untracked)
- `public/image/logo_tova_matano_full.png` (untracked, 1,204,260 bytes / 1.15 MB, 1319×1193px).
  - **Verification:** Actively rendered in `resources/js/Pages/About.jsx:102` (`src="/image/logo_tova_matano_full.png"`).
  - **Verdict:** CRITICAL asset required by the application. Must be included in Git.

### Group F: Audit & Implementation Documentation (1 directory, untracked)
- `docs/reports/` (untracked folder containing 40 markdown audit and phase reports tracking Phase 2 through Phase 8C, plus consolidation audits).

### Group G: Client Requirement Documents (1 file, untracked)
- `docs/revisi web.docx` (untracked, 4,088,593 bytes / 4.09 MB binary Word document).

---

## 2. SENSITIVE FILE REVIEW

A rigorous check was executed across the working directory to ensure zero sensitive data is committed:

| File / Pattern | Status | Verification Detail |
|---|---|---|
| `.env` | **PROTECTED** | Ignored by `.gitignore:3`. Not staged, not tracked. |
| `database/database.sqlite` | **PROTECTED** | Ignored by `database/.gitignore:1`. |
| `tmcc-build.zip` / `tmcc-build.tar.gz` | **PROTECTED** | Ignored by `.gitignore:31-32`. |
| `storage/logs/*.log` | **PROTECTED** | Ignored by `storage/logs/.gitignore:1`. |
| Passwords / API Keys / DB Credentials | **CLEAN** | Grep scan across all migrations, tests, reports, and code found zero hardcoded secrets or production credentials. |
| Personal Information | **CLEAN** | All test data uses Laravel Faker or generic sample strings; real production content is managed strictly via production database. |

---

## 3. CLIENT REQUIREMENT DOCUMENT RECOMMENDATION (`docs/revisi web.docx`)

`docs/revisi web.docx` is the authoritative client revision specification file provided at the start of this sprint.

- **File Type:** Microsoft Word Binary Document (`.docx`)
- **File Size:** **4,088,593 bytes (~4.09 MB)**

### Comparison of Options:

| Criteria | Option A: Include in Git | Option B: Keep Locally Only (Add to `.gitignore`) |
|---|---|---|
| **Repository Size** | Permanently adds 4.1 MB to git history and every future clone. | Keeps repository history lean and code-focused. |
| **Auditability** | Any developer cloning the repository has the client brief directly on hand. | Client brief remains stored locally and in team storage/drive. |
| **Git Diff Value** | Zero. Git cannot diff binary `.docx` files. | Content is already fully extracted into readable Markdown in `docs/reports/PHASE_3_1_CLIENT_CONTENT_EXTRACTION.md`. |
| **Standard Practice** | Discouraged for large binaries unless Git LFS is configured. | Recommended for web application code repositories. |

### Recommendation:
**EXCLUDE from the consolidated code commit by default.** Await explicit user decision:
- If user wants it tracked in git: stage and commit alongside reports.
- If user prefers a lean repo: add `docs/*.docx` to `.gitignore` and retain the file locally.

---

## 4. REPORT INVENTORY (`docs/reports/`)

The `docs/reports/` folder contains 40 Markdown reports:
- **Phase 0 & 0.1 Reports:** `FINAL_CONSOLIDATION_PHASE_0_AUDIT.md`, `FINAL_CONSOLIDATION_PHASE_0_1_CORRECTION.md`
- **Master Roadmap & Client Extraction:** `TMCC_MASTER_REVISION_ROADMAP.md`, `PHASE_3_1_CLIENT_CONTENT_EXTRACTION.md`
- **Phase 2–8C Audit & Implementation Reports (36 files):** Covering Home, About, Water Quality, Gallery, Members, Organization, Blog, Logos, Hero Backgrounds, and Program name corrections.

### Verification:
- **Duplicates:** Zero redundant duplicate files found. Each report marks a discrete milestone or sub-phase.
- **Sensitive Content:** Scanned for tokens, passwords, and private URLs — clean.
- **Repository Value:** These reports serve as the permanent, verifiable proof of work and design decisions for TMCC. Staging and committing `docs/reports/` is strongly recommended.

---

## 5. RECOMMENDED STAGING LIST

When the user grants permission to commit, the following **42 items** should be staged:

### 1. Modified Tracked Files (22 files):
```
app/Filament/Pages/ManageSiteSettings.php
app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php
app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php
app/Filament/Resources/Members/Tables/MembersTable.php
app/Http/Controllers/WebController.php
app/Models/SiteSettings.php
database/seeders/DatabaseSeeder.php
lang/en/site.php
lang/id/site.php
resources/js/Components/Public/AboutSnippet.jsx
resources/js/Components/Public/Navigation.jsx
resources/js/Pages/About.jsx
resources/js/Pages/Blog.jsx
resources/js/Pages/BlogDetail.jsx
resources/js/Pages/Contact.jsx
resources/js/Pages/Gallery.jsx
resources/js/Pages/Home.jsx
resources/js/Pages/Organization.jsx
resources/js/Pages/Partnerships.jsx
resources/js/Pages/Programs.jsx
resources/js/Pages/WaterQuality.jsx
tests/Unit/SiteSettingsTest.php
```

### 2. New Database Migrations (4 files):
```
database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php
database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php
database/migrations/2026_11_14_100000_add_blog_hero_image_to_site_settings_table.php
database/migrations/2026_11_14_110000_add_page_hero_images_to_site_settings_table.php
```

### 3. New Automated Test Suites (14 files):
```
tests/Feature/AboutPageTest.php
tests/Feature/BlogPageTest.php
tests/Feature/GalleryPageTest.php
tests/Feature/ManageSiteSettingsMapUploadTest.php
tests/Feature/ManageSiteSettingsTest.php
tests/Feature/MemberTableSortingTest.php
tests/Feature/MembersPageTest.php
tests/Feature/OrganizationPageTest.php
tests/Feature/PageHeroBackgroundsTest.php
tests/Feature/ProgramNameCorrectionTest.php
tests/Feature/SiteSettingsAboutContentTest.php
tests/Feature/SiteSettingsWaterQualityMapTest.php
tests/Feature/WaterQualityMapTest.php
tests/Unit/SiteLocalizationTest.php
```

### 4. Static Asset (1 file):
```
public/image/logo_tova_matano_full.png
```

### 5. Documentation & Reports (1 directory):
```
docs/reports/
```

---

## 6. EXCLUDED FILES

The following files are **EXCLUDED** from the automatic staging set:
1. `docs/revisi web.docx` — Excluded pending user decision (Section 3).
2. `.env`, `database.sqlite`, `tmcc-build.*`, `storage/logs/*` — Excluded by `.gitignore`.

---

## 7. CORRECTED DEPLOYMENT ROLLBACK STRATEGY

The initial deployment rollback proposals in Phase 0.1 contained critical operational hazards that must be corrected:

### Dangers of Naive Rollbacks:
1. **Hazard of `php artisan migrate:rollback --step=4`:**
   - The `site_settings` table is a singleton record.
   - If admins entered content or uploaded images in those 9 new fields on production, rolling back the migrations will drop those 9 columns and **PERMANENTLY DESTROY all entered data**.
   - If other migrations were executed on production in the interim, `--step=4` would roll back completely unintended tables.
2. **Hazard of Blind Full Database Restoration:**
   - If days or even hours have elapsed since deployment, live users may have sent contact inquiries or volunteers may have registered.
   - Restoring a pre-deployment database dump will **WIPE OUT all intervening user submissions**.
3. **Hazard of `git checkout` as Sole Code Rollback:**
   - Reverting PHP code without simultaneously rebuilding and syncing Vite assets leaves a catastrophic mismatch between Inertia component props and JavaScript build bundles.

### Safe, Non-Destructive Rollback Strategy:

```
+-------------------------------------------------------------------------------+
|                       DEPLOYMENT INCIDENT OCCURS                             |
+-------------------------------------------------------------------------------+
                                        |
                 +----------------------+----------------------+
                 |                                             |
    [Scenario A: Immediate Failure]              [Scenario B: Delayed Discovery]
   (During 15-min maintenance window;            (Hours/days after go-live; live
      Zero live user transactions)                 data has been accumulated)
                 |                                             |
                 v                                             v
    1. Keep maintenance mode ON:                  1. DO NOT drop migration columns.
       `php artisan down`                         2. DO NOT restore full DB dump.
    2. Inspect exact error log:                   3. Apply a FORWARD-FIX (`fix: ...`)
       `tail -n 100 storage/logs/laravel.log`        for code or styling bugs.
    3. Revert code via git checkout TO            4. If a specific column's data is
       PREVIOUS TAG: `git checkout e5e8886`          corrupted, execute an isolated,
    4. Restore pre-deploy DB dump ONLY IF            surgical data fix query, NOT a
       database corruption occurred.                 full table wipe.
    5. Re-run cache clear:                        5. If reverting a feature entirely,
       `php artisan optimize:clear`                  hide the UI toggle or route in code
    6. Rebuild / restore previous assets.            while keeping database columns intact.
    7. Exit maintenance: `php artisan up`
```

### Pre-Deployment Migration Safety Checklist:
- [ ] In production terminal, run: `php artisan migrate:status`.
- [ ] Confirm that `site_settings` does NOT already have the 9 columns before migrating.
- [ ] Execute `php artisan migrate --force`.
- [ ] Confirm that exactly 4 migrations ran (Batches 7–10) and no other tables were modified.

---

## 8. FINAL APPROVAL CHECKLIST

Before any write action is initiated, explicit user approval is required on:

- [ ] **Approval A — Git Staging:** Approve staging the 42 recommended files (Group A, B, C, D, E, F).
- [ ] **Approval B — Client Word Document:** Decide whether `docs/revisi web.docx` should be:
  - (1) Included in Git, OR
  - (2) Kept locally only.
- [ ] **Approval C — Git Commit:** Approve executing the consolidated commit:
  `feat: consolidate TMCC client revisions (phases 2 through 8c)`
- [ ] **Approval D — Git Push:** Approve pushing `main` to `origin/main`.
- [ ] **Approval E — Production Deployment:** Approve initiating production synchronization per Section 7.

---

**PRE-COMMIT REVIEW COMPLETE — STRICT READ-ONLY MODE MAINTAINED — ZERO FILES STAGED OR COMMITTED — AWAITING USER APPROVAL.**
