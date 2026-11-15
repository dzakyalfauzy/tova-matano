# PHASE 8A — CROSS-PAGE ASSET & CONTENT READINESS AUDIT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 8A — Readiness Audit  
**Date:** November 14, 2026  
**Status:** AUDIT COMPLETE — STRICT READ-ONLY MODE  
**Primary Reference:** `docs/revisi web.docx`  
**Master Roadmap Reference:** `docs/reports/TMCC_MASTER_REVISION_ROADMAP.md`

---

## 1. Executive Summary

Phase 8A is a strict read-only audit investigating six cross-page asset and editorial items requested by the client in `docs/revisi web.docx`:
1. **Logo asset** with full text ("Tova Matano Care Center").
2. **Featured Program names** alignment (including spelling ambiguity for "Champaign" vs. "Campaign").
3. **Partnership page hero background** image management.
4. **Program page hero background** image management.
5. **Contact page copy alignment** and hero background image management.
6. **Running text / marquee visibility** at desktop 100% zoom.

### Summary Status by Audit Dimension

| Item | Dimension | Status | Key Finding |
|---|---|---|---|
| **A** | Logo with full text | **BLOCKED BY CLIENT ASSET** | Only emblem file `logo_tova_matano.png` (259×258px) exists. No graphic asset with wordmark exists in repo or storage. |
| **B** | Program Names & Slugs | **CLIENT DECISION REQUIRED** | 4 programs currently in DB (`Tova Water Patrol`, `Eco Education`, `Clean Lake Action`, `Biodiversity Watch`). 4th program name in client doc is `"Tova Champaign & Event"` (ambiguous spelling). Slug updates carry breaking URL risks for 9 activity routes. |
| **C** | Partnership Background | **READY (Requires Option Selection)** | Hardcoded solid navy hero in `Partnerships.jsx`. Currently no `partnership_hero_image_url` field in DB/Admin. |
| **D** | Program Background | **READY (Requires Option Selection)** | Hardcoded solid navy hero in `Programs.jsx`. Currently no `program_hero_image_url` field in DB/Admin. |
| **E** | Contact Copy & Background | **READY (Copy) / READY (Background)** | Current subtitle is missing `"Ada saran dan masukan,"`. Background is currently solid navy without Admin setting. |
| **F** | Marquee Desktop 100% Zoom | **NEEDS VISUAL QA** | Marquee logic in `Home.jsx` is functionally sound (double track + 16 duplicated items, infinite CSS transform), but real-browser visual QA on 100% monitor zoom is required to confirm client's observation. |

---

## 2. Exact Client Requirements (Verbatim from `docs/revisi web.docx`)

### 2.1 Logo
- **Page: Beranda, Masukan #2 (Lines 003–005):**
  > *"Logo : Bisakah dipakai yang dibawahnya ada tulisan Tova Matano Care Centrer"*
- **Page: Tentang TMCC, Masukan #9 (Line 045):**
  > *"Photo/logo : diperkecil dan gunakan yang ada tulisan tova Matano care center"*

### 2.2 Program Unggulan
- **Page: Beranda, Masukan #4 (Line 013):**
  > *"CATATAN : Tolong dinaikkan Program Unggulan baru nyusul Portal Keanggotaan"* (Implemented in Phase 2).
- **Page: Beranda, Masukan #5 (Lines 015–020):**
  > *"REVISI: Program Unggulan sudah saya revisi:*
  > *1. Tova Water Patrol*
  > *2. Tova Edu & Research*
  > *3. Tova Care Action*
  > *4. Tova Champaign & Event"*

### 2.3 Partnership Background
- **Page: Partnership, Masukan #13 (Lines 086–090):**
  > *"PAGE : PARTNERSHIP*
  > *MASUKAN#13*
  > *Perubahan Redaksional:*
  > *Partnership*
  > *Mitra dan pendukung yang telah bekerjasama dan berkontribusi bersama Tova Matano Care Center.*
  > *Pertanyaan: memang tdk ada photo background kah"*

### 2.4 Program Background
- **Page: Program, Masukan #14 (Lines 091–096):**
  > *"PAGE : PROGRAM*
  > *MASUKAN#14*
  > *Perubahan Redaksional:*
  > *Program Kami*
  > *Empat pilar aksi nyata TMCC dalam menjaga kelestarian Danau Matano.*
  > *Pertanyaan: memang tdk ada photo background kah"*

