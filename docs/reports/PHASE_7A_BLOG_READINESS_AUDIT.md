# PHASE 7A — BLOG / BERITA DAN CERITA READINESS AUDIT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 7A — Readiness & Architecture Audit  
**Date:** November 14, 2026  
**Status:** AUDIT COMPLETE — READY FOR CLIENT DECISION  
**Mode:** READ-ONLY (No code, database, migration, or record modifications)  
**Primary Reference:** `docs/revisi web.docx`  
**Master Roadmap Reference:** `docs/reports/TMCC_MASTER_REVISION_ROADMAP.md`

---

## 1. Executive Summary

### 1.1 Context & Purpose
Phase 7A investigates the blog/news module (`/blog` and `/blog/{slug}`) in response to feedback in the client's authoritative document `docs/revisi web.docx` under **`PAGE : BERITA`** (`MASUKAN#16`).

The audit aims to:
1. Extract exact client comments regarding the News/Blog page.
2. Determine how categories are currently defined, stored, and filtered.
3. Investigate the feasibility and architecture of adding a **"Cerita"** category alongside `Edukasi`, `Rilis Pers`, `Berita`, and `Kebijakan`.
4. Inspect database readiness, Filament admin management, frontend rendering, localization, and testing.
5. Provide a clear, zero-database-migration implementation path for Phase 7B.

### 1.2 Key Finding Highlight
The `blog_posts` table column `category` was originally created as an enum in `2026_01_01_000011_create_blog_posts_table.php`, but was subsequently converted to a standard `string` column by migration `2026_10_01_085628_alter_category_enum_in_blog_posts.php`.

**Crucial Technical Discovery:**
- The database schema **already supports any category string value without requiring a migration**.
- Adding the `CERITA` category requires **zero database schema changes** and **zero migrations**.
- Category lists are currently hardcoded in three application locations:
  1. Frontend Filter & Badges: `resources/js/Pages/Blog.jsx:5-19` and `resources/js/Pages/BlogDetail.jsx:5-19`
  2. Admin Form Select: `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php:39-45`
  3. Admin Table Filter: `app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php:55-61`

---

## 2. Source Document Findings (`docs/revisi web.docx`)

### 2.1 Extraction & Verbatim Text
The client document `docs/revisi web.docx` contains a dedicated section for the News/Blog page:

```
Line 103: PAGE : BERITA
Line 104: MASUKAN#16
Line 105: Perubahan Redaksional:
Line 106: Tova Blog
Line 107: Berita dan Cerita
Line 108: Kemudian, bisakah dibawahnya ditambahkan kolom Cerita selain Edukasi, rilis pers, berita,  dan kebijakan.
Line 109: Pertanyaan: memang tdk ada photo background kah
```

Related mentions in the document:
- Line 52 (Tentang Kami narrative): *"...Danau Matano merupakan kebanggaan dan bagian dari cerita perjalanan hidup kami..."*
- Line 118–119 (Admin navigation order note): *"Home – About – Program – Water Q – Galeri - News - Org - Member - Partnership – contact"* (References "News").

### 2.2 Client Requirements Interpretation & Analysis

| Item | Client Verbatim | Meaning & Expected Behavior | Ambiguity / Confirmation Needed |
|---|---|---|---|
| **R1. Page Header Title** | `Perubahan Redaksional: Tova Blog` | The title remains or is confirmed as "Tova Blog". Currently, `t.blog_title` in `lang/id/site.php` is already `'Tova Blog'`. | None. Already matches. |
| **R2. Page Header Subtitle** | `Berita dan Cerita` | The hero subtitle should become **"Berita dan Cerita"** (or localized English equivalent). Currently, `lang/id/site.php` has `'Edukasi, rilis pers, dan kabar terbaru dari lapangan.'`. | None. Clear editorial change. |
| **R3. Category Addition ("Cerita")** | `Kemudian, bisakah dibawahnya ditambahkan kolom Cerita selain Edukasi, rilis pers, berita,  dan kebijakan.` | The client requests a category/filter button for **"Cerita"** (Stories/Narratives) in addition to the existing four: Edukasi, Rilis Pers, Berita, and Kebijakan. The phrase *"kolom Cerita"* colloquially means a category tab/pill filter on the web page. | Exact category key convention (e.g. `CERITA`) and English translation (e.g. "Stories"). |
| **R4. Background Photo Question** | `Pertanyaan: memang tdk ada photo background kah` | The client asks whether the hero section of the Berita page has a background photo option (similar to Home, About, and Organization). Currently, the hero in `Blog.jsx` has a flat `bg-brand-navy` background with no background image setting. | Does the client want an admin upload field in Site Settings for `blog_hero_image_url`? |

