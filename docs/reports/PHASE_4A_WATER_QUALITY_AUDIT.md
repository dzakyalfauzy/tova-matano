# Phase 4A — Water Quality Station Map Audit

**Status:** Audit complete. Read-only; no source, database, migration, or asset changes.
**Client request (authoritative):** `PAGE : KUALITAS AIR` / `MASUKAN#15` / screenshot `media/image15.png` / `NOTE :` / `Masukkan gambar titik/posisi sampel station`.
**Interpretation:** Add a visual map of sampling-station points/positions to the public Water Quality page.
**Clarification update (2026-11-12):** the client confirmed the map image will be uploaded by the admin via the Admin Panel. No developer-supplied map, GPS coordinates, Leaflet/OpenStreetMap, or Maps API is required. Sections 8–9 below are updated accordingly; the remaining findings are unchanged. See `PHASE_4A_IMPLEMENTATION_READINESS.md` for the revised plan.
**Final review (2026-11-12):** Filament 5.8.4 upload lifecycle, DB-failure/cancel cleanup, server-side validation, and the 6,000 px dimension limit were reviewed and incorporated into the readiness report (§3.1–3.2 and §5). No audit findings changed.

---

## 1. Scope & Inputs

| Item | Finding |
| --- | --- |
| Client paragraphs | `docs/revisi web.docx` P149–P154 only |
| Screenshot | `media/image15.png` — a screenshot of the **current** `/kualitas-air` page, **not** a map image |
| Station count / names | Not supplied in the docx; not required — the map is uploaded as a single image by the admin |
| GPS coordinates | Not supplied and **not required**; seeded coordinates must not be used |
| Official map / basemap | Admin uploads it through the Admin Panel (client clarification) |
| Required layout details | Responsive image display; hide the entire section when no image exists |
| PRD | No station-map requirement; PRD §7.3 only mentions pH/clarity line charts |
| Existing reports | No dedicated Water Quality report exists; `docs/HANDOFF.md` documents the page as "SELESAI (kode), visual TIDAK DIVERIFIKASI" |

The client request is a visual map only. The clarified source is an admin-uploaded image; no geospatial data or third-party map service is involved.

---

## 2. Current Public Page Structure

`resources/js/Pages/WaterQuality.jsx` (800 lines) renders, in order:

1. Navy hero — title `Kualitas Air Danau Matano` + English subtitle.
2. Explanatory note from `waterQualityConfig.explanatory_note`.
3. Filters — sampling point `<select>` and time range `<select>`; measurement count.
4. Summary cards — one per parameter (average + count).
5. Six individual charts — TSS, pH, TDS, DO, temperature, clarity (`ParameterChart`, Recharts).
6. Measurement history — server-side paginated table.

**There is no map, map placeholder, station list card, or visual position element.** Sampling points appear only as `<option>` labels in the filter (`WaterQuality.jsx:552-565`).

---

## 3. Data Model & Actual Database State

### Schema
`water_sampling_points`:

| Column | Type | Null | Default |
| --- | --- | --- | --- |
| `id` | bigint unsigned | NO | — |
| `name` | varchar(255) | NO | — |
| `description` | text | YES | NULL |
| `coordinates` | varchar(255) | YES | NULL |
| `is_active` | tinyint(1) | NO | 1 |
| `created_at`, `updated_at` | timestamp | YES | NULL |

`water_quality_records.water_sampling_point_id` is a nullable FK with `nullOnDelete` (`database/migrations/2026_10_01_084401_add_sampling_point_to_water_quality_records_table.php`).

`coordinates` is a **single free-text string**, not separate `latitude`/`longitude` numerics. There is no spatial index, no geo column, and no coordinate validation.

### Actual local data (read-only query, 2026-11-12)

| ID | Name | Coordinates | Active | Records |
| --- | --- | --- | --- | --- |
| 1 | Titik A — Muara Sungai Petea | `-2.4321, 121.3210` | yes | 21 |
| 2 | Titik B — Tengah Danau (Utara) | `-2.4500, 121.3400` | yes | 20 |
| 3 | Titik C — Dermaga Sorowako | `-2.5100, 121.3500` | yes | 22 |
| 4 | Titik D — Teluk Timur | `-2.4800, 121.3900` | yes | 24 |
| 5 | Titik E — Muara Sungai Larona | `-2.5300, 121.3100` | yes | 21 |
| 6 | Titik F — Teluk Barat (Zona Konservasi) | `-2.4600, 121.2900` | yes | 23 |

- 6 sampling points, **all active**.
- 131 records total; **131 have a sampling point**; 0 orphaned.
- All six coordinates follow one `lat, lng` string format.
- **Critical provenance finding:** these coordinates come from demo seed data, `database/seeders/DatabaseSeeder.php:624-629`. They have **not** been verified by the client or against an official source. They must not be presented publicly as authoritative positions without client confirmation.

---

## 4. Admin Management Audit

### `WaterSamplingPoints`
- Form (`WaterSamplingPointForm.php`): `name` (required), `coordinates` (free `TextInput`, max 255, **no format validation**), `description` (textarea), `is_active` (toggle, default true).
- Table (`WaterSamplingPointsTable.php`): name, coordinates, active icon; active filter; edit/delete/bulk delete.
- No map picker, no geocoding, no coordinate preview, no lat/lng validation, no unique constraint on name.

