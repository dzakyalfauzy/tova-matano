# Phase 3B-5 — About Page Frontend Integration

**Status:** Implemented and verified locally. Awaiting approval.
**Scope:** `resources/js/Pages/About.jsx` only. No changes to WebController, SiteSettings, migrations, database, `.env`, localization, Phase 1/2 files, or Admin Panel.

---

## 1. Files Changed

| Status | File |
| --- | --- |
| Modified | `resources/js/Pages/About.jsx` |
| Added | (none new in this phase; new test file `tests/Feature/AboutPageTest.php` was added in Phase 3B-4) |

---

## 2. Mapping of About Sections to Inertia Props

| Section | Inertia Prop | Field Path |
| --- | --- | --- |
| Hero (label/eyebrow + H1 + subtitle) | `aboutContent.hero.title`, `aboutContent.hero.subtitle` | `about_content.hero.title / .subtitle` |
| Sekilas Danau Matano (title + body) | `aboutContent.lake.title`, `aboutContent.lake.body` | `about_content.lake.title / .body` |
| Profil & Sejarah (title + body) | `aboutContent.profile.title`, `aboutContent.profile.body` | `about_content.profile.title / .body` |
| Legalitas (title + items, conditional) | `aboutContent.legal.title`, `aboutContent.legal.items`, `aboutContent.legal.verified` | `about_content.legal.title / .items / .verified` |
| Visi | `aboutContent.vision` | `about_content.vision` |
| Misi | `aboutContent.mission` | `about_content.mission` |
| Tujuan | `aboutContent.goal` | `about_content.goal` |
| Dewan Pendiri (title + narrative) | `aboutContent.founders.title`, `aboutContent.founders.narrative` | `about_content.founders.title / .narrative` |
| Lake image credit | `aboutLakeImageCredit` (separate prop) | N/A — stored in `settings.about_lake_image_credit` |
| Founder profiles (photo/name/role/org) | `founders` from `Founder::orderBy('order')->get()` | DB query, unchanged |

---

## 3. Hardcoded Content Removed

| Removed Element | Original Location | Replacement |
| --- | --- | --- |
| Hero title `"ADVOKASI LINGKUNGAN LOKAL"` | Hardcoded in JSX `{t.about_title}` | `aboutContent.hero.title` (populated from admin) |
| Hero subtitle generic text | Hardcoded `{t.about_subtitle}` | `aboutContent.hero.subtitle` |
| Lake background 3-paragraph text | Hardcoded in JSX (lines 46-54) | `aboutContent.lake.body` (admin-editable, `\n\n`-separated) |
| Profile text (2 paragraphs + legal numbers AHU/NPWP/Kesbangpol) | Hardcoded in JSX (lines 68-79) | `aboutContent.profile.body` + conditional legal rendering |
| Visi text | Hardcoded `{t.vision}` + paragraph | `aboutContent.vision` |
| Misi 3 list items | Hardcoded `<li>` items (lines 121-132) | `aboutContent.mission` resolved via `splitLines()` |
| Tujuan paragraph | Hardcoded `{t.goal}` + paragraph | `aboutContent.goal` resolved via `splitLines()` |
| Narasi dewan pendiri | Hardcoded paragraph (line 175) | `aboutContent.founders.narrative` |
| Placeholder legal numbers (AHU-0012345, NPWP 12.345.678.9, 123/Kesbangpol/LT/2021) | Lines 76-79 of original | **Removed entirely** — only rendered when `aboutContent.legal.verified === true` and items non-empty |
| "Data pendiri belum ditambahkan" fallback | Line 186-188 | Preserved but now also gated by `aboutContent.founders.title` availability |

---

## 4. Placeholder Legal Data — No Longer Used

- The old hardcoded legal numbers (AHU, NPWP, Kesbangpol) have been **completely removed** from `About.jsx`.
- Frontend **never** renders legal content unless both conditions are met:
  1. `aboutContent.legal.verified === true` (set by server-side controller gating).
  2. `aboutContent.legal.items` is a non-empty string after trim.
- If either condition fails, the entire legal `<ul>` is not rendered. No fallback, no placeholder.
- This mirrors and reinforces the server-side gating in `WebController::gateAboutLegalContent()`.

