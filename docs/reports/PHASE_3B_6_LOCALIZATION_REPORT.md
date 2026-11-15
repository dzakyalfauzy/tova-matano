# Phase 3B-6 — About Localization & Copy Polish

**Status:** Implemented and verified locally. Awaiting approval.
**Scope:** `resources/js/Pages/About.jsx`, `lang/id/site.php`, `lang/en/site.php`, focused tests, and this report. No changes to WebController, SiteSettings, ManageSiteSettings, migrations, database, `.env`, SetLocale middleware, or Phase 1/2 files.

---

## 1. Localization Audit

### Project localization pattern
- There is **no** `resources/js/lang/` directory.
- The project uses PHP translation files: `lang/id/site.php` and `lang/en/site.php`.
- Translations are shared with every Inertia page through `HandleInertiaRequests::share()` as `translations` (`trans('site')`), consumed in React as `const { translations: t } = usePage().props`.
- Default application locale is `id`; `SetLocale` middleware is untouched.

### Hardcoded UI strings found in `About.jsx` (before this phase)
| Element | Issue |
| --- | --- |
| Section heading "Legalitas" | Hardcoded; also rendered even when legal gating hid the items |
| Legal notice "Data legalitas hanya ditampilkan setelah diverifikasi." | Hardcoded placeholder-like copy |
| Section heading "Potensi TMCC" | Hardcoded fallback |
| Potential body | Duplicated admin content as a hardcoded Indonesian paragraph |
| Vision/Mission/Goal section heading | Used `aboutContent.vision` (full sentence) as the heading |
| Vision card heading | Duplicated `aboutContent.vision` as both heading and body |
| Mission card heading | Used the full `aboutContent.mission` text as the heading |
| Goal card heading | Used the full `aboutContent.goal` text as the heading |
| Goal rendering | Rendered as a single paragraph instead of a list |
| Badge letters V/M/T | Hardcoded; "T" was wrong for English ("Goal") |
| Hero eyebrow | Available in props but never rendered |
| Image `alt` texts | Hardcoded English/Indonesian labels |
| Founder hover text | Hardcoded "Berdedikasi sejak awal mula TMCC berdiri." |
| Empty founders message | Hardcoded "Data pendiri belum ditambahkan." |
| CTA heading/body/buttons | Hardcoded Indonesian copy |
| `prose`/`prose-like` classes | `@tailwindcss/typography` is not installed; classes were inert |

### Admin-managed content (NOT localized)
`aboutContent`, `aboutLakeImageCredit`, `founders`, and `settings` remain the source of truth and are **not** moved to `lang/*`:
- `aboutContent.hero.eyebrow/title/subtitle`
- `aboutContent.lake.title/body`
- `aboutContent.profile.title/body`
- `aboutContent.potential.title/body`
- `aboutContent.legal.title/items/verified`
- `aboutContent.vision`
- `aboutContent.mission`
- `aboutContent.goal`
- `aboutContent.founders.title/narrative`
- `aboutLakeImageCredit`
- `founders` (DB)
- `settings` images/contact

---

## 2. Translation Keys Added

Both `lang/id/site.php` and `lang/en/site.php` received the same 11 keys (existing keys untouched):

| Key | Indonesian | English |
| --- | --- | --- |
| `potential` | Potensi TMCC | TMCC Potential |
| `legal` | Legalitas | Legal Status |
| `vision_mission_goal` | Visi, Misi & Tujuan | Vision, Mission & Goal |
| `founders_tagline` | Merekalah yang meletakkan batu pertama perjuangan pelestarian Danau Matano. | They laid the first stone in the struggle to preserve Lake Matano. |
| `founder_dedication` | Berdedikasi sejak awal mula TMCC berdiri. | Dedicated since TMCC was founded. |
| `founders_empty` | Data pendiri belum ditambahkan. | Founder data has not been added yet. |
| `about_hero_image_alt` | Latar belakang Tova Matano Care Center | Tova Matano Care Center background |
| `about_lake_image_alt` | Danau Matano | Lake Matano |
| `about_logo_alt` | Logo Tova Matano Care Center | Tova Matano Care Center logo |
| `cta_title` | Mari Berkarya Bersama Kami | Let Us Work Together |
| `cta_body` | Pelestarian lingkungan bukanlah tugas satu orang atau satu kelompok. Ini adalah tanggung jawab kita bersama. Jadilah bagian dari solusi nyata. | Environmental preservation is not the task of one person or one group. It is our shared responsibility. Be part of the real solution. |

Existing keys reused: `about_title`, `about_subtitle`, `lake_background`, `profile_history`, `vision`, `mission`, `goal`, `founders`, `nav_join`, `contact_us`.

No keys were removed or renamed.

---

## 3. Files Changed

| Status | File |
| --- | --- |
| Modified | `resources/js/Pages/About.jsx` |
| Modified | `lang/id/site.php` |
| Modified | `lang/en/site.php` |
| Modified | `tests/Feature/AboutPageTest.php` (2 new localization/content-separation tests) |
| Added | `tests/Unit/SiteLocalizationTest.php` (4 tests) |
| Added | `docs/reports/PHASE_3B_6_LOCALIZATION_REPORT.md` |

