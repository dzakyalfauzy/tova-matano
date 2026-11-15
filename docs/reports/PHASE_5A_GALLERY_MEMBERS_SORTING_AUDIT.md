# PHASE 5A — Gallery, Members & Admin Sorting Audit

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 5A — Audit Only  
**Date:** 2026-11-13  
**Mode:** Read-only inspection, requirement analysis, implementation planning  
**Status:** Audit complete — awaiting approval

---

## 1. Executive Summary

This audit reviewed the three Phase 5 areas against `docs/revisi web.docx` and `docs/reports/TMCC_MASTER_REVISION_ROADMAP.md`.

### Gallery
- The public Gallery already loads items from the database, supports photos/videos, has a lightbox, and paginates 12 items per page.
- Captions exist for all 5 local gallery items, and all photo files exist.
- **Explicit client gap:** captions currently appear only on mouse hover or inside the lightbox. The client wants captions visible together with the image.
- **Confirmed latent bug:** pagination calls `router.visit(...)` but `router` is not imported in `Gallery.jsx`; pagination will throw a JavaScript error when more than 12 items create pagination links.
- No database change is needed to show existing captions.

### Members & Volunteers
- The public Members page already renders member photos when `photo_url` exists, with initials as fallback.
- Approved volunteers also render photos with initials as fallback.
- **Local verification is blocked by data:** the local application database contains **0 members and 0 volunteers**. The client's photo issue cannot be reproduced or confirmed locally.
- A public storage symlink exists, and gallery photo files resolve correctly.
- Admin photo upload fields exist for members and volunteers, but they have no explicit file-size or dimension limits.
- The `is_featured` member flag is present in the schema/admin, but it is not used by any public query despite a migration comment describing it as a way to exclude members from the portal.

### Admin Sorting
- `MembersTable` already has `name` as **searchable**, but not **sortable**.
- The `points` column is sortable and is the current default sort.
- The client explicitly asks for alphabetical name sorting. Adding `->sortable()` to the existing `name` column is the minimal, database-backed solution.
- No migration, data reorder, or permanent data change is needed.

### Overall
- Current automated baseline: **127 passed / 999 assertions**.
- No Gallery/Members public-page tests or member-sorting tests currently exist.
- No application file, database, `.env`, or production data was changed during this audit.
- **Recommendation:** proceed with Phase 5B and Phase 5D after approval; make Phase 5C data-first because the code path already renders photos and the local database cannot confirm the client's issue.

---

## 2. Source Document Findings

### 2.1 Client revision document

| Source section | Client text (verbatim) | Requirement type | Interpretation |
|---|---|---|---|
| `PAGE : GALERI` | `Tolong beri keterangan gambar yang tampil bersamaan gambar` | **Explicit requirement** | Gallery captions must be visible with the image, not only on hover or in the lightbox. |
| `PAGE : ANGGOTA DAN RELAWAN` — `MASUKAN#12` | `HANYA MAU MEMASTIKAN PHOTO ANGGOTA MUNCUL` | **Explicit requirement / QA request** | The client wants member photos to appear. This may be a data issue, a path/storage issue, or a display issue; the document does not identify the cause. |
| `ADMIN` | `Bisakah daftar anggota ini dipasangi filter di kolom NAMA bukan hanya di POINT untuk mempermudah mencari ybs kalaa ada mau melakukan perubahan di poinnya seseorang. Kalo pakai search saja susah mesti berkali-kali kalo banyak yg mau di edit. Tapi kalo sort alphabet lebih mudah mencarinya` | **Explicit requirement** | The client wants name-based lookup/sorting in the member admin list, not only point sorting. The phrase “filter di kolom NAMA” and “sort alphabet” support adding name sorting. |
| `ADMIN` | `Tong disusun seperti Home – About – Program – Water Q – Galeri - News - Org - Member - Partnership – contact` | Out of Phase 5 scope | Navigation order is not one of the three Phase 5 audit areas. Current `Navigation.jsx` already matches this order; no action proposed in 5A. |

### 2.2 Roadmap references