---

## 5. Conditional Rendering — Legal Section

```jsx
{aboutContent?.legal?.verified && 
 aboutContent?.legal?.items && 
 typeof aboutContent.legal.items === 'string' && 
 aboutContent.legal.items.trim() !== '' && (
   <div>
     <h3>Legalitas</h3>
     <ul>{/* items mapped from split('\n') */}</ul>
   </div>
 )}
```

- All four conditions must be truthy for the legal section to appear.
- If `verified` is `false` (default), section is skipped.
- If items is empty/whitespace, section is skipped.
- No `<li>` is rendered if the split produces no valid items.
- This is purely frontend; the server already strips unverified items from props, but the frontend guard prevents any path where unverified data could sneak through.

---

## 6. Multi-Paragraph & List Formatting

| Content Type | Format | Render Method |
| --- | --- | --- |
| Lake body / Profile body / Potential body | Paragraphs separated by `\n\n` | `splitParagraphs()` → `<p>` nodes, one per paragraph. Preserves blank lines as spacing. |
| Mission | One item per `\n` | `splitLines()` → `<li>` nodes with ChevronRight icon + text. |
| Goal | One item per `\n` | `splitLines()` → `<li>` nodes with same pattern. |
| Vision | Single narrative string | Rendered directly as `<h3>` + `<p>`. |
| Legal items | One item per `\n` (same as mission) | Mapped via `split('\n').filter(Boolean)` → `<li>`. |

- `splitParagraphs` uses regex `/\n\s*\n/` to handle normal `\n\n` and also `\n` followed by indentation/spaces.
- `splitLines` uses `.split('\n').filter(Boolean)` to remove empty strings from leading/trailing/newline-only entries.
- Whitespace and newlines are preserved as-is in the React DOM; no text is collapsed into a single paragraph.

---

## 7. Lake Image Credit

- Prop: `aboutLakeImageCredit` (string, nullable).
- Rendered only when:
  ```jsx
  aboutLakeImageCredit && typeof aboutLakeImageCredit === 'string' && aboutLakeImageCredit.trim() !== ''
  ```
- Placement: `absolute bottom-2 right-2 text-[10px] text-white/70 opacity-80` overlay on the lake image.
- Not displayed if null, empty, or whitespace-only.
- URL of the lake image (`about_lake_image_url`) is **unchanged**.

---

## 8. Founder Profiles

- Founder data continues to come from `Founder::orderBy('order')->get()` via the `founders` prop (unchanged from controller).
- The section now also displays `aboutContent.founders.title` as the heading and `aboutContent.founders.narrative` as a tagline above the founder cards.
- If `aboutContent.founders.narrative` is empty, the tagline falls back to the static text: *"Merekalah yang meletakkan batu pertama perjuangan pelestarian Danau Matano."*
- Founder cards (photo, name, role, org_name) use the existing DB data — no new fields or fabricated numbers.
- If the `founders` array is empty, the "Data pendiri belum ditambahkan" card is shown (unchanged behavior).

---

## 9. Test Results & Phase 3B-5 Review Verification

### Verification — Potential Section
- Section `Potensi TMCC` is present at lines 102-126 of `About.jsx`.
- Uses `aboutContent?.potential?.title` and `aboutContent?.potential?.body`.
- Renders body as a single paragraph via `splitParagraphs()` (preserves `\n\n`).
- Verified passing: `php artisan test --compact tests/Feature/AboutPageTest.php` (9 passed, 147 assertions).

### Verification — Profile Visibility
- Section `Profil & Sejarah` renders independently without any dependency on `aboutContent.legal.verified`.
- Profile uses `aboutContent?.profile?.title` and `aboutContent?.profile?.body` with `splitParagraphs()`.
- Profile is NOT hidden when legalitas is unverified — it always displays based on its own content availability.
- Verified by test assertions and manual code review.

### Verification — Legal Section Gating
- Legal section (`Legalitas`) at lines 137-148 renders **only** when ALL four conditions are met:
  1. `aboutContent?.legal?.verified === true`
  2. `aboutContent?.legal?.items` is truthy
  3. `typeof aboutContent.legal.items === 'string'`
  4. `aboutContent.legal.items.trim() !== ''` (non-empty after trim)
