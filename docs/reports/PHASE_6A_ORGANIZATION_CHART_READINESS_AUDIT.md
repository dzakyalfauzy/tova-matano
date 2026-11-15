# PHASE 6A — ORGANIZATION CHART READINESS & ARCHITECTURE AUDIT

**Project:** Tova Matano Care Center (TMCC)  
**Date:** November 14, 2026  
**Status:** AUDIT COMPLETE — READY FOR CLIENT DECISION  
**Reference Document:** `docs/reports/TMCC_MASTER_REVISION_ROADMAP.md`  
**Client Feedback Source:** `docs/revisi web.docx`

---

## 1. Executive Summary & Client Requirement

### 1.1 Client Feedback Verbatim
In `docs/revisi web.docx`, the client left the following explicit notes regarding the Organization page (`/struktur-organisasi`):
> *"AKAN DIUBAH DI ADMIN"*
>
> *"CATATAN : PSAYA SUDAH MELAKUKAN PERUBAHAN DI STRUKTUR ORGANISASI TAPI TIDAK MERUBAH STRUKTUR DI BAGAN"*

### 1.2 Core Problem Statement
1. The lower section of `/struktur-organisasi` (the category accordion with people grids and role lists) is dynamically loaded from MySQL via `OrgCategory` and `OrgPerson`.
2. When the client updated categories and people in the Filament Admin panel, they expected the top visual tree diagram (**"Bagan Organisasi"**) to reflect those changes.
3. However, lines 100–257 in `resources/js/Pages/Organization.jsx` contain a **100% hardcoded HTML/CSS tree diagram** featuring 7 static tiers (Pelindung → Penasehat → Pengarah → Badan Litbang/Dewan Eksekutif/Dewan Pakar → Direktur Utama TMCC → 4 Direktur/Sekjen → 6 Divisi).
4. As a result, no change made in Admin has ever had any effect on the visual chart.

---

## 2. Public Page Inspection (`Organization.jsx` & `WebController.php`)

### 2.1 WebController Inspection (`app/Http/Controllers/WebController.php:109-115`)
```php
public function organization()
{
    return Inertia::render('Organization', [
        'categories' => OrgCategory::with(['people' => fn ($q) => $q->orderBy('order')])->orderBy('order')->get(),
        'settings' => $this->settings(),
    ]);
}
```
- Only two props are passed: `categories` and `settings`.
- `settings` includes all columns from `site_settings`, notably `org_hero_image_url` and `org_chart_image_url`.

### 2.2 Sections in `Organization.jsx`
| Section | Line Range | Data Source | Behavior |
|---|---|---|---|
| **Hero Header** | Lines 74–97 | Dynamic (`settings.org_hero_image_url`) with fallback to logo | Displays title and dynamic background |
| **Bagan Organisasi (Visual Chart)** | Lines 100–257 | **100% Hardcoded JSX** | Renders static nested boxes and fixed connector lines. Completely ignores props. |
| **Bagan Organisasi Banner** | Lines 252–255 | Hardcoded JSX | "ANGGOTA DAN SUKARELAWAN" divider bar |
| **Accordion Section** | Lines 260–319 | Dynamic (`categories` prop) | Headless UI `Disclosure` loop rendering `ROLE_LIST` or `PERSON_GRID` for each category |

### 2.3 The Hardcoded Chart Anatomy
The hardcoded chart renders:
- **Tier 1:** `PELINDUNG` (Single box)
- **Tier 2:** `PENASEHAT` (Single box)
- **Tier 3:** `PENGARAH` (Single box)
- **Tier 4 (Branch of 3):** `Badan Penelitian dan Pengembangan Organisasi` | `Dewan Eksekutif Organisasi` | `Dewan Pakar`
- **Tier 5:** `Direktur Utama TMCC` (Single box)
- **Tier 6 & 7 (4 Columns under Dirut):**
  - Col 1: `Sekretaris Jenderal` → sub-boxes: `Divisi Legal dan Advokasi`, `Divisi Media dan Kampanye Lingkungan`
  - Col 2: `Direktur Operasional` → sub-boxes: `Divisi Pelestarian dan Penyelamatan Danau`, `Divisi Pengelolaan Asset, Perlengkapan dan Transportasi Air`
  - Col 3: `Direktur Eksternal Relation` → sub-boxes: `Divisi Pengembangan Masyarakat dan Usaha Penopang`, `Tim Kreatif dan Event Organizer`
  - Col 4: `Direktur Keuangan` (no sub-divisions)

