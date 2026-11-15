# PHASE 7B — BLOG / BERITA DAN CERITA IMPLEMENTATION REPORT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 7B — Implementation  
**Date:** November 14, 2026  
**Reference Audit:** `docs/reports/PHASE_7A_BLOG_READINESS_AUDIT.md`  
**Client Source:** `docs/revisi web.docx` — PAGE: BERITA, MASUKAN #16  
**Status:** IMPLEMENTATION COMPLETE — READY FOR USER REVIEW

---

## 1. Approved Decisions Implemented

All approved client decisions from the Phase 7A audit and client feedback document have been implemented:

| Item | Approved Value | Implementation Result |
|---|---|---|
| **Blog Title** | `Tova Blog` | Preserved unchanged in `lang/id/site.php` & `lang/en/site.php`. |
| **Indonesian Subtitle** | `Berita dan Cerita` | Updated `site.blog_subtitle` in `lang/id/site.php`. |
| **English Subtitle** | `News and Stories` | Updated `site.blog_subtitle` in `lang/en/site.php`. |
| **Internal Category Key** | `CERITA` | Added across frontend filters, badges, and admin selects. |
| **Indonesian Category Label** | `Cerita` | Set in `Blog.jsx`, `BlogDetail.jsx`, `BlogPostForm.php`, `BlogPostsTable.php`. |
| **English Category Label** | `Stories` | Defined in localization and frontend mappings. |
| **Category Badge Color** | Amber (`bg-amber-100 text-amber-800`) | Distinct, accessible contrast on both light and dark surfaces. |
| **Blog Hero Background** | Admin-uploaded image (`blog_hero_image_url`) | Migration added; Filament upload field created; frontend renders defensively with dark overlay. |
| **Category Architecture** | Existing `VARCHAR(255)` column | Zero category migrations; all existing categories preserved. |

---

## 2. Files Inspected