### Copy/format corrections made in `About.jsx` during the audit
These were necessary to make the localized UI correct and to honor the previously approved legal gating:
1. **Legal section fully conditional** — the entire section (heading + list) now renders only when `aboutContent.legal.verified === true` and `legal.items` is a non-empty string. Previously the heading/notice rendered even when items were gated.
2. **Vision/Mission/Goal headings fixed** — section heading uses `t.vision_mission_goal`; card headings use `t.vision`, `t.mission`, `t.goal`; `aboutContent.vision` is now only the vision body; mission/goal render as lists.
3. **Goal now renders as a list** (`splitLines`) instead of a single paragraph.
4. **Hero eyebrow rendered** from `aboutContent.hero.eyebrow` (was missing).
5. **Decorative badges localized** — `t.vision.charAt(0)`, `t.mission.charAt(0)`, `t.goal.charAt(0)` so English shows V/M/G.
6. **Potential** uses `aboutContent.potential.title/body`; duplicated hardcoded body removed; fallback title uses `t.potential`.
7. **Invalid `prose`/`prose-like` classes replaced** with explicit Tailwind typography classes (the typography plugin is not installed).

---

## 4. Evidence Admin Content Is Not Moved to `lang`

- `About.jsx` reads all main content from `aboutContent` (Admin-managed JSON) and `founders` (DB). The `lang` files contain only UI labels/alt text/CTA copy — no lake/profile/potential/legal/vision/mission/goal/founder narrative.
- New test `keeps admin managed about content separate from ui translation strings` asserts:
  - `aboutContent.hero.title === 'Konten Admin Hero'` (from SiteSettings), while
  - `translations.about_title === 'Tentang Kami'` (UI label).
- Unit test `removes hardcoded legal placeholders from the about page` asserts no AHU/NPWP/Kesbangpol placeholders in `About.jsx`.
- Unit test `reads all nine about sections from inertia props` asserts `aboutContent?.hero/lake/profile/potential/legal/vision/mission/goal/founders` references exist.

---

## 5. ID and EN Verification

- Feature test `exposes the about ui translation keys in indonesian and english` opens `/tentang` under locale `id` and `en` and asserts the localized values for `potential`, `legal`, `vision_mission_goal`, `founder_dedication`, `cta_title`, and `cta_body`.
- Existing tests still verify:
  - EN uses English admin content when available.
  - EN falls back to Indonesian when EN content is empty (`SiteSettings::getAboutContent` behavior unchanged).
- No changes to `SetLocale`, default locale, or the model fallback.

### Focused test results
| Command | Result |
| --- | --- |
| `php artisan test --compact tests/Unit/SiteLocalizationTest.php tests/Feature/AboutPageTest.php` | **15 passed, 265 assertions** |

### Full suite
| Command | Result |
| --- | --- |
| `php artisan test --compact` | **99 passed, 732 assertions** |

### Build
| Command | Result |
| --- | --- |
| `npm run build` | succeeded (About bundle regenerated; no build/console errors) |
| `vendor/bin/pint lang/id/site.php lang/en/site.php tests/Unit/SiteLocalizationTest.php tests/Feature/AboutPageTest.php --format agent` | passed |

---

## 6. Legal Gating Re-verified

- Server-side gating (`WebController::gateAboutLegalContent`) unchanged and still covered by tests (sentinel `AHU-SECRET-001` absent from response body; items emptied when unverified or empty).
- Frontend now hides the **entire** legal section when unverified/empty (previously the heading and a notice rendered). This matches the approved architecture: the section must not render at all.
- No legal fallback or placeholder is rendered.

---

## 7. Git Status

```
 M app/Filament/Pages/ManageSiteSettings.php       (Phase 3B-3, pre-existing)
 M app/Http/Controllers/WebController.php          (Phase 3B-4 + Phase 2B)
 M app/Models/SiteSettings.php                     (Phase 3B-2)
 M lang/en/site.php                                (Phase 3B-6)
 M lang/id/site.php                                (Phase 3B-6)
 M resources/js/Components/Public/AboutSnippet.jsx (Phase 2B)
 M resources/js/Pages/About.jsx                    (Phase 3B-5 + 3B-6)
 M resources/js/Pages/Home.jsx                     (Phase 2B)
 M tests/Unit/SiteSettingsTest.php                 (Phase 3B-2)
 M tests/Feature/AboutPageTest.php                 (Phase 3B-4 + 3B-6)
?? database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php (Phase 3B-1)
?? docs/reports/                                  (all report markdowns)
?? "docs/revisi web.docx"
?? tests/Feature/ManageSiteSettingsTest.php        (Phase 3B-3)
?? tests/Feature/SiteSettingsAboutContentTest.php  (Phase 3B-2)
?? tests/Unit/SiteLocalizationTest.php             (Phase 3B-6)
```

No commits, pushes, stashes, deletions, or deployments were performed.

---

## 8. Open Items Requiring Client Decision

1. **English UI wording** — the 11 new English labels/CTA copy were authored for this implementation (e.g., "Legal Status", "TMCC Potential", "Let Us Work Together"). Client approval is recommended before treating them as final.
2. **Founder narrative placement** — `aboutContent.founders.narrative` is used as the tagline above founder cards. If the client prefers it as a dedicated narrative block, the layout may need a small design adjustment (out of scope).
3. **Admin English content** — client has not supplied EN translations for the About content; the existing ID fallback remains.
4. **Final legal numbers** — still pending client verification; hidden until `about_legal_verified` is enabled.

**STOP — Phase 3B-6 complete.** Awaiting Project Owner approval before any further phase.