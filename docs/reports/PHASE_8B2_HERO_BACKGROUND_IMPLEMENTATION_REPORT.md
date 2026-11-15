# PHASE 8B.2 — PARTNERSHIP, PROGRAM & CONTACT HERO BACKGROUND IMPLEMENTATION REPORT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 8B.2 — Hero backgrounds + Contact copy alignment  
**Date:** November 14, 2026  
**Status:** COMPLETE — AWAITING MANUAL VISUAL QA

---

## 1. Client Requirement Mapping

| Page | Client Verbatim (MASUKAN) | Implemented |
|---|---|---|
| Partnership | `Partnership` / `Mitra dan pendukung yang telah bekerjasama dan berkontribusi bersama Tova Matano Care Center.` / `memang tdk ada photo background kah` (#13) | Title/subtitle unchanged; `partnership_hero_image_url` added and rendered. |
| Program | `Program Kami` / `Empat pilar aksi nyata TMCC dalam menjaga kelestarian Danau Matano.` / `memang tdk ada photo background kah` (#14) | Title/subtitle unchanged; `program_hero_image_url` added and rendered. |
| Contact | `Hubungi Kami` / `Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar` / `memang tdk ada photo background kah` (#17) | Subtitle aligned verbatim (missing clause added); `contact_hero_image_url` added and rendered. |

---

## 2. Existing Implementation Inspection

- **Hero pattern verified:** `Blog.jsx` (`blog_hero_image_url`) uses a `relative overflow-hidden` navy section, absolutely positioned `<img class="w-full h-full object-cover opacity-30">`, then `bg-brand-navy/80 mix-blend-multiply` overlay, with content in `relative z-10` and `drop-shadow`.
- **Upload pattern verified:** Blog Hero FileUpload in `ManageSiteSettings.php` uses `public` disk, dedicated directory, `image()`, JPEG/PNG/WebP, `maxSize(5120)`, `visibility('public')`, `preventFilePathTampering()`, preview 240, openable/downloadable, full width.
- **Pages verified:** `Partnerships.jsx` and `Programs.jsx`/`Contact.jsx` previously used plain `bg-brand-navy` headers. Partnership and Program subtitles already matched the client verbatim. Contact subtitle did not.
- **Schema verified before migration:** no `partnership_hero_image_url`, `program_hero_image_url`, or `contact_hero_image_url` existed; `blog_hero_image_url` was the last image column.
- **Localization verified:** no approved English contact subtitle exists; pages render Indonesian copy directly. No new English copy was invented (gap reported in §7).

---

## 3. Migration / Schema Changes

New file: `database/migrations/2026_11_14_110000_add_page_hero_images_to_site_settings_table.php`

```php
up():   partnership_hero_image_url  nullable string after blog_hero_image_url
        program_hero_image_url      nullable string after partnership_hero_image_url
        contact_hero_image_url      nullable string after program_hero_image_url
down(): dropColumn([...all three...])
```

- Nullable, no defaults rewritten, no data touched, fully reversible.
- Executed against **local MySQL** (`tova_matano` @ 127.0.0.1) — `198.64ms DONE`.
- `SiteSettings::$fillable` extended with the three fields.
- Verified after migration: 1 `site_settings` row, 4 `programs`, 1 `partnerships` — unchanged.

---

## 4. Filament Upload Implementation

Added three independent FileUploads inside the existing **"Tampilan Header & Gambar"** section (no new duplicate section):

| Field | Label | Directory |
|---|---|---|
| `partnership_hero_image_url` | Background Hero Partnership | `settings/partnership` |
| `program_hero_image_url` | Background Hero Program | `settings/program` |
| `contact_hero_image_url` | Background Hero Contact | `settings/contact` |

All use: `image()`, `acceptedFileTypes(['image/jpeg','image/png','image/webp'])`, `maxSize(5120)`, `disk('public')`, `visibility('public')`, `preventFilePathTampering()`, `imagePreviewHeight('240')`, `openable()`, `downloadable()`, `columnSpanFull()`, and Indonesian helper text.

**Lifecycle:** follows the same Filament-managed lifecycle as Blog Hero — new files are stored during state dehydration; existing images are not deleted before the database update succeeds; each field is independent so replacing/removing one never touches the other two.

---

## 5. Partnership Hero Result

`resources/js/Pages/Partnerships.jsx`
- Header wrapped in `relative ... overflow-hidden`; renders `settings?.partnership_hero_image_url` via `/storage/{path}` with `object-cover opacity-30`.
- Navy/80 multiply overlay; title + subtitle moved to `relative z-10` with `drop-shadow-md`/`drop-shadow`.
- No image → solid `bg-brand-navy` (no broken image, no empty container, no layout shift).
- All partnership grouping, empty state, logo cards, and links untouched.

---

## 6. Program Hero Result

`resources/js/Pages/Programs.jsx`
- Same pattern using `settings?.program_hero_image_url` only.
- Title `Program Kami` and subtitle unchanged and verbatim.
- Program list, images, ordering (`Program #{index+1}`), links, and empty state untouched.

---

## 7. Contact Hero & Copy Result

`resources/js/Pages/Contact.jsx`
- Hero uses `settings?.contact_hero_image_url` only, same overlay/fallback pattern.
- Subtitle updated to exact client wording:
  `Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar` (no trailing period, per source document).
- Title unchanged: `Hubungi Kami`.
- Contact info cards, form fields, validation, `POST /kontak/kirim`, success state, and reset behavior untouched.

**English localization gap:** the Contact subtitle is rendered as hardcoded Indonesian in all locales, and no client-approved English wording exists. Per instructions, no new English copy was invented. This is reported as an open content gap (current behavior unchanged from before this phase).

---

## 8. Files Changed

| File | Change |
|---|---|
| `database/migrations/2026_11_14_110000_add_page_hero_images_to_site_settings_table.php` | New reversible migration (3 nullable columns). |
| `app/Models/SiteSettings.php` | Added 3 fields to `$fillable`. |
| `app/Filament/Pages/ManageSiteSettings.php` | Added 3 FileUpload components in existing "Tampilan Header & Gambar" section. |
| `resources/js/Pages/Partnerships.jsx` | Hero background rendering + fallback. |
| `resources/js/Pages/Programs.jsx` | Hero background rendering + fallback. |
| `resources/js/Pages/Contact.jsx` | Hero background rendering + fallback; subtitle aligned verbatim. |
| `tests/Feature/PageHeroBackgroundsTest.php` | New feature tests (23 tests / 219 assertions). |
| `docs/reports/PHASE_8B2_HERO_BACKGROUND_IMPLEMENTATION_REPORT.md` | This report. |

Not modified: program names/slugs/data, partnership records, contact details/form behavior, Blog hero, About, Navigation, logo assets, Organization, Water Quality, homepage marquee.

---

## 9. Test Results

Focused:
```
php artisan test tests/Feature/PageHeroBackgroundsTest.php --compact
→ 23 passed / 219 assertions
```

Coverage: column existence + nullable defaults; reversible migration source check; valid JPEG/PNG/WebP accepted into correct directories; PDF rejected for each field; >5 MB rejected for each field; upload independence (one upload leaves other two intact); removal independence; each public page receives only its own hero prop; null settings safe on all three routes; per-page field usage + navy fallback + `object-cover` source checks; exact Contact title/subtitle; existing contact settings preserved.

Full suite:
```
php artisan test --compact
→ 197 passed / 1723 assertions (114.54s)
```
Baseline 174/1504 → +23 tests / +219 assertions, zero regressions.

Pint:
```
vendor/bin/pint --dirty --format agent
→ passed
```

---

## 10. Build Results

```
npm run build
→ ✓ built in 7.49s
   Partnerships: 3.65 kB | Programs: 4.37 kB | Contact: 7.96 kB
```

Warnings — all pre-existing: `esbuild`/`oxc` recommendation, `optimizeDeps.rollupOptions` deprecation, `/image/pattern.svg` runtime resolution, plugin timing. No new warnings.

---

## 11. Migration Execution Environment

- Connection verified: `mysql`, database `tova_matano`, host `127.0.0.1` (local development).
- `php artisan migrate --pretend` reviewed before execution.
- Executed once locally: `2026_11_14_110000_add_page_hero_images_to_site_settings_table ... DONE`.
- No production database or server was accessed; no seeders run; no `migrate:fresh`, `db:wipe`, or data reset.

---

## 12. Safety Confirmation

- Existing records, program data, slugs, and partnership data unchanged.
- No file deletions; no logo or storage asset modified.
- No commit, push, or deployment.
- All Phase 1–8B.1.2 uncommitted work preserved.

---

## 13. Manual Visual QA Checklist

**Admin**
- [ ] Pengaturan Situs → "Tampilan Header & Gambar" shows the three new fields with preview, download, and remove.
- [ ] Upload JPEG/PNG/WebP ≤ 5 MB to each; SVG/PDF/GIF and > 5 MB are rejected.
- [ ] Replacing Partnership image does not change Program/Contact images (and vice versa).
- [ ] Removing one image leaves the other two intact.

**Partnership `/partnership`**
- [ ] With image: background photo visible, darkened, title/subtitle readable.
- [ ] Without image: solid navy, no broken image or layout jump.
- [ ] Partner groups, logos, links unchanged.

**Program `/program`**
- [ ] With image: background photo visible with overlay; title/subtitle readable.
- [ ] Without image: solid navy fallback.
- [ ] Program list ordering, images, links unchanged.

**Contact `/kontak`**
- [ ] With image: background photo visible with overlay; title/subtitle readable.
- [ ] Without image: solid navy fallback.
- [ ] Subtitle reads exactly: "Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar".
- [ ] Contact info, form submission, success state work as before.

**Responsive**
- [ ] 1280 px, 768 px, 375 px: hero heights, text wrapping, and overlays balanced; no horizontal overflow.

---

**STOP — awaiting manual browser QA and explicit approval.**
