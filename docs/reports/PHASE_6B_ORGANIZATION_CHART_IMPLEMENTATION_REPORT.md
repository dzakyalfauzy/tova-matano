# PHASE 6B — ORGANIZATION CHART IMAGE INTEGRATION REPORT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 6B — Implementation (Option B: Admin-Uploaded Official Organization Chart)  
**Date:** November 14, 2026  
**Reference Audit:** `docs/reports/PHASE_6A_ORGANIZATION_CHART_READINESS_AUDIT.md`  
**Client Source:** `docs/revisi web.docx`

---

## 1. Approved Architecture Decisions

1. **Option B selected:** the public organization chart is an official image uploaded by Admin through Site Settings.
2. **Existing six categories are not final:** category names, count, and hierarchy must remain fully dynamic and Admin-managed. No category is hardcoded, renamed, deleted, or re-seeded.
3. **Full replacement:** the uploaded chart image replaces the old hardcoded public chart. The old chart is not retained as a fallback.

---

## 2. Pre-Implementation Inspection (Verified, Not Assumed)

| Check | Verified Finding |
|---|---|
| `org_chart_image_url` passed to the page | `WebController::organization()` passes `$this->settings()`, which returns `SiteSettings::first()?->makeHidden(['about_content'])`. `org_chart_image_url` is a normal column and is included automatically. Confirmed. |
| Filament field storage target | `FileUpload::make('org_chart_image_url')` uses `->disk('public')->directory('settings')` → `storage/app/public/settings`. Confirmed. |
| Database value format | Relative disk path (e.g. `settings/<ulid>.png`). `org_chart_image_url` is currently `NULL`. Confirmed. |
| Replacement/deletion behavior | Filament `BaseFileUpload` stores new files during state dehydration and does **not** delete previous files. The existing image is therefore never deleted on a failed upload or failed DB save. Preserved. |
| Upload preview / file types | Existing field had `->image()` only (no explicit accepted types, size limit, preview height). Improved minimally (see §7). |
| Frontend URL convention | `/storage/{relative-path}` is the project convention (`WaterQuality.jsx`, `Gallery.jsx`, `Members.jsx`). Confirmed and reused. |
| Organization tests | No `OrganizationPageTest.php` existed. Filament CRUD redirect behavior is covered in `tests/Feature/FilamentCrudRedirectTest.php`. |
| JS test framework | None installed (`package.json` has only `vite build`). Lightbox behavior therefore cannot be asserted by automated browser tests in this repo. |
| Git baseline | 13 modified + 14 untracked files preserved untouched except the files listed in §3. |

---

## 3. Files Changed

| File | Status | Reason |
|---|---|---|
| `resources/js/Pages/Organization.jsx` | Modified | Removed the hardcoded tree chart; added DB-image chart rendering, accessible lightbox, and a neutral no-image state. |
| `app/Filament/Pages/ManageSiteSettings.php` | Modified (one field) | Hardened the existing `org_chart_image_url` upload field: helper text, explicit raster types, 5 MB limit, public visibility, path-tampering guard, preview height, open/download, full width. No new field, no disk/directory change. |
| `tests/Feature/OrganizationPageTest.php` | New | Phase 6B regression coverage (8 tests / 120 assertions). |
| `docs/reports/PHASE_6B_ORGANIZATION_CHART_IMPLEMENTATION_REPORT.md` | New | This report. |

No other application code was modified. No migrations, seeders, `.env`, or database records were touched.

---

## 4. Hardcoded Chart Removal

Removed from `Organization.jsx`:

- The entire static `{/* Static Organization Chart */}` block (7 tiers, all connector lines, all position boxes).
- Hardcoded nodes: `PELINDUNG`, `PENASEHAT`, `PENGARAH`, `Badan Penelitian dan Pengembangan Organisasi`, `Dewan Eksekutif Organisasi`, `Dewan Pakar`, `Direktur Utama TMCC`, `Sekretaris Jenderal`, `Direktur Operasional`, `Direktur Eksternal Relation`, `Direktur Keuangan`, all six hardcoded divisions, and the `ANGGOTA DAN SUKARELAWAN` banner tied to the old chart.
- No hardcoded chart content was retained as a fallback.