### 2.5 Kontak Copy & Background
- **Page: Kontak, Masukan #17 (Lines 110–115):**
  > *"PAGE : KONTAK*
  > *MASUKAN#17*
  > *Perubahan Redaksional:*
  > *Hubungi Kami*
  > *Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar*
  > *Pertanyaan: memang tdk ada photo background kah"*

### 2.6 Marquee / Running Text
- **Page: Beranda, Masukan #1 (Lines 001–002):**
  > *"PAGE : BERANDA*
  > *MASUKAN#1*
  > *Tolong di cek, benarkah tampilan pesan berjalannya tdk muncul dalam tampilan monitor 100% ?"*

---

## 3. Logo Asset Audit

### 3.1 Existing Asset Inspection
- Search across entire `public/` and `storage/app/public/` directories:
  - Only one logo image exists: `public/image/logo_tova_matano.png`.
  - Dimensions: **259 × 258 pixels** (Square aspect ratio 1:1).
  - Content: Pure circular emblem (waves + sun + mountain emblem) without any text underneath.
  - File size: 68,877 bytes.
- No file containing wordmark or horizontal lockup exists anywhere in the repository or storage.

### 3.2 Usage in Codebase
- `resources/js/Components/Public/Navigation.jsx:40-47`:
  Renders `<img src="/image/logo_tova_matano.png" className="h-9 w-auto" />` followed by an HTML `<span>Tova Matano Care Center</span>` (hidden on mobile `< sm`).
- `resources/js/Pages/About.jsx:40, 70, 102`: Uses `logo_tova_matano.png` as fallback illustration.
- `resources/js/Pages/Organization.jsx:109`: Uses `logo_tova_matano.png` as hero fallback.

### 3.3 Status & Specification Needed
- **Status:** **BLOCKED BY CLIENT ASSET**
- We must not generate or fabricate a makeshift logo.
- **Specification for Client Asset Request:**
  - File format: Transparent PNG or SVG (vector preferred).
  - Layout options needed:
    1. **Vertical/Stacked Lockup:** Emblem with "Tova Matano Care Center" underneath (for About page profile and general lockups).
    2. **Horizontal Lockup:** Emblem on left with "Tova Matano Care Center" on right (for Navigation bar).
  - Minimum resolution: 1200px width (or vector SVG).

---

## 4. Program Data & Slug Audit

### 4.1 Current Database Records vs. Client Revision
Read-only inspection of the `programs` table:

| ID | Current `name` in DB | Current `slug` in DB | Client Requested Name | Activities Linked |
|---|---|---|---|---|
| 1 | `Tova Water Patrol` | `tova-water-patrol` | `Tova Water Patrol` | 3 activities |
| 2 | `Eco Education` | `eco-education` | `Tova Edu & Research` | 2 activities |
| 3 | `Clean Lake Action` | `clean-lake-action` | `Tova Care Action` | 2 activities |
| 4 | `Biodiversity Watch` | `biodiversity-watch` | `Tova Champaign & Event` *(Ambiguous)* | 2 activities |

### 4.2 Technical & Routing Impact Analysis
1. **Activity Association:**
   There are 9 records in `activities`. Each activity belongs to `program_id` via a foreign key constraint:
   - `activities.program_id -> programs.id`
   Updating `programs.name` alone does **NOT** break foreign key relationships or activity bindings.
2. **Routing & Slugs:**
   In `routes/web.php`:
   - `Route::get('/program/{slug}', [WebController::class, 'programCategory'])`
   - `Route::get('/program/{program_slug}/{activity_slug}', [WebController::class, 'programDetail'])`
   - Notice: `programDetail` matches by `program_slug`! If `slug` is changed:
     - Old bookmark URLs or external links to `/program/eco-education` will return 404 unless redirected.
     - Changing `slug` from `eco-education` to `tova-edu-research` requires updating any hardcoded links if they exist.
3. **Ambiguity on Program #4:**
   - Client document: `"Tova Champaign & Event"`.
   - "Champaign" is typically a misspelling of **"Campaign"** (or refers to the wine/region).
   - We must not guess whether the client intends `"Tova Campaign & Event"` or strictly `"Tova Champaign & Event"`.
4. **Status:** **CLIENT DECISION REQUIRED**

---

## 5. Partnership Hero Background Audit

### 5.1 Current Implementation (`resources/js/Pages/Partnerships.jsx:25-33`)
```jsx
{/* Page Header */}
<div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
    <div className="max-w-4xl mx-auto px-4">
        <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Partnership</h1>
        <p className="text-xl text-gray-200">
            Mitra dan pendukung yang telah bekerjasama dan berkontribusi bersama Tova Matano Care Center.
        </p>
    </div>
</div>
```
- Current state: Solid navy background with static copy.
- Subtitle in `Partnerships.jsx:30` **already exactly matches** client requirement MASUKAN #13 verbatim.
- Currently, `site_settings` has no `partnership_hero_image_url` column.
- Controller: `WebController::partnerships()` passes `$this->settings()`.