---

## 3. Current Blog Implementation Audit

### 3.1 Public Blog Pages
- **Index View:** `resources/js/Pages/Blog.jsx`
  - Hero header displays `{t.blog_title}` and `{t.blog_subtitle}` from Laravel localization (`usePage().props.translations`).
  - Category buttons: hardcoded array `CATEGORIES` (lines 5–11):
    ```js
    const CATEGORIES = [
        { value: '', label: 'Semua' },
        { value: 'EDUKASI', label: 'Edukasi' },
        { value: 'RILIS_PERS', label: 'Rilis Pers' },
        { value: 'BERITA', label: 'Berita' },
        { value: 'KEBIJAKAN', label: 'Kebijakan' },
    ];
    ```
  - Note: `BASELINE_STUDY` is present in `CATEGORY_COLORS` (line 18), but excluded from `CATEGORIES` filter bar and rendered in a dedicated bottom section (lines 148–171).
  - Posts grid: 3-column responsive grid (`grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8`).
  - Card anatomy: cover image (or `<Tag>` placeholder), category badge, PDF badge (if `pdf_url` present), post title, and formatted Indonesian publication date (`toLocaleDateString('id-ID')`).
  - Pagination: 9 items per page via Laravel length-aware paginator with `withQueryString()`.
  - Empty state: clean placeholder (*"Belum ada artikel yang dipublikasikan."*).
- **Detail View:** `resources/js/Pages/BlogDetail.jsx`
  - Route: `/blog/{slug}`.
  - Cover image hero overlay (if `cover_image_url` is present).
  - Back button (`/blog`), category badge, formatted date, rich HTML content (`dangerouslySetInnerHTML={{ __html: post.content }}` with Tailwind typography `prose`).
  - Attached PDF viewer box with download/open button (if `pdf_url` is present).
  - Category badges use hardcoded lookup `CATEGORY_LABELS` and `CATEGORY_COLORS` (lines 5–19).

### 3.2 Controller (`app/Http/Controllers/WebController.php:176-203`)
```php
public function blog(Request $request)
{
    $query = BlogPost::where('is_published', true)
        ->where('category', '!=', 'BASELINE_STUDY')
        ->orderByDesc('published_at');

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    return Inertia::render('Blog', [
        'posts' => $query->paginate(9)->withQueryString(),
        'filters' => $request->only('category'),
        'baselineStudyPosts' => BlogPost::where('is_published', true)
            ->where('category', 'BASELINE_STUDY')
            ->orderByDesc('published_at')
            ->get(),
        'settings' => $this->settings(),
    ]);
}

public function blogDetail($slug)
{
    return Inertia::render('BlogDetail', [
        'post' => BlogPost::where('slug', $slug)->where('is_published', true)->firstOrFail(),
        'settings' => $this->settings(),
    ]);
}
```
- Only published posts (`is_published = true`) are accessible.
- Draft posts return 404 in `blogDetail`.
- Baseline Study posts are segregated into their own prop and excluded from the main paginated feed.