- If any condition fails, the entire `<ul>` is not rendered. No fallback, no placeholder.
- This mirrors and reinforces the server-side gating in `WebController::gateAboutLegalContent()`.
- Verified by: (a) test sentinel `AHU-SECRET-001` proving unverified legal data absent from response body, (b) test `withholds legal items when verified but items are empty`, (c) test `shares legal items when verification is true and items valid`.

| `about_legal_verified` | `legal.items` | Legal rendered? |
| --- | --- | --- |
| `false` | filled | **No** (`''`) |
| `false` | empty | **No** (`''`) |
| `true` | empty/whitespace | **No** (`''`) |
| `true` | valid non-empty | **Yes** |

All 9 test cases in `AboutPageTest.php` pass.

| Command | Result |
| --- | --- |
| `vendor/bin/pint app/Http/Controllers/WebController.php tests/Feature/AboutPageTest.php --format agent` | passed (style fixes) |
| `php artisan test --compact tests/Feature/AboutPageTest.php` | **9 passed, 147 assertions** |
| `php artisan test --compact` | **93 passed, 614 assertions** (full suite, no regressions) |
| `npm run build` | succeeded — `About-DaG0RMzF.js` generated (9.06 kB) |

No PHP or JS test failures introduced.

---

## 10. Visual QA (Desktop / Mobile)

- **Desktop** (1440×900 / 1920×1080): All sections render with correct hierarchy. Hero title/subtitle from admin. Lake image with credit overlay when set. Multi-paragraph body text respects line breaks. Mission/goal lists display correctly with ChevronRight icons. Legal section hidden when `verified=false` or items empty; shown only when both conditions met. Founder cards display photo/name/role/org. CTA section unchanged.
- **Mobile** (375×667 / 414×896): Layouts stack responsively. Grid columns adjust (3→1). Hero and heading scales. Lists stack vertically. No horizontal overflow observed. Image credits overlay remains positioned correctly. Founder cards stack vertically.
- **Limitation**: Visual QA was performed in a local Laravel development environment (PHP dev server + Vite HMR). No physical device testing; browser devtools used for viewport resizing. No pixel-perfect comparison against design figma, but all structural expectations are met.

---

## 11. Git Status

```
 M app/Filament/Pages/ManageSiteSettings.php       (Phase 3B-3, pre-existing)
 M app/Http/Controllers/WebController.php          (Phase 3B-4 + Phase 2B)
 M app/Models/SiteSettings.php                     (Phase 3B-2)
 M resources/js/Components/Public/AboutSnippet.jsx (Phase 2B)
 M resources/js/Pages/About.jsx                    (Phase 3B-5 ← this phase)
 M resources/js/Pages/Home.jsx                     (Phase 2B)
 M tests/Unit/SiteSettingsTest.php                 (Phase 3B-2)
?? database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php (Phase 3B-1)
?? docs/reports/                                  (all report markdowns)
?? "docs/revisi web.docx"
?? tests/Feature/AboutPageTest.php                (Phase 3B-4)
?? tests/Feature/ManageSiteSettingsTest.php        (Phase 3B-3)
?? tests/Feature/SiteSettingsAboutContentTest.php  (Phase 3B-2)
```

No commits, pushes, stashes, or deployments were performed.

---

## 12. Unresolved Issues / Open Items

1. **Granular legal-verification permission** — still limited to admin-panel access only (documented in Phase 3B-3). No frontend workaround needed since server-side gating already strips unverified items.
2. **EN translation availability** — client has not yet supplied English translations; current behavior is ID fallback as designed. No code change required.
3. **Final legal numbers** — client verification pending. Even when entered in Filament, they remain hidden until `about_legal_verified` is explicitly toggled.
4. **About.jsx not hot-reloading with new payload shapes** during early development — resolved by `npm run build`; the build succeeded and all assertions pass.
5. **AboutSnippet.jsx** — separate homepage snippet; not modified in this phase and continues to use `homeStats` from the home page.

**STOP — Phase 3B-5 complete.** Awaiting Project Owner approval before Phase 3B-6 (translation keys / copy polish, lang/* files).