*Noticeable discrepancy:* The chart lists roles like "PENGARAH", "Direktur Utama TMCC", "Direktur Operasional", etc., whereas the actual categories in the database are:
1. Pelindung
2. Penasehat
3. Dewan Pakar
4. Badan Eksekutif
5. Pengurus Inti
6. Divisi

The database structure and the hardcoded chart structure **do not match**.

---

## 3. Database & Models Inspection

### 3.1 Existing Models and Schemas
#### `org_categories` Table
- `id` (bigint unsigned, PK)
- `name` (varchar 255)
- `description` (varchar 255, nullable)
- `display_type` (enum: `ROLE_LIST`, `PERSON_GRID`)
- `order` (unsigned int, default 0)
- `timestamps`

#### `org_people` Table
- `id` (bigint unsigned, PK)
- `org_category_id` (foreignId -> `org_categories.id`, cascadeOnDelete)
- `name` (varchar 255, nullable)
- `role_title` (varchar 255)
- `photo_url` (varchar 255, nullable)
- `divisi_name` (varchar 255, nullable)
- `order` (unsigned int, default 0)
- `timestamps`

### 3.2 Hierarchy & Relationship Capabilities of Current Schema
| Capability | Supported in Current Schema? | Details |
|---|---|---|
| **Parent-Child Hierarchy** | **NO** | Neither `org_categories` nor `org_people` has a `parent_id` column. The hierarchy is flat (1 category has N people). |
| **Multi-tier Tree Structure** | **NO** | There is no mechanism in the DB to represent that "Sekretaris Jenderal" reports to "Direktur Utama", or that "Divisi Legal" reports to "Sekretaris Jenderal". |
| **Sub-divisions** | **PARTIAL / INFORMAL** | `OrgPerson` has a string column `divisi_name`, but it is just a plain text attribute, not a relational node. |
| **Custom Ordering** | **YES** | Both `org_categories` and `org_people` have an integer `order` column with reorderable table support. |
| **Multiple People per Category** | **YES** | One-to-many relationship (`OrgCategory->hasMany(OrgPerson)`). |

### 3.3 Current Live Database Records
A read-only inspection of the local database revealed:
- **6 Org Categories:**
  1. `Pelindung` (order 1, ROLE_LIST) — 2 people: *H. Andi Sudirman Sulaiman* (Gubernur Sulsel), *Ir. H. Irwan Bachri Syam* (Bupati Luwu Timur).
  2. `Penasehat` (order 2, ROLE_LIST) — 2 people: *Prof. Dr. Arifin*, *Dr. Sri Wahyuni*.
  3. `Dewan Pakar` (order 3, PERSON_GRID) — 2 people: *Dr. Hendra Kurniawan*, *Dr. Mayang Sari*.
  4. `Badan Eksekutif` (order 4, PERSON_GRID) — 3 people: *Rina Kusuma* (Sekjen), *Ahmad Fadli* (Bendahara Umum), *Dewi Lestari* (Kepala Divisi Riset).
  5. `Pengurus Inti` (order 5, PERSON_GRID) — 2 people: *Budi Santoso* (Koordinator Lapangan), *Siti Rahmawati* (Koordinator Relawan).
  6. `Divisi` (order 6, PERSON_GRID) — 3 people: *Yusuf Maulana*, *Nina Aprilia*, *Rico Pratama*.

---

## 4. Admin Panel Inspection (Filament Resources)

### 4.1 `OrgCategoryResource`
- Form: `name`, `display_type` (Select: `ROLE_LIST`, `PERSON_GRID`), `description`.
- Table: `name`, `description`, `display_type`, drag-and-drop row reordering (`->reorderable('order')`).
- **Limitation:** Cannot specify parent category, tier level, or chart layout coordinates.

### 4.2 `OrgPersonResource`
- Form: `org_category_id` (BelongsTo select), `name`, `role_title`, `divisi_name`, `photo_url` (FileUpload to `org-people` on `public` disk).
- Table: Category name, photo thumbnail, name, role title, division name, drag-and-drop reordering.
- **Limitation:** Cannot link a person or role to a supervisor, parent position, or tree node.

### 4.3 `ManageSiteSettings` (Global Settings)
- In `ManageSiteSettings.php` lines 99–108:
  - `org_hero_image_url`: FileUpload for the page header background.
  - `org_chart_image_url`: FileUpload with label `"Gambar Bagan Organisasi (Opsional)"`, stored in `settings` directory on `public` disk.