### 3.3 Admin Panel (`app/Filament/Resources/BlogPosts/`)
- **Form:** `BlogPostForm.php`
  - `title` with auto-slug generation on create (`Str::slug($state)`).
  - `slug` unique check.
  - `category` Select field with hardcoded options:
    - `EDUKASI` => 'Edukasi'
    - `RILIS_PERS` => 'Rilis Pers'
    - `BERITA` => 'Berita'
    - `KEBIJAKAN` => 'Kebijakan'
    - `BASELINE_STUDY` => 'Baseline Study'
  - `published_at` DateTimePicker (default `now()`).
  - `is_published` Toggle (default `true`).
  - `cover_image_url` FileUpload to disk `public`, directory `blog-posts`.
  - `pdf_url` FileUpload to disk `public`, directory `blog-pdfs`.
  - `content` RichEditor.
- **Table:** `BlogPostsTable.php`
  - Columns: `cover_image_url` (thumbnail), `title`, `slug`, `category` (badge), `published_at`, `is_published` (boolean icon).
  - Default sort: `published_at desc`.
  - Filters: `SelectFilter` on `category` (same 5 options), `TernaryFilter` on `is_published` (Terbit / Draf).
  - Actions: Edit, Delete, Bulk Delete. Re-direct trait applied via `FilamentCrudRedirectTest`.

---

## 4. Blog Database & Data Findings

### 4.1 Schema Inspection (`blog_posts` Table)
```sql
CREATE TABLE `blog_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `cover_image_url` varchar(255) DEFAULT NULL,
  `content` longtext,
  `pdf_url` varchar(255) DEFAULT NULL,
  `published_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_published` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_unique` (`slug`)
)
```
- `category` is a `varchar(255)`. It is **NOT** a MySQL enum constraint.
- No separate `categories` table exists for blog posts.
- `pdf_url` and `content` are nullable.
- `slug` is unique indexed.

### 4.2 Local Database Data Audit (Read-Only)
- **Total posts in local DB:** `0`
- **Published posts:** `0`
- **Draft posts:** `0`
- **Posts with cover:** `0`
- **Posts with PDF:** `0`
- **Categories represented:** None (empty table).
- **Physical storage:** `storage/app/public/blog-posts/` contains 1 orphaned file (`01M3HF94KV0ZBM2PZJ70P8A0KF.jpeg`) not referenced by any record in the local database.
- **Seeder reference:** `DatabaseSeeder.php` lines 372–470 defines 9 seed articles (2 Edukasi, 2 Rilis Pers, 1 Berita, 1 Kebijakan, 3 Baseline Study). The seeder has not been run against the current local DB.

---

## 5. Category Architecture Assessment — "Cerita"

### 5.1 Architecture Comparison

| Dimension | Option 1: Native String Category (Recommended) | Option 2: New Relational Category Table (`blog_categories`) |
|---|---|---|
| **Database Migration Required?** | **NO (0 migrations)** | **YES (at least 2 migrations: create table + FK alter)** |
| **Existing Code Alignment** | 100% matches existing codebase patterns (`BlogPost.php`, `WebController.php`, `BlogPostForm.php`). | Breaks existing convention; requires refactoring model, resource, controller, and seeder. |
| **Data Migration Risk** | **Zero**. Schema is already `varchar(255)`. | High risk of breaking existing or production data during FK conversion. |
| **Admin Usability** | Immediate: "Cerita" appears in category dropdown and table filter. | Requires building a separate CRUD resource for blog categories. |
| **Client Request Fit** | Exact: client requested adding *"kolom Cerita selain Edukasi, rilis pers, berita, dan kebijakan"*. | Over-engineered beyond client's explicit request. |
| **Implementation Effort** | Minimal: update category arrays in 4 files + localization. | High: new migrations, models, relations, seeders, filament resources. |