### 5.2 Status & Readiness
- **Status:** **READY (Requires Decision on Background Upload)**
- If client confirms they want a background photo:
  - Add nullable `partnership_hero_image_url` to `site_settings` table.
  - Add FileUpload to `ManageSiteSettings.php` (directory: `settings/partnerships`).
  - Render with dark overlay in `Partnerships.jsx` falling back to solid navy.

---

## 6. Program Page Hero Background Audit

### 6.1 Current Implementation (`resources/js/Pages/Programs.jsx:11-18`)
```jsx
{/* Page Header */}
<div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
    <div className="max-w-4xl mx-auto px-4">
        <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Program Kami</h1>
        <p className="text-xl text-gray-200">
            Empat pilar aksi nyata TMCC dalam menjaga kelestarian Danau Matano.
        </p>
    </div>
</div>
```
- Current state: Solid navy background with static copy.
- Subtitle in `Programs.jsx:15` **already exactly matches** client requirement MASUKAN #14 verbatim.
- Currently, `site_settings` has no `program_hero_image_url` column.
- Controller: `WebController::programs()` passes `$this->settings()`.

### 6.2 Status & Readiness
- **Status:** **READY (Requires Decision on Background Upload)**
- If client confirms they want a background photo:
  - Add nullable `program_hero_image_url` to `site_settings` table.
  - Add FileUpload to `ManageSiteSettings.php` (directory: `settings/programs`).
  - Render with dark overlay in `Programs.jsx` falling back to solid navy.

---

## 7. Contact Copy & Hero Background Audit