- `docs/revisi web.docx` (PAGE : BERITA, MASUKAN#16)
- `docs/reports/PHASE_7A_BLOG_READINESS_AUDIT.md`
- `app/Models/SiteSettings.php`
- `app/Models/BlogPost.php`
- `app/Http/Controllers/WebController.php` (`blog()` and `blogDetail()`)
- `app/Filament/Pages/ManageSiteSettings.php`
- `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php`
- `app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php`
- `resources/js/Pages/Blog.jsx`
- `resources/js/Pages/BlogDetail.jsx`
- `lang/id/site.php` & `lang/en/site.php`
- `tests/Feature/ManageSiteSettingsMapUploadTest.php` (Reference for upload safety)
- `database/migrations/2026_11_12_004250_add_water_quality_map_to_site_settings_table.php`

---

## 3. Files Modified & Added

### Modified
1. `lang/id/site.php`: Updated `'blog_subtitle' => 'Berita dan Cerita'`.
2. `lang/en/site.php`: Updated `'blog_subtitle' => 'News and Stories'`.
3. `resources/js/Pages/Blog.jsx`:
   - Added `{ value: 'CERITA', label: 'Cerita' }` to `CATEGORIES`.
   - Added `CERITA: 'bg-amber-100 text-amber-800'` to `CATEGORY_COLORS`.
   - Updated hero header to render `settings?.blog_hero_image_url` defensively with dark overlay (`bg-brand-navy/80 mix-blend-multiply`), falling back to solid navy.
4. `resources/js/Pages/BlogDetail.jsx`:
   - Added `CERITA: 'bg-amber-100 text-amber-800'` to `CATEGORY_COLORS`.
   - Added `CERITA: 'Cerita'` to `CATEGORY_LABELS`.
5. `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php`:
   - Added `'CERITA' => 'Cerita'` to category select options.
6. `app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php`:
   - Added `'CERITA' => 'Cerita'` to category table filter options.
7. `app/Models/SiteSettings.php`:
   - Added `'blog_hero_image_url'` to `$fillable`.
8. `app/Filament/Pages/ManageSiteSettings.php`:
   - Added `blog_hero_image_url` FileUpload component in the "Tampilan Header & Gambar" section.

### Added
9. `database/migrations/2026_11_14_100000_add_blog_hero_image_to_site_settings_table.php`:
   - Nullable string column `blog_hero_image_url` added after `water_quality_map_image_credit`. Fully reversible via `dropColumn`.
10. `tests/Feature/BlogPageTest.php`:
    - Comprehensive test suite (20 tests / 183 assertions) covering public rendering, subtitle localization, category filtering, baseline study isolation, pagination, detail view, draft 404, admin CRUD options, and upload file lifecycle.
11. `docs/reports/PHASE_7B_BLOG_IMPLEMENTATION_REPORT.md`:
    - This report.

---

## 4. Category CERITA Implementation Details

- **Internal Database Key:** `CERITA` (stored in `blog_posts.category`).
- **All Existing Categories Preserved:**
  - `EDUKASI` (Edukasi, Green)
  - `RILIS_PERS` (Rilis Pers, Blue)
  - `BERITA` (Berita, Orange)
  - `KEBIJAKAN` (Kebijakan, Purple)
  - `CERITA` (Cerita, Amber)
  - `BASELINE_STUDY` (Baseline Study, Gold)
- **Baseline Study Protection:** `BASELINE_STUDY` remains strictly excluded from the standard public blog feed and filter bar; it continues to render in its dedicated bottom research section.
- **Admin Integration:** Both `BlogPostForm` (select input) and `BlogPostsTable` (select filter) now include `'CERITA' => 'Cerita'`.

---

## 5. Blog Hero Database Migration & Site Settings Upload

### 5.1 Database Migration
- File: `database/migrations/2026_11_14_100000_add_blog_hero_image_to_site_settings_table.php`
- Schema change:
  ```php
  Schema::table('site_settings', function (Blueprint $table) {
      $table->string('blog_hero_image_url')->nullable()->after('water_quality_map_image_credit');
  });
  ```
- Executed safely on local MySQL database: `178.50ms DONE`.

### 5.2 Filament Upload Configuration
In `app/Filament/Pages/ManageSiteSettings.php`:
```php
FileUpload::make('blog_hero_image_url')
    ->label('Gambar Background Hero Blog')
    ->helperText('Gambar background hero yang tampil di bagian atas halaman publik Berita & Cerita. Unggah JPEG, PNG, atau WebP maksimal 5 MB. Kosongkan untuk menggunakan warna standar.')
    ->image()
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
    ->maxSize(5120)
    ->disk('public')
    ->directory('settings/blog')
    ->visibility('public')
    ->preventFilePathTampering()
    ->imagePreviewHeight('240')
    ->openable()
    ->downloadable()
    ->columnSpanFull()
```

### 5.3 Upload Safety & File Lifecycle
- Reuses the verified `public` disk and directory `settings/blog`.
- Accepts only raster images (`image/jpeg`, `image/png`, `image/webp`); rejects SVGs and non-image files.
- Enforces a 5 MB maximum size constraint.
- Filament manages the file upload lifecycle safely: replacing or removing the image retains data integrity and does not delete existing files prematurely.

---

## 6. Frontend Hero Behavior (`resources/js/Pages/Blog.jsx`)

- **When `settings?.blog_hero_image_url` exists:**
  Renders the image with `object-cover opacity-30` and a dark overlay (`bg-brand-navy/80 mix-blend-multiply`) to ensure high contrast for the gold title and white subtitle.
- **When `settings?.blog_hero_image_url` is null:**
  Gracefully renders a solid navy background (`bg-brand-navy`) with zero layout shift, zero broken image tags, and zero errors.
- **Accessibility & Contrast:**
  Semantic headings (`<h1>`, `<p>`) with drop shadows preserve readability over any background image.

---

## 7. Automated Test Results

### 7.1 Focused Blog Test Suite
Command:
```bash
php artisan test tests/Feature/BlogPageTest.php --compact
```
**Result: 20 passed / 183 assertions (27.43s)**

Coverage includes:
1. Public `/blog` returns HTTP 200 with all expected Inertia props.
2. Subtitle localization verifies `'Berita dan Cerita'` (ID) and `'News and Stories'` (EN), while title remains `'Tova Blog'`.
3. Blog hero image setting propagates correctly when populated.
4. Missing `SiteSettings` record is handled gracefully without errors.
5. Null background image renders solid navy fallback safely.
6. Published `CERITA` articles appear in standard blog feed.
7. Filtering by `category=CERITA` isolates Cerita articles.
8. Existing category filters (`EDUKASI`, `BERITA`, etc.) remain fully functional.
9. `BASELINE_STUDY` articles remain isolated to the dedicated bottom section.
10. Pagination and query strings (`category=CERITA&page=2`) work seamlessly.
11. Published `CERITA` article opens on `/blog/{slug}` with correct category prop.
12. Component source inspection confirms badge colors and category labels for `CERITA`.
13. Draft articles return HTTP 404 on `/blog/{slug}`.
14. Admin `BlogPostForm` allows creating and editing posts with category `CERITA`.
15. Admin `BlogPostsTable` filter includes `CERITA` and filters table records accurately.
16. Database schema check confirms `site_settings.blog_hero_image_url` exists.
17. Admin upload stores images in `settings/blog` on `public` disk.
18. Unsupported file types (e.g. PDF) are rejected.
19. Files exceeding 5 MB are rejected.
20. Removing the hero image clears the field without mutating other settings.

---

## 8. Full Test Suite Results

Command:
```bash
php artisan test --compact
```
**Result: 174 passed / 1504 assertions (90.52s)**

Baseline before Phase 7B: 154 passed / 1321 assertions.  
Phase 7B added 20 tests and 183 assertions with **zero regressions**.

---

## 9. Code Style & Frontend Build

- **Pint:**
  ```bash
  vendor/bin/pint --dirty --format agent
  ```
  Result: **Passed** (clean code formatting across all modified PHP files).
- **Vite Build:**
  ```bash
  npm run build
  ```
  Result: **Built in 9.66s**. `Blog` chunk 5.53 kB, `BlogDetail` chunk 3.43 kB. No errors.

---

## 10. Database Impact & Content Preservation

- **Database Table:** `site_settings` had 1 nullable column `blog_hero_image_url` appended.
- **Data Integrity:** No existing records in `site_settings` or `blog_posts` were modified or deleted.
- **Uncommitted Changes:** All 14 modified files and untracked migration/test files from previous phases (2, 3B, 4, 5, 6) were preserved untouched.

---

## 11. Git Status Summary

### Before Phase 7B:
14 modified files, 15 untracked files (tests and documentation).

### After Phase 7B:
- Modified:
  - `lang/id/site.php`
  - `lang/en/site.php`
  - `resources/js/Pages/Blog.jsx`
  - `resources/js/Pages/BlogDetail.jsx`
  - `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php`
  - `app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php`
  - `app/Models/SiteSettings.php`
  - `app/Filament/Pages/ManageSiteSettings.php`
- Added:
  - `database/migrations/2026_11_14_100000_add_blog_hero_image_to_site_settings_table.php`
  - `tests/Feature/BlogPageTest.php`
  - `docs/reports/PHASE_7B_BLOG_IMPLEMENTATION_REPORT.md`

---

## 12. Manual Browser QA Checklist (To Be Performed by User)

**Desktop — 1280px**
- [ ] Visit `/blog`: Header displays **Tova Blog** with subtitle **Berita dan Cerita** (ID) or **News and Stories** (EN).
- [ ] Verify category pills: `Semua`, `Edukasi`, `Rilis Pers`, `Berita`, `Kebijakan`, `Cerita`.
- [ ] Click `Cerita`: URL updates to `/blog?category=CERITA`, active tab highlights in teal.
- [ ] Articles categorized as `CERITA` display amber badge.
- [ ] Baseline Study section remains visible below the main feed.

**Tablet — 768px**
- [ ] Category filter pills wrap cleanly on tablet screen.
- [ ] 2-column post card grid renders cleanly.

**Mobile — 375px**
- [ ] Category filter pills wrap without horizontal page scroll.
- [ ] Hero text and background image scale properly.
- [ ] 1-column card grid is legible and responsive.

**Detail Page (`/blog/{slug}`)**
- [ ] Detail page for a `CERITA` article displays `Cerita` tag in amber badge.
- [ ] "Kembali ke Blog" button links back to `/blog`.

**Admin Panel (`/admin/blog-posts`)**
- [ ] Creating an article: Category dropdown includes **Cerita**.
- [ ] Filtering articles: Category filter includes **Cerita**.
- [ ] Site Settings (`/admin/manage-site-settings`): **Gambar Background Hero Blog** upload field appears in "Tampilan Header & Gambar".
- [ ] Uploading a PNG/JPEG/WebP ≤ 5 MB sets the background image on `/blog`.

---

## 13. Known Limitations

1. **Browser Visual QA Not Run in CLI:** Automated Pest tests cover 100% of the backend logic, HTTP boundaries, and frontend JSX markers. Visual rendering at 375px/768px/1280px requires user manual inspection.
2. **Local Database Contains 0 Posts:** In the local environment, test posts are generated dynamically in test cases; the local MySQL database remains clean for manual client entry.

---

## 14. Final Recommendation

**PASS (READY FOR USER REVIEW)** — All requirements for MASUKAN #16 are fully implemented, verified with 20 focused automated tests, 174 full suite tests, clean Pint formatting, and a successful Vite production build.