Approximately 160 lines of hardcoded chart markup were replaced with ~75 lines of image/lightbox rendering.

---

## 5. Image Rendering Implementation

- Resolves `const chartImageUrl = settings?.org_chart_image_url;`.
- Renders `<img src={`/storage/${chartImageUrl}`}>` inside a responsive `max-w-5xl` figure.
- `object-contain` (never `object-cover`), `w-full h-auto`, `max-h-[75vh]` → no cropping and the full chart fits the viewport.
- `loading="lazy"`, descriptive Indonesian alt text.
- Wrapped in a `<button>` with `aria-label="Perbesar bagan organisasi"`, hover "Perbesar" affordance, and keyboard focus ring.
- TMCC visual language preserved: `brand-paper` panel, `brand-navy`/`brand-teal` accents, rounded-2xl border/shadow.
- Caption: “Klik gambar untuk memperbesar bagan organisasi.”

---

## 6. Lightbox Behavior

Implemented in-component with `useState`/`useEffect`; **no new dependency**.

| Requirement | Implementation |
|---|---|
| Click to expand | Chart button sets `isChartOpen = true`. |
| Visible close button | Top-right `h-11 w-11` circular button with `X` icon, `aria-label="Tutup bagan organisasi"`. |
| Click outside closes | Backdrop `onClick` closes; image `onClick` calls `stopPropagation()`. |
| Escape closes | `keydown` listener checked for `event.key === 'Escape'`. |
| Background scroll locked | `document.body.style.overflow = 'hidden'` while open; previous value restored on cleanup. |
| Contained in viewport | `max-h-[90vh] max-w-full object-contain`. |
| Accessible labels | Close button and enlarge button both have explicit `aria-label`s; container uses `role="dialog"` + `aria-modal="true"`. |
| Comfortable mobile close | 44×44 px close target, high-contrast `bg-white/10` on black, top-right within safe padding. |

---

## 7. Admin Upload Field Verification / Changes

The existing field was kept (no duplicate field, same `public` disk, same `settings` directory). Minimal hardening applied:

- `->helperText(...)` explaining it appears on the public Struktur Organisasi page, accepted formats, 5 MB limit, and that leaving it empty hides the chart.
- `->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])` — raster only; **SVG deliberately not enabled**.
- `->maxSize(5120)` (5 MB).
- `->visibility('public')` to make the stored path web-accessible like sibling uploads.
- `->preventFilePathTampering()` (consistent with the water-quality map upload field).
- `->imagePreviewHeight('240')`, `->openable()`, `->downloadable()`, `->columnSpanFull()` for preview and full-width usability.

Confirmed: no save/replacement/deletion logic was added or altered; the existing image is not deleted on failed upload or failed database save (Filament stores new files without deleting prior ones).

---

## 8. Dynamic Accordion Preservation

- Accordion still renders exclusively from the `categories` prop (`OrgCategory::with('people')`), no hardcoded names or counts.
- `ROLE_LIST` and `PERSON_GRID` rendering, photo/initials fallback, `divisi_name` display, and empty states are unchanged.
- Admin CRUD and drag-and-drop `order` behavior untouched.
- Changing category records in Admin immediately changes the public payload; verified by an end-to-end Livewire create + edit test.

---

## 9. Automated Test Results

`php artisan test tests/Feature/OrganizationPageTest.php --compact`

```
tests: 8 passed | assertions: 120
```

Coverage:

1. `/struktur-organisasi` returns 200 and the `Organization` Inertia component.
2. `settings.org_chart_image_url` is passed when populated.
3. Missing `SiteSettings` record → `settings` prop is `null`, `categories` empty, no error.
4. Categories returned with nested people in configured `order`.
5. Admin create + edit of a category via Filament `Livewire::test` is reflected in the public payload (and old name disappears).
6. Component source renders `/storage/${chartImageUrl}` with `object-contain` and `loading="lazy"`, and contains none of the removed hardcoded chart nodes.
7. Source contains the accessible, dependency-free lightbox markers (`role="dialog"`, `aria-modal`, Escape handler, scroll lock, `max-h-[90vh]`).
8. No-image state: `org_chart_image_url` is `null`, accordion category still returned, neutral message present.

Note: tests 6–8 are structural source assertions, **not** browser interaction tests. No JS test framework exists in this project.

---

## 10. Full Test Suite Results

```
php artisan test --compact
tests: 154 passed | assertions: 1321
```

Baseline before this phase: 146 passed / 1201 assertions. Phase 6B added 8 tests / 120 assertions, with zero regressions.

---

## 11. Pint and Build Results

- `vendor/bin/pint --dirty --format agent` → **passed**.
- `npm run build` → **successful** (Vite 8.3.1, `Organization` chunk 25.76 kB, built in 8.68 s). Only pre-existing Vite/esbuild deprecation warnings and the existing `/image/pattern.svg` runtime-resolution notice.

---

## 12. Database / Data Impact

- No migration, schema change, seeder, or record modification.
- Verified after implementation: `org_chart_image_url = null`, 6 `org_categories`, 14 `org_people` (unchanged).
- No dummy chart was uploaded; the existing setting was not overwritten.

---

## 13. Manual Visual QA Checklist (NOT YET PERFORMED)

Browser-level visual QA was **not** executed in this environment. To be performed by the user:

**Desktop — 1280px**
- [ ] Uploaded chart displays fully without cropping (object-contain).
- [ ] Aspect ratio preserved; panel centered at `max-w-5xl`.
- [ ] Hover shows “Perbesar”; click opens lightbox.
- [ ] Close button visible; only the image area does not close; backdrop click closes.
- [ ] `Escape` closes the lightbox.
- [ ] Page scrolling is locked while open and restored after close.

**Tablet — 768px**
- [ ] Chart scales down without overflow or horizontal scroll.
- [ ] Lightbox image fits and remains readable.
- [ ] Navigation still renders (preserved `xl` breakpoint from Phase 5B.1).

**Mobile — 375px**
- [ ] No horizontal scrolling on the chart section.
- [ ] Lightbox close button reachable and ≥44 px.
- [ ] Enlarged chart is readable when zoomed.
- [ ] Accordion categories open/close and show people/photo fallbacks.

**No-image state**
- [ ] Neutral “Bagan organisasi belum tersedia” panel appears; no broken image; no old hardcoded chart.
- [ ] Accordion remains visible and functional.

**Admin**
- [ ] Upload PNG/JPEG/WebP ≤ 5 MB in Pengaturan Situs → appears on `/struktur-organisasi`.
- [ ] Uploading a file > 5 MB or SVG is rejected.
- [ ] Replacing the image updates the public page; existing image is not removed on a failed save.

---

## 14. Known Limitations

1. **No browser QA in this environment** (no Playwright/Puppeteer); responsiveness and lightbox behavior are verified structurally only.
2. **No JS test framework**, so click/Escape/scroll-lock behavior has no automated regression test; PHP feature tests deliberately do not claim browser coverage.
3. **No automatic old-file cleanup:** Filament's default behavior leaves the previous chart file on disk after replacement (pre-existing behavior, intentionally preserved to avoid deletion risk). Orphaned settings files may accumulate.
4. **Very large/tall charts** are scaled to `max-h-[75vh]` in-page; full readability depends on the click-to-zoom lightbox.
5. **SVG is intentionally rejected** for the chart upload pending a separate security assessment.

---

## 15. Final Recommendation

**PASS WITH LIMITATIONS** — implementation, automated tests, Pint, and production build all succeed with no database impact; only manual browser QA and JS-interaction automation remain outstanding.