### `WaterQualityRecords`
- Form selects a sampling point via relationship (required, searchable, preloaded) and records the six parameters.
- Table shows sampling point name, filters by active point and date range, exports CSV/Excel (`WaterQualityRecordExporter`).
- Records cannot be created without a sampling point (form is required), although the FK is nullable for legacy rows.

**Implication:** the admin UI can maintain names/descriptions/coordinates, but there is no map-oriented workflow and no data-quality guardrails for coordinates.

---

## 5. Map/Station Asset Audit

| Location | Result |
| --- | --- |
| `public/image/` | Only `logo_tova_matano.png` |
| `public/storage/` | Hero slides, founders, gallery, members, org-people, partnerships, programs, settings, blog, livewire-tmp — **no map/basemap asset** |
| React components | No Leaflet/Mapbox/Google Maps dependency or component |
| `package.json` | `recharts` only for charts; no mapping library |
| Client docx media | 20 images extracted; all are screenshots/photos — none is a map of Lake Matano |

No offline-safe station map currently exists in the project.

**Clarification:** this is not a blocker. The client will upload the map image via the Admin Panel; the implementation must therefore provide the upload/storage facility rather than bundle an asset or plot coordinates.

---

## 6. Gap vs Client Request

| Requirement | Current state | Gap |
| --- | --- | --- |
| Show a picture of sampling station points/positions | Only a filter dropdown of names | **Not implemented** |
| Station names/descriptions | Present in DB and admin | Not surfaced visually |
| Coordinates | Present as free-text, seeded/unverified | Not parsed, validated, or displayed |
| Map source/legend | None | Admin uploads the image (clarified); no coordinates or map API |
| Accessibility/fallback | N/A | Alt text + optional credit; whole section hidden when no image |

The literal request ("masukkan gambar") is satisfied by an admin-managed static image. No interactive map is required or planned.

---

## 7. Technical Risks & Constraints

1. **Unverified geodata:** seeded coordinates are demonstration values. They will not be used for this feature; publishing them as real positions remains prohibited.
2. **Coordinate format:** `coordinates` is a string. No parsing, plotting, or display is part of the map feature, so no parser is required.
3. **Dependency policy:** no Leaflet/Mapbox/Google/tile dependency will be added; the clarified approach is dependency-free.
4. **Offline/local reliability:** a stored static image is offline-safe and requires no network or tile provider.
5. **Attribution/licensing:** an admin-uploaded map may carry its own license/source; the optional credit field covers attribution.
6. **Upload security:** file type, size, dimensions, random filenames, and server-side validation are required for the admin upload.
7. **Accessibility:** the map needs meaningful alt text and an optional credit; no text station list is required because stations are not plotted from data.
8. **Visual integration:** the page already has a strong navy/teal system and six dense charts; the map must not compete with the explanatory note or filters.

---

## 8. Options (Superseded by Client Clarification)

The earlier Options A/B/C (client-supplied static map, Leaflet/OSM, full geodata model) are **no longer applicable**. The client resolved the source decision: **the admin uploads the map image through the Admin Panel**.

### Selected approach — Admin-managed static map
- Add an upload field to the existing `ManageSiteSettings` page and two nullable columns to `site_settings`.
- Store the image on the Laravel `public` disk; store only the relative path and optional credit in the database.
- Public page renders the image responsively and hides the whole section when absent.
- **Pros:** matches the clarified requirement; no external dependency/API; offline-safe; admin-controlled; no station-data changes.
- **Cons:** static image; admin is responsible for map accuracy; no marker interactivity.
- **Prerequisite:** none beyond Phase 4B approval.

### Explicitly rejected
- **Developer-supplied map asset** — client will upload it.
- **Leaflet/OpenStreetMap/Maps API** — explicitly not needed.
- **Plotting seeded coordinates** — unverified and must not be used.
- **New settings table/resource/menu** — existing settings facility is sufficient.

**Recommendation:** implement the admin-managed static map as specified in `PHASE_4A_IMPLEMENTATION_READINESS.md`.

---

## 9. Findings Summary & Resolved Inputs

**Summary**
- The page is feature-rich but has **no map**; sampling points are only a filter dropdown.
- The data model supports a station list today (`name`, `description`, `coordinates`, `is_active`) and the controller already sends active `samplingPoints` to the page (`WebController.php:339`).
- The only coordinates in the system are **seeded demo values**, so they must not be used for the map.
- No map asset or mapping library exists; the client clarified the map will be uploaded by the admin.

**Resolved by clarification**
1. ~~Supply a station map image or confirm coordinates/approve Leaflet~~ → admin uploads the image via the Admin Panel.
2. ~~Confirm station labels for a generated legend~~ → labels are part of the uploaded image.
3. ~~Decide marker interaction~~ → not applicable; static image.
4. ~~Provide the map image~~ → admin provides/uploads it.

**Optional input only**
- A preferred credit/source line for the image (otherwise the credit field stays empty).

**No code changes were made in Phase 4A.** Working tree remains as delivered by Phase 3B-6 (see `PHASE_4A_IMPLEMENTATION_READINESS.md` §10).