| Roadmap section | Current roadmap statement | Audit result |
|---|---|---|
| Section 4 — Anggota dan Relawan (#12) | `HANYA MAU MEMASTIKAN PHOTO ANGGOTA MUNCUL`; file terkait `Members.jsx`, `Member` model, `WebController`, Filament member resource; status “Perlu verifikasi foto”. | Confirmed. Code renders photos; local DB has no member/volunteer rows to verify. |
| Section 8 — Galeri | Caption only appears on hover; client wants caption “tampil bersamaan gambar”; files related `Gallery.jsx`, `GalleryItem` model/controller. | Confirmed. `Gallery.jsx:43-47` uses `opacity-0 group-hover:opacity-100`; lightbox caption exists. |
| Section 11 — Admin | Member list needs `name` `->sortable()`/`searchable()`; points already sortable; file `MembersTable.php`. | Confirmed. `name` is searchable only; `points` is sortable; default sort is points desc. |

---

## 3. Gallery Audit

### 3.1 Route, controller, and query

| Item | Finding |
|---|---|
| Public route | `GET /galeri` — `routes/web.php:21` |
| Controller | `WebController::gallery()` — `app/Http/Controllers/WebController.php:205-211` |
| Query | `GalleryItem::latest()->paginate(12)` — newest first, 12 per page |
| Inertia component | `Gallery` — `resources/js/Pages/Gallery.jsx` |
| Props | `items` paginator and `settings` |

No category, tag, featured, or custom ordering fields are used in the query.

### 3.2 Model and database

| Item | Finding |
|---|---|
| Model | `app/Models/GalleryItem.php` |
| Fillable | `type`, `url`, `caption` |
| Table | `gallery_items` |
| Schema | `id`, `type` enum `PHOTO/VIDEO`, `url` string, `caption` string nullable, timestamps |
| Migration | `database/migrations/2026_01_01_000012_create_gallery_items_table.php` |
| Indexes | Primary key only; no category/order index |
| Storage | Public disk, directory `gallery`; DB stores relative path or video URL |

### 3.3 Admin management

| Item | Finding |
|---|---|
| Resource | `app/Filament/Resources/GalleryItems/GalleryItemResource.php` |
| Form | `GalleryItemForm.php` — type selector, photo `FileUpload`, video URL `TextInput`, caption `TextInput` |
| Table | `GalleryItemsTable.php` — type, image/URL, caption, created/updated timestamps |
| CRUD | Create, edit, delete, bulk delete available |
| Photo upload | `->image()->disk('public')->directory('gallery')` — no explicit `maxSize()` or dimension validation |
| Video | External URL only; opens in new tab from lightbox |
| Default admin sort | `created_at desc` |
| Record title | Resource declares `recordTitleAttribute = 'title'`, but the model/table have no `title` attribute (only `caption`). Non-blocking admin UX inconsistency. |

### 3.4 Current local data

Read-only local database/storage inspection:

| Check | Result |
|---|---|
| Gallery items | **5 total** |
| Types | **5 PHOTO; 0 VIDEO** |
| Missing captions | **0** |
| Missing photo files | **0** |
| Photo formats | JPEG and WebP |
| File sizes | Approximately **41–261 KB** |
| Known dimensions | 600×400, 650×472, 1200×676, 1280×960; one WebP not decoded by the available Windows image API |
| Public storage symlink | `public/storage` is a junction to `storage/app/public` |

All 5 local captions are populated, so the client’s Gallery request can be implemented without a caption backfill.

### 3.5 Public UI behavior

| Behavior | Current implementation |
|---|---|
| Grid | 2 columns mobile, 3 at `md`, 4 at `lg` — `Gallery.jsx:24` |
| Photo rendering | `/storage/${item.url}` with caption as alt fallback — `:31-36` |
| Video rendering | Generic play tile; no thumbnail — `:37-42` |
| Caption | Bottom overlay, hidden by default, shown on hover — `:43-47`; `line-clamp-2` |
| Lightbox | Opens on tile click; caption shown below media — `:86-123` |
| Caption on mobile | Not visible in the grid; only after opening the lightbox |
| Pagination | Client-side `router.visit()` on Laravel paginator links — `:65-82` |
| Lazy loading | Not present on gallery images |
| Empty state | Present — `:58-63` |

### 3.6 Responsive assessment from source

- Grid is responsive at 2/3/4 columns.
- Images use `aspect-square` + `object-cover`, which preserves a uniform card grid but crops non-square photos.
- Caption overlay is `line-clamp-2`; long captions are truncated on hover.
- On touch devices, hover is not a reliable caption mechanism.
- No horizontal overflow is evident from source, but rendered mobile/tablet QA was not performed in this audit.

### 3.7 Confirmed Gallery defects and gaps

1. **Caption visibility — explicit client gap**  
   `Gallery.jsx:43-47` uses `opacity-0 group-hover:opacity-100`. The caption is hidden until hover; on touch/mobile it is not visible in the grid.

2. **Pagination runtime bug**  
   `Gallery.jsx:72` calls `router.visit(link.url)`, but `router` is not imported. The import at `Gallery.jsx:2` is `{ Head }` only. Pagination is hidden with the current 5 items, but becomes active when items exceed 12 and will fail at runtime.

3. **Image performance risk**  
   Gallery thumbnails use original files, not generated thumbnails, and have no lazy loading. Current files are modest (41–261 KB), but new uploads are not constrained.

4. **Upload validation gap**  
   `GalleryItemForm.php:28-35` uses `->image()` but no explicit `maxSize()` or `dimensions` rule. Very large images can enter public storage and be served in the grid.

5. **Photo/video field reuse**  
   Both photo and video inputs use the same `url` state. Changing an item’s type while editing can leave an incompatible value in the other input; validation may catch it, but the form has no separate media fields. Not part of the client request.

6. **Admin record title mismatch**  
   `GalleryItemResource.php:33` declares `title`, which does not exist. This does not break CRUD but weakens record-title behavior/global search labels.

---

## 4. Members & Volunteers Audit

### 4.1 Route, controller, and query

| Item | Finding |
|---|---|
| Public route | `GET /anggota` — `routes/web.php:14` |
| Controller | `WebController::members()` — `app/Http/Controllers/WebController.php:125-135` |
| Members query | `Member::orderByDesc('points')->get()` — all members, ranked by points |
| Volunteers query | `Volunteer::where('status', 'APPROVED')->select(['id', 'full_name', 'photo_url', 'submitted_at'])->orderByDesc('submitted_at')->get()` |
| Component | `resources/js/Pages/Members.jsx` |
| Pagination | None on the public Members page |

### 4.2 Models and schema

| Model | Table | Key fields | Notes |
|---|---|---|---|
| `Member` | `members` | `name`, `photo_url`, `divisi_name`, `points`, `message`, `joined_at`, `is_featured` | No email, status, or unique constraint. `points` index exists. |
| `Volunteer` | `volunteers` | `registration_code`, `full_name`, `email`, `phone`, `status`, `photo_url`, `quote`, `quote_consent`, review timestamps | Status enum `PENDING/APPROVED/REJECTED`; email is not unique; photo upload is admin-only. |

Migrations:
- `database/migrations/2026_01_01_000007_create_members_table.php`
- `database/migrations/2026_01_01_000008_create_volunteers_table.php`
- `database/migrations/2026_11_02_055318_add_quote_and_quote_consent_to_volunteers_table.php`

### 4.3 Photo rendering

| Area | Current behavior |
|---|---|
| Member photo | `member.photo_url ? <img src={/storage/${member.photo_url}} ... /> : initial` — `Members.jsx:138-149` |
| Volunteer photo | `volunteer.photo_url ? <img src={/storage/${volunteer.photo_url}} ... /> : initial` — `Members.jsx:233-244` |
| Fallback | Initial letter shown when `photo_url` is empty; this can mask missing photo data |
| Alt text | Member `alt={member.name}`, volunteer `alt={volunteer.full_name}` |
| Image sizing | Member 48×48 avatar; volunteer 48×48 avatar |
| Lazy loading | Not used |

**Conclusion:** the code already has a working photo path and fallback. If a member photo is missing on the live site, the most likely causes are:
1. `photo_url` is empty in the database,
2. the stored file is missing from `storage/app/public`,
3. the stored path is malformed (absolute path, `public/` prefix, legacy path),
4. browser cache/deployment storage issue, or
5. the record is not in the expected table/status.

### 4.4 Current local data

| Check | Result |
|---|---|
| Members in local DB | **0** |
| Volunteers in local DB | **0** |
| Approved volunteers | **0** |
| Public storage symlink | Exists and targets `storage/app/public` |
| `members` storage directory | Contains **1 orphaned PNG**: 6000×3375, approximately **3.9 MB**, with no matching DB row |
| `volunteers` storage directory | Not present |

The local database cannot confirm the client’s photo issue. Production/staging data or an admin data export is required. No production access was performed.

### 4.5 Admin management and uploads

| Item | Finding |
|---|---|
| Member resource | `MemberResource.php`; form `MemberForm.php`; table `MembersTable.php` |
| Member photo field | `FileUpload::make('photo_url')->image()->disk('public')->directory('members')` — no max size/dimensions |
| Member fields | name, division, photo, points, joined date, message, featured toggle |
| Volunteer resource | `VolunteerResource.php`; form `VolunteerForm.php`; table `VolunteersTable.php` |
| Volunteer photo field | `FileUpload::make('photo_url')->image()->disk('public')->directory('volunteers')` — no max size/dimensions |
| Public volunteer registration | `WebController::submitVolunteer()` creates volunteer data but does **not** accept/upload a photo; photos must be added by admin |
| Volunteer review | `status` field with pending/approved/rejected options |
| Member status | No active/inactive field; only `is_featured` |

### 4.6 Status and filtering behavior

| Behavior | Finding |
|---|---|
| Public volunteers | Only `APPROVED` records are exposed. |
| Public members | All members are exposed; `is_featured` is not applied in `WebController::members()`. |
| `is_featured` model comment | Migration says “exclude dari portal tanpa hapus data”, but no public query uses the flag. |
| Admin member filter | `TernaryFilter('is_featured')` exists in `MembersTable`; it filters the admin list only. |
| Duplicate people | Members and volunteers are separate tables with no relation or uniqueness rule. The same person can appear in both sections. |

### 4.7 Responsive and content behavior

- Member list uses a horizontal row: rank, 48px avatar, name/division/message, points badge.
- At 375px, the row is dense; truncation helps, but visual QA is needed.
- Volunteer section uses 1/2/3-column cards at mobile/tablet/desktop.
- Both pages use hardcoded Indonesian copy (`Head` titles, headings, labels, empty states).
- Navigation labels come from `lang/{id,en}/site.php`, but Gallery/Members body content has no translation keys. Switching to English translates the nav, not these page bodies.
- This is existing behavior, not an explicit Phase 5 request. It needs confirmation if localization is in scope.

### 4.8 Existing tests

- **No public Gallery or Members page tests exist.**
- `tests/Feature/VolunteerQuoteTest.php` covers volunteer submission and volunteer admin table quote behavior.
- `tests/Feature/FilamentCrudRedirectTest.php` covers create/edit redirects for Member/Volunteer/Gallery resources and volunteer list delete behavior.
- No test covers member photo rendering, volunteer photo rendering, `is_featured` visibility, or member table name sorting.

---

## 5. Admin Sorting Audit

### 5.1 Relevant resource and table

| Item | Finding |
|---|---|
| Resource | `app/Filament/Resources/Members/MemberResource.php` |
| Table | `app/Filament/Resources/Members/Tables/MembersTable.php` |
| Page | `app/Filament/Resources/Members/Pages/ListMembers.php` |
| Model | `app/Models/Member.php` |
| Database table | `members` |
| Name field | `name` (single full-name string) |
| Points field | `points` integer, indexed |

### 5.2 Current sorting/searching state

`MembersTable.php`:

```php
TextColumn::make('name')
    ->label('Nama')
    ->searchable(),

TextColumn::make('points')
    ->label('Poin')
    ->badge()
    ->numeric()
    ->sortable(),

// defaultSort('points', 'desc')
```

| Check | Result |
|---|---|
| Name column visible | Yes |
| Name searchable | Yes — database-backed Filament search |
| Name sortable | **No** |
| Points sortable | Yes |
| Default sort | Points descending |
| Ascending/descending name sort | Not available |
| Sorting layer | Not applicable until `sortable()` is added; Filament `sortable()` uses a database `orderBy` |
| Search + pagination compatibility | Filament standard; must be regression-tested after adding sort |
| Permanent data reorder | Not needed; sorting is query/UI state only |
| Schema change needed | No |

### 5.3 Interpretation of the client request

The client says “filter di kolom NAMA bukan hanya di POINT” and “sort alphabet lebih mudah mencarinya”.

- Existing search already finds names, but the client finds repeated searching inconvenient.
- The roadmap interprets the request as `name` `->sortable()`/`searchable()`.
- Recommended minimal implementation: add `->sortable()` to the existing `name` column and keep `->searchable()`.
- No custom filter UI is required for the explicit “sort alphabet” outcome.
- If the client literally wants a filter control (not just sorting), that is a separate UI decision and needs confirmation.

### 5.4 Risk assessment

- Query-only sorting has low risk to CRUD and existing data.
- Default points sorting should remain unchanged until the user clicks the name column.
- Sorting should work with existing search and pagination through Filament’s query string.
- No migration is needed. A `name` index is not necessary for the current dataset; adding one would be an optional performance decision, not a Phase 5 requirement.
- Volunteers (`full_name`) are searchable but not sortable. The client’s request names “daftar anggota”; extending sorting to volunteers is optional and needs confirmation.

---

## 6. Requirements vs Existing Implementation Matrix

| Area | Client Requirement | Existing Implementation | Gap | Recommendation |
|---|---|---|---|---|
| Gallery | Caption displayed together with image (`PAGE: GALERI`) | Caption exists in DB; displayed on hover (`Gallery.jsx:43-47`) and in lightbox (`:112-114`) | Caption not permanently visible in grid; not touch-friendly | Show caption permanently below each image/card; keep lightbox caption. Confirm below-image vs always-on overlay. |
| Gallery | Existing gallery should continue to work | Grid, lightbox, photo/video types, pagination, admin CRUD | Pagination has a latent missing `router` import (`Gallery.jsx:72`) | Fix `router` import in 5B; add pagination regression test. |
| Gallery | Image performance and safety | Public disk; `image()` validation; no explicit size/dimension limits; full-size images in grid; no lazy loading | Large uploads possible; no thumbnail pipeline | Add optional upload guard/optimization only if approved; at minimum document admin guidance. |
| Gallery | Categories/filters/pagination | Pagination 12; no categories/filters | No category/filter feature | Not requested; do not add unless separately confirmed. |
| Members | Ensure member photos appear (`MASUKAN#12`) | `member.photo_url` rendered with `/storage/...`; initials fallback (`Members.jsx:138-149`) | Local DB has 0 members; cannot verify live data; fallback can mask empty data | Data-first audit: check all production member `photo_url` values and files; upload missing photos via existing admin. Code path already works. |
| Members | Volunteer photos on same page | Approved volunteers expose `photo_url`; initials fallback (`Members.jsx:233-244`) | Local DB has 0 approved volunteers; volunteer photo upload is admin-only | Confirm whether client means members only or also volunteers; verify admin-uploaded volunteer photos. |
| Members | Status/visibility | Volunteers filtered to `APPROVED`; members expose all; `is_featured` not used publicly | Possible mismatch between `is_featured` comment and behavior | Confirm whether `is_featured` should hide members from public; do not change without approval. |
| Members | Responsive layout | Member rows + volunteer 1/2/3 grid | No browser visual QA in this audit | Manual 375/768/1280 checks in 5C. |
| Members | Indonesian/English consistency | Page bodies hardcoded Indonesian; nav translated | No body translation keys | Confirm whether 5C should add EN content; not explicit in client document. |
| Members | No duplicate data | Separate members/volunteers tables; no relation/unique rule | Same person can appear twice | Confirm whether deduplication or cross-linking is required; not explicit. |
| Admin Sorting | Sort member list alphabetically by name (`ADMIN`) | `name` searchable only; `points` sortable; default points desc | Name sort missing | Add `->sortable()` to `name`; keep search and default points sort. |
| Admin Sorting | Keep search useful | Name search already database-backed | Search exists but sorting absent | Keep searchable; test search + sort + pagination. |
| Admin Sorting | Do not reorder stored data | No permanent reorder currently | None | Use query-layer `sortable()`; no data mutation. |
| Admin Sorting | Avoid schema change if possible | Sort can use existing `name` column | None | No migration proposed. |
| Admin Sorting | Volunteer sorting? | `full_name` searchable, not sortable | Not requested | Optional consistency item; needs confirmation. |

### Label key
- **Explicit requirement:** directly requested in `docs/revisi web.docx`.
- **Existing behavior:** verified from code/local data.
- **Technical recommendation:** audit-derived solution, not a client quote.
- **Needs confirmation:** cannot be decided from source documents or local data.

---

## 7. Technical Risks

| Risk | Area | Severity | Impact | Mitigation |
|---|---|---|---|---|
| Missing `router` import | Gallery | **Medium** | Pagination throws `ReferenceError` when gallery exceeds 12 items | Import `router` from `@inertiajs/react` or use `<Link>` for pagination in 5B. |
| Hover-only caption | Gallery | **Medium** | Client requirement unmet; captions inaccessible on touch devices | Render caption permanently; keep lightbox. |
| Full-size images/no thumbnails | Gallery, Members | **Medium** | Slow mobile loads, bandwidth, layout risk as data grows; local member upload is 6000×3375 at ~3.9 MB | Optional upload limits and image optimization; add `loading="lazy"`; do not add dependencies without approval. |
| Local Members/Volunteers data is empty | Members | **High uncertainty** | Cannot confirm the client’s photo complaint from local DB | Obtain production/staging data or ask admin to audit `photo_url` and files. |
| `is_featured` not used publicly | Members | **Medium** | Admin toggle may appear to have no effect on public visibility | Clarify intended semantics; if needed, apply filter in `WebController::members()`. |
| No relation/uniqueness between members and volunteers | Members | **Low–Medium** | Duplicate person can appear twice; no identity integrity | Confirm whether deduplication/linking is required; no schema change in 5C unless approved. |
| No Gallery/Members public-page tests | Gallery, Members | **Medium** | Regressions in captions/photos can go unnoticed | Add focused feature/source tests in 5B/5C. |
| No member sorting test | Admin Sorting | **Low** | Regression could break sorting/search/pagination | Add Filament Livewire test for asc/desc/search/pagination/default sort. |
| Hardcoded Indonesian body copy | Gallery, Members | **Low–Medium** | English switch only translates navigation | Confirm whether EN translation is in Phase 5 scope; otherwise document as existing behavior. |
| `recordTitleAttribute` mismatches | Admin | **Low** | Weak record labels/global search for Gallery/Volunteer resources | Optional cleanup; not part of explicit Phase 5 request. |
| No upload size/dimension guard | Gallery, Members, Volunteers | **Medium** | Very large public files; possible disk/browser performance issues | Optional additive hardening; confirm limits before changing admin behavior. |
| Same `url` state for photo/video | Gallery admin | **Low** | Type switching can produce invalid cross-type values | Keep separate fields or add explicit normalization if editing UX is addressed. |

---

## 8. Open Questions / Needs Confirmation

1. **Gallery caption placement:** Should captions appear directly below each image, or as an always-visible overlay on the image? Recommendation: below image for readability and mobile accessibility.
2. **Gallery pagination fix:** The missing `router` import is a confirmed technical bug. Include the fix in 5B even though it is not explicitly in the client text? Recommendation: yes, because it protects the Gallery feature.
3. **Member photo issue:** Is the client seeing missing photos because `photo_url` is empty, files are missing, or records are absent? Local DB has 0 members/volunteers, so production/staging data is needed.
4. **Member `is_featured`:** Should non-featured members be hidden from `/anggota`? The migration comment says “exclude dari portal”, but the controller currently loads all members.
5. **Volunteer photos:** Does “photo anggota” include volunteers, or only core members? Volunteer photos can currently only be added by admin.
6. **Admin “filter NAMA”:** Is alphabetical sorting enough, or does the client expect an actual filter control in the NAMA column? Recommendation: add `sortable()` plus existing search; no custom filter unless confirmed.
7. **Volunteer sorting:** Should the volunteer admin table also sort by `full_name`? Not requested in the client document; roadmap focuses on members.
8. **Localization:** Should Gallery/Members body text be translated to English in Phase 5, or is Indonesian-only acceptable? Existing pages are hardcoded Indonesian.
9. **Image limits/optimization:** Should new uploads be limited by file size/dimensions or converted to thumbnails? The local member upload is 6000×3375 at ~3.9 MB; current forms have no limit.
10. **Duplicate identities:** Should a person who exists in both `members` and `volunteers` be merged, linked, or shown twice? No relation currently exists.

---

## 9. Proposed Implementation Roadmap (5B–5D)

### Phase 5B — Gallery

**1. Tujuan**  
Make gallery captions permanently visible with the image while preserving the existing grid and lightbox, and fix the latent pagination runtime bug.

**2. Files likely to change**
- `resources/js/Pages/Gallery.jsx` — caption layout; import `router` from `@inertiajs/react`.
- `tests/Feature/GalleryPageTest.php` — new focused test.
- Optional: no controller/model/admin changes.

**3. Database impact**  
None. `gallery_items.caption` already exists; all 5 local items have captions. No migration/backfill.

**4. Backend impact**  
None required. Existing `GalleryItem::latest()->paginate(12)` can remain.

**5. Frontend impact**  
Replace hover-only caption overlay with a permanently visible caption area; preserve image aspect ratio, lightbox, photo/video handling, and responsive 2/3/4 grid. Consider `loading="lazy"` as an optional low-risk improvement.

**6. Admin Panel impact**  
None required. Existing Gallery CRUD remains unchanged.

**7. Risks to existing data**  
None; no data changes. UI-only change.

**8. Test cases required**
- `/galeri` returns Gallery component with paginated items.
- Existing captions are present in Inertia props and rendered in source/component structure.
- Pagination works with >12 items; no `router` reference error.
- Empty state still renders.
- Caption remains visible in lightbox.
- Source-level assertion for caption visibility (since no JS browser runner is installed).

**9. Acceptance criteria**
- Caption is visible with every captioned item without hover, on mobile/tablet/desktop.
- Image remains uncropped or intentionally framed; no layout break.
- Pagination works after the 12th item.
- Lightbox still opens, displays media, and shows caption.
- No database/schema change; full suite/build/Pint pass.

**10. Dependencies / confirmation**
- Confirm caption placement (below image vs overlay).
- Approve fixing the `router` import as part of 5B.

---

### Phase 5C — Members & Volunteers

**1. Tujuan**  
Ensure member and approved volunteer photos appear reliably, verify the actual data/storage issue, and preserve existing member/volunteer behavior.

**2. Files likely to change**
- `resources/js/Pages/Members.jsx` — only if the fallback/layout/photo presentation needs adjustment.
- `app/Http/Controllers/WebController.php` — only if `is_featured` visibility or query behavior is approved.
- `app/Filament/Resources/Members/Schemas/MemberForm.php` / `Volunteers/Schemas/VolunteerForm.php` — only if upload limits/optimization are approved.
- `tests/Feature/MembersPageTest.php` — new focused test.
- No migration expected.

**3. Database impact**  
None for displaying existing photos. `photo_url` is already nullable on both tables. If `is_featured` semantics change, no schema change is required. No data migration.

**4. Backend impact**  
Potentially none. Existing `WebController::members()` already exposes members and approved volunteers with photo URLs. Query changes only if `is_featured` or ordering is confirmed.

**5. Frontend impact**  
Verify `/storage/${photo_url}` paths, fallback initials, avatar sizing, and responsive layouts. Do not redesign the page unless requested.

**6. Admin Panel impact**  
Existing member/volunteer CRUD and photo upload remain. Optional: add upload size/dimension guard, but this can reject existing workflows and needs approval.

**7. Risks to existing data**  
Low if the phase is data-only. Uploading/replacing photos must be done by admin; do not bulk-update records without approval. No existing photos should be deleted.

**8. Test cases required**
- Public `/anggota` returns Member/Volunteer props.
- Member with `photo_url` exposes the expected path; member without photo gets fallback.
- Approved volunteer is included; PENDING/REJECTED volunteer is excluded.
- Source test confirms `/storage/` URL construction and fallback branch.
- Optional: `is_featured` behavior if confirmed.
- Manual browser check at 375/768/1280.

**9. Acceptance criteria**
- Every member intended for public display has a visible photo or intentional fallback.
- Approved volunteer photos appear; unapproved volunteers do not.
- No data loss or unauthorized data changes.
- Responsive layout remains intact.
- Full suite/build/Pint pass.

**10. Dependencies / confirmation**
- Production/staging member and volunteer data audit.
- Decision on `is_featured` public visibility.
- Decision on volunteer photo scope.
- Decision on English localization.
- Approval before any upload limit/optimization change.

---

### Phase 5D — Admin Sorting by Name

**1. Tujuan**  
Add alphabetical name sorting to the member admin list without changing stored data.

**2. Files likely to change**
- `app/Filament/Resources/Members/Tables/MembersTable.php` — add `->sortable()` to `name`.
- `tests/Feature/MemberTableSortingTest.php` — new focused test.
- No controller/model/migration changes.

**3. Database impact**  
None. Sorting uses the existing `name` column in the `members` table. No data reorder, no index required for the current dataset.

**4. Backend impact**  
Filament generates the database `orderBy` when the column is sortable. Search and pagination remain query-string driven.

**5. Frontend impact**  
Filament table header becomes clickable with ascending/descending indicators. Default points sort remains until the user chooses name sort.

**6. Admin Panel impact**  
Member list gains name sorting while retaining search, points sort, filters, export, and CRUD.

**7. Risks to existing data**  
None. No records are updated or reordered permanently.

**8. Test cases required**
- Name ascending order with records created out of alphabetical order.
- Name descending order.
- Default points descending behavior remains when no name sort is selected.
- Search + name sort still returns matching records in order.
- Pagination with sorted results.
- Existing member CRUD/delete/export tests still pass.

**9. Acceptance criteria**
- NAMA column toggles asc/desc and orders by database `name`.
- POINT sorting remains available.
- Search, pagination, filters, CRUD, and export behave normally.
- No data mutation; full suite/build/Pint pass.

**10. Dependencies / confirmation**
- Confirm whether “filter NAMA” means sort only or an additional filter control.
- Optional: confirm whether volunteers should also get `full_name` sorting.

---

## 10. Files Likely to Change

| File | Phase | Likely change | Notes |
|---|---|---|---|
| `resources/js/Pages/Gallery.jsx` | 5B | Caption layout; import `router` | Primary Gallery UI fix |
| `resources/js/Pages/Members.jsx` | 5C | Only if fallback/photo presentation changes | Code already renders photos |
| `app/Http/Controllers/WebController.php` | 5C | Only if `is_featured`/query behavior approved | Members query at `:125-135`; Gallery query at `:205-211` |
| `app/Filament/Resources/Members/Tables/MembersTable.php` | 5D | Add `->sortable()` to `name` | Minimal one-line change |
| `app/Filament/Resources/Members/Schemas/MemberForm.php` | 5C optional | Upload limits | Not required for photo visibility |
| `app/Filament/Resources/Volunteers/Schemas/VolunteerForm.php` | 5C optional | Upload limits | Not required for photo visibility |
| `tests/Feature/GalleryPageTest.php` | 5B | New | Public Gallery + pagination |
| `tests/Feature/MembersPageTest.php` | 5C | New | Member/volunteer photo props |
| `tests/Feature/MemberTableSortingTest.php` | 5D | New | Filament name sorting |
| `resources/js/Pages/Gallery.jsx` source test | 5B | New assertion | Caption visibility/router import |

### Explicitly not proposed
- No migration for Gallery, Members, Volunteers, or sorting.
- No change to `gallery_items`, `members`, or `volunteers` schema.
- No database reorder or bulk data update.
- No new image-processing dependency unless separately approved.

---

## 11. Database Impact Assessment

| Area | Schema impact | Data impact | Migration |
|---|---|---|---|
| Gallery caption display | None — `caption` already exists | None; 5/5 local captions populated | **No** |
| Gallery pagination fix | None | None | **No** |
| Gallery upload limits | None if using validation only | None; may reject future oversized uploads | **No** |
| Member photo display | None — `photo_url` already exists | Existing photo data must be audited; no automatic changes | **No** |
| Volunteer photo display | None — `photo_url` already exists | Approved volunteer data must be audited | **No** |
| Member `is_featured` visibility | None | Query behavior change only, if approved | **No** |
| Admin name sorting | None — `name` already exists | None; query-only ordering | **No** |
| Volunteer name sorting (optional) | None — `full_name` already exists | None | **No** |

**Database conclusion:** all three Phase 5 areas can be implemented without a migration. Any data correction must be performed through admin UI or an explicitly approved data operation, not an automatic migration.

---

## 12. Testing Strategy

### 12.1 Current baseline

- Full suite: **127 passed / 999 assertions**.
- Build: previously passing; must be rerun after any 5B–5D implementation.
- Pint: must be run after any PHP change.
- No tests currently cover public Gallery, public Members, member photo rendering, or member name sorting.

### 12.2 New tests

**Gallery — `tests/Feature/GalleryPageTest.php`**
- `/galeri` returns 200 and `Gallery` component.
- Inertia props expose 5 local/test items and captions.
- Pagination with 13 items returns 12 on page 1 and 1 on page 2.
- Source test confirms `router` is imported and pagination uses it.
- Source/component test confirms captions are not exclusively hover-driven (`opacity-0 group-hover...` removed or superseded).
- Empty state test.

**Members — `tests/Feature/MembersPageTest.php`**
- `/anggota` returns 200 and `Members` component.
- Member with `photo_url` exposes the expected path.
- Member without photo has a fallback path.
- Approved volunteer included; PENDING/REJECTED excluded.
- Source test confirms `/storage/${...}` and fallback branch.
- Optional `is_featured` behavior test if confirmed.

**Member sorting — `tests/Feature/MemberTableSortingTest.php`**
- Acting as admin, set Filament panel.
- Create members with names out of alphabetical order and differing points.
- `Livewire::test(ListMembers::class)->sortTable('name', 'asc')->assertCanSeeTableRecords([...], inOrder: true)`.
- Repeat descending.
- Assert default points-desc order when no name sort is selected.
- Assert search + sort + pagination still work.
- Assert no database row order/data changes.

### 12.3 Manual QA required

- Gallery at 375/768/1280: caption visible without hover, no horizontal scroll, images not broken.
- Members at 375/768/1280: avatars, fallback initials, volunteer cards, no overflow.
- Admin: click NAMA header; verify asc/desc indicators, search, pagination, export, edit/delete.

### 12.4 Commands

```text
php artisan test tests/Feature/GalleryPageTest.php --compact
php artisan test tests/Feature/MembersPageTest.php --compact
php artisan test tests/Feature/MemberTableSortingTest.php --compact
php artisan test --compact
npm run build
vendor/bin/pint --dirty --format agent
```

Browser automation is not installed in this project; no browser test is assumed.

---

## 13. Acceptance Criteria

### Gallery
- [ ] Captions are visible with images without hover.
- [ ] Captions remain readable on mobile.
- [ ] Lightbox still works and shows the caption.
- [ ] Pagination works when items exceed 12.
- [ ] Existing Gallery CRUD remains functional.
- [ ] No schema/data migration.

### Members & Volunteers
- [ ] All intended members have a working photo or intentional fallback.
- [ ] Approved volunteers with photos render correctly.
- [ ] Unapproved volunteers do not appear publicly.
- [ ] Photo paths resolve through `public/storage`.
- [ ] Layout remains responsive.
- [ ] No existing member/volunteer data is lost or silently changed.

### Admin Sorting
- [ ] NAMA column sorts ascending and descending at the database layer.
- [ ] Search still works with sorting.
- [ ] Pagination preserves sorting.
- [ ] POINT sorting remains available and default behavior is unchanged.
- [ ] No stored row order/data is changed.
- [ ] CRUD/delete/export still pass.

### Cross-cutting
- [ ] Full test suite passes.
- [ ] `npm run build` passes.
- [ ] Pint passes.
- [ ] No `.env`, production, deployment, commit, or destructive Git action.
- [ ] Existing uncommitted local changes remain intact.

---

## 14. Final Recommendation

**Proceed after explicit approval, with this order:**

1. **Phase 5D — Admin Sorting** — smallest, lowest-risk change: add `->sortable()` to `MembersTable` name column and add a Filament sorting test.
2. **Phase 5B — Gallery** — fix caption visibility and the missing `router` import; add Gallery feature/source tests.
3. **Phase 5C — Members & Volunteers** — data-first verification. The code already renders photos; confirm production/staging `photo_url` and file existence before changing application logic. Regional/localization and `is_featured` decisions require confirmation.

**No migration is recommended for Phase 5B, 5C, or 5D.**

**Scope compliance:** this audit created only `docs/reports/PHASE_5A_GALLERY_MEMBERS_SORTING_AUDIT.md`; no application file, database, `.env`, production resource, or deployment was changed. Existing local/uncommitted changes were preserved.

**STOP.** Do not start Phase 5B, 5C, or 5D until approved.