### 5.2 Technical Recommendation on Category Architecture
**Option 1 is unequivocally recommended.**
Because migration `2026_10_01_085628_alter_category_enum_in_blog_posts.php` already converted `category` to a plain string, storing `'CERITA'` requires zero database changes. We simply add `'CERITA'` to:
1. `CATEGORIES`, `CATEGORY_COLORS`, and `CATEGORY_LABELS` in `Blog.jsx` and `BlogDetail.jsx`.
2. Filament `BlogPostForm` category select options.
3. Filament `BlogPostsTable` category filter options.

---

## 6. UI/UX Assessment

### 6.1 Current Visual & Structural Layout
- **Hero Section:**
  - Background is solid `bg-brand-navy` with a `border-b-4 border-brand-teal`.
  - Title: `{t.blog_title}` ("Tova Blog").
  - Subtitle: `{t.blog_subtitle}` ("Edukasi, rilis pers, dan kabar terbaru dari lapangan.").
  - In response to the client's question *"memang tdk ada photo background kah"*, the hero currently does **not** feature a background image.
- **Filter Pills:**
  - Pill buttons styled with `rounded-full text-sm font-semibold`.
  - Active: `bg-brand-teal text-white shadow-md`.
  - Inactive: `bg-white text-gray-600 hover:bg-brand-teal/10 border border-gray-200`.
  - Adding "Cerita" will fit naturally within the existing horizontal flex-wrap container without overflowing desktop, tablet, or mobile screens.
- **Post Cards & Badges:**
  - 3-column grid with smooth hover scale on image.
  - Fallback icon `<Tag>` in `bg-brand-teal/5` when cover image is null.
  - Category badge colors currently assigned:
    - `EDUKASI`: Green (`bg-green-100 text-green-700`)
    - `RILIS_PERS`: Blue (`bg-blue-100 text-blue-700`)
    - `BERITA`: Orange (`bg-orange-100 text-orange-700`)
    - `KEBIJAKAN`: Purple (`bg-purple-100 text-purple-700`)
    - `BASELINE_STUDY`: Gold (`bg-brand-gold/20 text-brand-navy border border-brand-gold/30`)
    - Proposed for `CERITA`: Amber / Teal accent (e.g., `bg-amber-100 text-amber-800` or `bg-teal-100 text-teal-700`).
- **Responsive Behavior:**
  - Desktop (1280px): 3 columns.
  - Tablet (768px): 2 columns.
  - Mobile (375px): 1 column.

---

## 7. Localization Assessment

### 7.1 Current Localization Keys (`lang/id/site.php` & `lang/en/site.php`)
```php
// lang/id/site.php
'nav_blog' => 'Berita',
'blog_title' => 'Tova Blog',
'blog_subtitle' => 'Edukasi, rilis pers, dan kabar terbaru dari lapangan.',

// lang/en/site.php
'nav_blog' => 'News',
'blog_title' => 'Tova Blog',
'blog_subtitle' => 'Education, press releases, and the latest news from the field.',
```

### 7.2 Required Localization Updates for Client Revision
To implement client revision `MASUKAN#16`:
1. **Indonesian Subtitle (`lang/id/site.php`):**
   Change from `'Edukasi, rilis pers, dan kabar terbaru dari lapangan.'` to `'Berita dan Cerita'` (verbatim from client document).
2. **English Subtitle (`lang/en/site.php`):**
   Update to `'News and Stories'` to maintain bilingual consistency.
3. **Category "Cerita" Label:**
   - Indonesian: `'Cerita'`
   - English: `'Stories'` (or `'Story'`)

---

## 8. SEO, Metadata & Security Assessment

1. **Slug Handling:** Slugs are uniquely enforced at the database level (`UNIQUE KEY blog_posts_slug_unique`) and generated via `Str::slug($title)` in the Filament form. Slugs are never mutated during display.
2. **Access Control:** Public controller enforces `where('is_published', true)`. Unpublished/draft posts return 404 in `blogDetail($slug)` and are filtered out of the index query.
3. **Rich HTML Sanitization:** Rich content is rendered with `dangerouslySetInnerHTML`. Because only authenticated Filament administrators can author posts, standard CMS behavior applies.
4. **Thumbnail & PDF Safety:** File uploads are restricted to `public` disk in dedicated directories (`blog-posts` and `blog-pdfs`). `pdf_url` accepts only `application/pdf`.