- Current database value: `org_chart_image_url` is `NULL`.
- In `Organization.jsx`: **`settings.org_chart_image_url` is completely unused!** The file upload field exists in Admin, but the frontend never renders it.

---

## 5. Architectural Comparison: Option A vs. Option B

### 5.1 Option A: Database-Driven Dynamic Tree Chart
Generate the visual tree diagram dynamically in React using structured data from the database.
- **Schema Requirements:** Requires adding `parent_id` (or tree hierarchy columns/table) or attempting to heuristically map flat `OrgCategory` records into fixed vertical tiers.
- **Visual Complexity:** Organization charts are notoriously difficult to render dynamically and cleanly across mobile (375px), tablet (768px), and desktop (1280px). When an organization has unequal branch depths, multiple co-heads, or matrix relationships, algorithmic layout engines or custom CSS tree branches frequently break, overlap, or wrap unreadably.
- **Admin Usability:** The client would need to manage a tree-building interface (e.g. parent dropdowns, tier numbers, or node connections) in Filament, which is complex and prone to user input errors that crash the tree layout.

### 5.2 Option B: Admin-Uploaded Official Chart Image
Utilize the existing `settings.org_chart_image_url` field in `site_settings` and `ManageSiteSettings`.
- **Schema Requirements:** **Zero schema changes**. The column `org_chart_image_url` already exists in `site_settings`.
- **Admin Usability:** Extremely simple and intuitive. The client exports their official visual chart (from Canva, Visio, Illustrator, PowerPoint, or Figma) as a high-resolution PNG, SVG, or WebP, and uploads it in Filament Settings (`Pengaturan Website`).
- **Frontend Display:** `Organization.jsx` renders the uploaded image in a responsive container with zoom/lightbox capability (click-to-expand) or clean high-DPI scaling. If no image is uploaded, it can gracefully display a fallback or omit the chart section while keeping the dynamic accordion.
- **Accordions Preserved:** The bottom accordion (`Pelindung`, `Penasehat`, `Dewan Pakar`, etc.) remains 100% dynamic from `OrgCategory` and `OrgPerson`, allowing individual people, bios, and photos to be updated dynamically in Admin.

### 5.3 Comparison Matrix

| Criteria | Option A: Dynamic DB Chart | Option B: Uploaded Image Chart |
|---|---|---|
| **Admin Editing Workflow** | Complex: Admin must configure parents/tiers/nodes for every person or role. High risk of broken connections. | Simple: Admin designs/exports chart and uploads a single image in Site Settings. |
| **Automatic Public Updates** | High maintenance: Adding a person or role immediately alters the tree layout, potentially breaking alignment. | Clean separation: Visual chart is updated when official governance changes; member/personnel lists update continuously. |
| **Schema Requirements** | Requires migrations (`parent_id`, tree nodes, or coordinate fields). | **None** (`site_settings.org_chart_image_url` already exists). |
| **Data Migration Risk** | Moderate to High: Existing 6 categories and 14 people must be retrofitted into tree nodes. | **Zero**: No migrations, no data changes. |
| **Visual Flexibility** | Low/Rigid: Constrained by CSS flexbox/grid tree nodes. Difficult to represent special boards (e.g. lateral advisory). | **High**: Client can use any visual design, branding, badges, and lateral lines they desire. |
| **Mobile Responsiveness** | Very difficult: Complex multi-tier trees overflow or compress to unreadable text on mobile screens (375px–768px). | **High**: Image scales smoothly; can be equipped with pan/zoom or full-screen modal preview. |
| **Maintenance** | High: Any future organizational restructure requires developer adjustments to layout algorithms. | **Low**: Client manages it completely independently without code changes. |
| **Complexity** | High (Frontend graph rendering + Backend tree structure). | Low (Render image with responsive wrapper & lightbox). |
| **Long-term Scalability** | Prone to layout regressions as the organization grows. | Highly stable across all organizational restructures. |

---

## 6. Technical Recommendation