### 7.1 Copy Comparison
- **Client Verbatim (MASUKAN #17):**
  - Title: `Hubungi Kami`
  - Subtitle: `Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar`
- **Current in `resources/js/Pages/Contact.jsx:32-33`:**
  - Title: `Hubungi Kami`
  - Subtitle: `Ada pertanyaan atau ingin berkolaborasi? Kami siap mendengar.`
- **Discrepancy:** The current subtitle is missing the initial clause: `"Ada saran dan masukan,"`.

### 7.2 Localization Check
- Currently, `lang/id/site.php` and `lang/en/site.php` have `'contact_us' => 'Hubungi Kami'`, but the subtitle is hardcoded in Indonesian directly inside `Contact.jsx:33`.
- In Phase 8B, this should be updated to the exact text and optionally moved to localization keys (`contact_title`, `contact_subtitle`).

### 7.3 Background Check
- `Contact.jsx:30` has solid navy background (`bg-brand-navy`).
- `site_settings` has no `contact_hero_image_url` column.
- Controller: `WebController::contact()` passes `$this->settings()`.

### 7.4 Status & Readiness
- **Copy Status:** **READY** (Simple, zero-risk editorial alignment).
- **Background Status:** **READY (Requires Decision on Background Upload)**.

---

## 8. Running Text / Marquee QA Readiness

### 8.1 Technical Inspection of Marquee Implementation
- Location: `resources/js/Pages/Home.jsx:29-56, 149-154`
- Tailored architecture implemented during Phase 2:
  1. `marqueeItems` uses `useMemo` to replicate `runningTexts` until at least 16 items exist:
     ```js
     while (list.length < 16) {
         list = [...list, ...runningTexts];
     }
     ```
  2. Renders **two identical tracks** with CSS animation:
     ```jsx
     {renderMarqueeTrack(false)}
     {renderMarqueeTrack(true)}
     ```
  3. Animation definition in `tailwind.config.js`:
     ```js
     keyframes: {
         marquee: {
             '0%': { transform: 'translateX(0%)' },
             '100%': { transform: 'translateX(-100%)' },
         }
     },
     animation: {
         'marquee': 'marquee 120s linear infinite',
     }
     ```
  4. Container has `overflow-hidden flex flex-nowrap whitespace-nowrap`. Each track has `shrink-0 min-w-full`.
  5. Local database check: 6 active running text items exist in `running_texts` table.

### 8.2 Client Issue Analysis: *"tdk muncul dalam tampilan monitor 100% ?"*
Potential causes for client's observation:
1. **Speed / 120s Duration:** The animation duration is 120 seconds. It moves slowly (~30px/s); on first load, if a user glanced quickly, they might perceive it as static or take time to notice the animation.
2. **Browser Rendering / Hardware Acceleration:** Without `transform: translate3d(0, 0, 0)` or `will-change: transform`, certain older browser engines or low-power modes might drop frame rendering.
3. **Admin Active Status:** If `is_active` was ever toggled off in Admin, `{runningTexts && runningTexts.length > 0}` would hide the entire bar. (Confirmed currently: all 6 records have `is_active = 1`).
4. **Resolution / Zoom Behavior:** At 100% zoom on 1920×1080 or 2560×1440, each track spans well over 2500px, so there is no gap or empty strip.

### 8.3 Required Manual QA Protocol
- **Status:** **NEEDS VISUAL QA**
- Visual verification must be performed across:
  - Standard desktop monitor at 100% zoom (1920×1080).
  - Scaled display at 125% and 150% Windows scaling.
  - Ultrawide viewport (2560px+).
  - Tablet (768px) and mobile (375px).

---

## 9. Database Read-Only Inspection Summary

| Table | Count | Relevant Columns / Values |
|---|---|---|
| `programs` | 4 records | `id: 1` (`Tova Water Patrol`), `id: 2` (`Eco Education`), `id: 3` (`Clean Lake Action`), `id: 4` (`Biodiversity Watch`). All `order: 1-4`. |
| `activities` | 9 records | Linked to `program_id` 1, 2, 3, 4. |
| `running_texts` | 6 records | All `is_active: 1`, `order: 1-6`. |
| `site_settings` | 1 record | Columns currently: `about_hero_image_url`, `about_lake_image_url`, `org_hero_image_url`, `org_chart_image_url`, `water_quality_map_image_url`, `blog_hero_image_url`. No hero image columns yet for `programs`, `partnerships`, or `contact`. |

*Zero database records, schema definitions, or seeders were modified during this audit.*

---

## 10. Client Decisions Required Before Phase 8B

Before implementation in Phase 8B can begin, the client must resolve the following questions:

### 1. Logo Asset
- Does the client already have the graphic file for the logo with "Tova Matano Care Center" text underneath?
- Can they provide high-resolution PNG or vector SVG files for:
  a. Stacked logo (emblem + text underneath)
  b. Horizontal logo (emblem + text to the right)

### 2. Program #4 Name Spelling
- Should Program #4 be spelled **"Tova Campaign & Event"** or strictly **"Tova Champaign & Event"**?
- When updating program names in Admin, should the URL slugs also be updated (e.g. `eco-education` -> `tova-edu-research`), or should the slugs remain unchanged to preserve existing URLs?

### 3. Background Images for Partnership, Program, and Contact
- The client asked: *"memang tdk ada photo background kah"* on Partnership, Program, and Contact.
- Should we add dedicated Admin upload fields in Site Settings for all three:
  - `partnership_hero_image_url`
  - `program_hero_image_url`
  - `contact_hero_image_url`
  *(All falling back to the current solid brand-navy if no image is uploaded)*?

---

## 11. Recommended Implementation Sequence (Phase 8B)

Once client decisions are confirmed, Phase 8B should proceed in the following order:

1. **Step 1 — Contact Copy Alignment:** (Zero risk) Update subtitle in `Contact.jsx` to include `"Ada saran dan masukan,"`.
2. **Step 2 — Hero Background Architecture (Partnership, Program, Contact):**
   - Single clean migration adding nullable `partnership_hero_image_url`, `program_hero_image_url`, and `contact_hero_image_url` to `site_settings`.
   - Add FileUpload components to `ManageSiteSettings.php` (grouped under "Tampilan Header & Gambar").
   - Update `Partnerships.jsx`, `Programs.jsx`, and `Contact.jsx` to render background images with defensive null handling and brand-navy overlay.
3. **Step 3 — Program Names Update:**
   - Execute client's approved names in Admin or seeder.
   - Update slugs with redirects if requested.
4. **Step 4 — Logo Integration:**
   - Integrate new assets once supplied by the client.
5. **Step 5 — Marquee Visual Verification:**
   - Conduct manual browser testing at 100% monitor zoom.

---

## 12. Explicit Confirmation of System Safety

- **No source code was modified.**
- **No migrations were run or created.**
- **No database records were altered or deleted.**
- **No files in `storage/` or `public/` were altered or deleted.**
- **All uncommitted changes from Phases 1 through 7B remain completely preserved.**
- **Mode:** STRICT READ-ONLY.

---

*Phase 8A Audit is complete. Stopping here and awaiting user approval and client decisions.*