---

## 9. Testing & Regression Risks

### 9.1 Existing Test Coverage
- `tests/Feature/FilamentCrudRedirectTest.php`: Tests that `BlogPostResource` uses the redirect trait upon create/edit.
- **There is currently NO dedicated public blog feature test** (`tests/Feature/BlogPageTest.php` does not exist).

### 9.2 Regression Risks
1. **Query Filtering:** Querying for `category=CERITA` must preserve pagination and query string parameters (`withQueryString()`).
2. **Baseline Study Isolation:** `baselineStudyPosts` must remain excluded from the standard blog query (`where('category', '!=', 'BASELINE_STUDY')`).
3. **Detail Page Resolution:** Opening an article categorized as `CERITA` must correctly display the "Cerita" badge on `BlogDetail.jsx`.

---

## 10. Questions Requiring Client Confirmation

Before proceeding to implementation in Phase 7B, the following decisions should be confirmed:

1. **Category "Cerita" Confirmation:**
   - Can we confirm the internal key will be `'CERITA'` with display label **"Cerita"** (Indonesian) and **"Stories"** (English)?
2. **Subtitle Update:**
   - Confirming that the subtitle in `lang/id/site.php` will be updated to:
     - ID: **"Berita dan Cerita"**
     - EN: **"News and Stories"**
   - Title remains **"Tova Blog"**.
3. **Background Photo Option (`Pertanyaan: memang tdk ada photo background kah`):**
   - Does the client want an admin upload field added to Site Settings (`blog_hero_image_url`) so they can upload a custom hero background for the Berita page (like About and Organization)?
   - *Recommendation:* Keep the current solid navy hero for Phase 7B unless explicitly requested, or add `blog_hero_image_url` to `site_settings` in a structured settings phase.

---

## 11. Proposed Implementation Plan (Phase 7B)

### Scope of Phase 7B
- **Files to Modify:**
  1. `lang/id/site.php`: Update `blog_subtitle` to `'Berita dan Cerita'`.
  2. `lang/en/site.php`: Update `blog_subtitle` to `'News and Stories'`.
  3. `resources/js/Pages/Blog.jsx`: Add `CERITA` to `CATEGORIES` and `CATEGORY_COLORS`.
  4. `resources/js/Pages/BlogDetail.jsx`: Add `CERITA` to `CATEGORY_COLORS` and `CATEGORY_LABELS`.
  5. `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php`: Add `'CERITA' => 'Cerita'` to category options.
  6. `app/Filament/Resources/BlogPosts/Tables/BlogPostsTable.php`: Add `'CERITA' => 'Cerita'` to table category filter options.
- **New Test Suite:**
  - Create `tests/Feature/BlogPageTest.php` covering:
    - HTTP 200 for `/blog` with expected Inertia props.
    - Category filtering for `CERITA`, `EDUKASI`, `BERITA`, etc.
    - Subtitle reflects updated translation.
    - HTTP 200 for published `/blog/{slug}` and HTTP 404 for draft `/blog/{slug}`.
    - Cover image and PDF attachment display logic.
    - Admin CRUD category selection for `CERITA`.
- **Database / Migration Impact:** **Zero**. No migration, no schema change, no data modification.

---

## 12. Safety Verification & Git Baseline

### 12.1 Git Status Check
- Git status verified before and after audit: zero application files modified during Phase 7A.
- Existing uncommitted changes across other phases remain preserved untouched.
- Read-only operations only: zero database writes, zero seeders executed.

### 12.2 Final Recommendation
**READY FOR CLIENT DECISION (PASS WITH ZERO BLOCKERS).**

All technical preconditions are satisfied. The database already supports the new category without any schema migration. Phase 7B can proceed immediately upon confirmation of the questions in Section 10.