### Recommendation: **OPTION B (Admin-Uploaded Official Chart Image) with Option A Fallback/Hybrid Strategy**
**Technical Justification:**
1. **Existing Infrastructure Already Anticipated This:** The database already has `site_settings.org_chart_image_url`, and Filament's `ManageSiteSettings` already has the upload field labeled `"Gambar Bagan Organisasi (Opsional)"`. The only missing link is that `Organization.jsx` was hardcoded instead of consuming `settings.org_chart_image_url`.
2. **Client Realities:** Real-world NGO and environmental organization charts (like TMCC) feature complex lateral advisory boards, patron links, and dotted reporting lines that automated CSS box trees cannot represent elegantly without a dedicated flowcharting suite.
3. **Responsive Stability:** Complex tree diagrams invariably break on mobile viewports (375px–768px). A high-resolution image with click-to-zoom/lightbox gives users the best viewing experience on both smartphones and desktops.
4. **Best of Both Worlds:**
   - The **visual chart** is presented via the official graphic uploaded in Admin (`org_chart_image_url`).
   - The **detailed personnel lists** (names, roles, photos, divisions) are dynamically driven by the existing `OrgCategory` and `OrgPerson` accordion, which is already fully functional.

---

## 7. Required Client Confirmations

Before any implementation begins, the client must answer the following three questions:

1. **Chart Delivery Choice:**
   - **Choice 1 (Recommended):** Would you prefer to upload an official organization chart image (JPEG/PNG/WebP/SVG) via the Admin Settings panel (`Pengaturan Website`) that automatically displays on `/struktur-organisasi` with click-to-expand/zoom?
   - **Choice 2:** Or do you require a fully automated, programmatic box-diagram generated by database relationships (which would require restructuring the Admin panel and database)?
2. **Current Structure Accuracy:**
   - In Admin (`Kategori Organisasi`), there are currently 6 categories: *Pelindung, Penasehat, Dewan Pakar, Badan Eksekutif, Pengurus Inti, Divisi*. Is this category list already final, or are you planning to restructure these categories?
3. **Bagan Replacement:**
   - If an official image is uploaded in Admin, should it completely replace the hardcoded "Bagan Organisasi" section on the public page?

---

## 8. Implementation Plans

### Phase 6B-B: Implementation Plan for OPTION B (Recommended: Uploaded Image Chart)
*Estimated Effort: Low | Risk: Zero Schema Risk*
- **Files to Change:**
  - `resources/js/Pages/Organization.jsx`: Replace lines 100–257 with a responsive container that checks `settings?.org_chart_image_url`. If present, render the image with responsive sizing and a modal lightbox (click-to-zoom). If null, either render a clean placeholder or keep the dynamic accordion prominent.
  - `app/Filament/Pages/ManageSiteSettings.php`: Verify upload rules (support WebP, PNG, JPEG, SVG up to 5MB) and ensure helper text clearly explains where it appears.
- **Database Impact:** None. Column already exists.
- **Admin Changes:** None required (field already exists; can add helper text / preview).
- **Public Frontend Changes:** Responsive image with zoom modal.
- **Tests Required:**
  - Feature test in `tests/Feature/OrganizationPageTest.php`: Assert page renders 200, handles null `org_chart_image_url`, displays `/storage/{org_chart_image_url}` when populated, and correctly delivers category accordion props.
- **Rollback:** Trivial git checkout of `Organization.jsx`.

---

### Phase 6B-A: Implementation Plan for OPTION A (Dynamic Database-Driven Tree)
*Estimated Effort: High | Risk: High Schema & Responsive Risk*
- **Files to Change:**
  - `database/migrations/*`: Add `parent_id`, `tier_level`, or create dedicated `org_nodes` table.
  - `app/Models/OrgCategory.php` or `OrgPerson.php`: Add recursive tree relationships (`parent()`, `children()`).
  - `app/Filament/Resources/*`: Add tree node builder or parent-selection fields.
  - `resources/js/Pages/Organization.jsx`: Build a dynamic recursive tree rendering algorithm with responsive SVG connectors or grid breakpoints.
- **Database Impact:** New migration required.
- **Admin Changes:** Complex form fields to establish supervisor/child relationships.
- **Tests Required:** Multi-level tree traversal unit tests, circular reference prevention tests, responsive layout snapshot tests.
- **Risks:** Broken layouts when categories have unequal depth or unbalanced children.

---

## 9. Current Status & Safety Verification

- **Audit Status:** COMPLETED.
- **Production / Data Safety:** No application code was modified. No migrations were created. No database records were altered.
- **Readiness:** **READY FOR CLIENT DECISION.**

*Awaiting client response to the 3 questions in Section 7 before proceeding to implementation.*
