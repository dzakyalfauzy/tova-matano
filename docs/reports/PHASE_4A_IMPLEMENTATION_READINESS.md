# Phase 4A — Water Quality Implementation Readiness (Revised)

**Status:** Revised audit/readiness only. Implementation not started. No source, database, migration, configuration, or asset changes made.
**Revision date:** 2026-11-12
**Final review:** Filament 5.8.4 lifecycle, failure/cancel cleanup, server-side validation, and test coverage verified; plan updated below.
**Supersedes:** the earlier Phase 4A readiness draft (client-supplied map image / Leaflet options).
**Companion:** `docs/reports/PHASE_4A_WATER_QUALITY_AUDIT.md` (findings preserved; only the superseded recommendation is updated).

---

## 1. Clarified Requirement & Verdict

**Client clarification:** the sampling-station map will be uploaded by the admin through the Admin Panel. Phase 4B therefore does **not** need a developer-supplied map, GPS coordinates, Leaflet, OpenStreetMap, a Maps API, or a tile provider.

**Verdict: READY for Phase 4B once approved.** No external/client input blocks implementation.

Required behavior:

1. Admin can upload a map image in the Admin Panel.
2. Admin can preview, replace, and remove/delete it.
3. The file is stored with Laravel Storage on the `public` disk; the database stores only the relative path plus optional credit metadata.
4. Upload validation restricts JPEG/PNG/WebP, enforces a size limit, and applies safe-upload practices.
5. The public Water Quality page displays the image responsively.
6. When no image exists, the entire map section is hidden.
7. Seeded `coordinates` are never used for this feature.
8. Sampling-station data, the 131 measurement records, six parameters, charts, filters, thresholds, and pagination remain untouched.

**Preserved audit findings**
- 6 active sampling points, 131 records (20–24 per point), 0 orphaned records.
- No map asset, mapping component, or mapping dependency exists today.
- `water_sampling_points.coordinates` exists as an unverified, seeded free-text string (`DatabaseSeeder.php:624-629`); it will remain unused by this feature.
- The public page currently shows sampling points only as a filter `<select>`.

---

## 2. Settings Placement Audit (Decision)

**Decision: extend the existing `site_settings` singleton and the existing `ManageSiteSettings` Filament page. Do not create a new table, resource, menu, or page.**

Why this is the right location:

| Existing facility | Relevance |
| --- | --- |
| `site_settings` table (single row) | Already stores site-wide media paths (`about_hero_image_url`, `about_lake_image_url`, `org_hero_image_url`, `org_chart_image_url`) and credits (`about_lake_image_credit`) |
| `App\Models\SiteSettings` | Central singleton with `$fillable`; already shared publicly through `WebController::settings()` |
| `App\Filament\Pages\ManageSiteSettings` | Existing Admin Panel settings page in group **Pengaturan**, already uses `FileUpload` with `disk('public')` and `directory('settings')` |
| Public `WaterQuality` page | Already receives `settings` as an Inertia prop (`WebController.php:350`) |

Rejected alternatives:

| Alternative | Why rejected |
| --- | --- |
| New `water_quality_settings` table + Filament resource/menu | Duplicates the established settings singleton; adds a table, migration, resource, and navigation for two fields |
| New dedicated Filament page | Duplicates `ManageSiteSettings`; settings would be split across two admin locations |
| `config/water_quality.php` / `.env` only | Not editable by the admin; contradicts the clarified requirement |
| Store the image on `WaterSamplingPoint` | The map is one page-level image, not per-station data; would require choosing a "primary" station |
| JSON blob inside `about_content` | Belongs to the About page domain; wrong semantics and bypasses dedicated validation/cleanup |

**Authorization note (preserved architecture):** the Admin Panel has no granular permission system; every authenticated panel user can already edit `ManageSiteSettings`. The map upload inherits the same access model. This is documented, not a regression.

---

## 3. Proposed Admin Panel UX

Add a new collapsible section to `ManageSiteSettings`:

**Section: "Peta Titik Sampling — Halaman Kualitas Air"**

| Field | Type | Behavior |
| --- | --- | --- |
| `water_quality_map_image_url` | `FileUpload` (single image) | Upload, preview, replace, remove; `disk('public')`, `directory('settings/water-quality')` |
| `water_quality_map_image_credit` | `TextInput` (optional, max 255) | Caption/credit shown under the map; empty hides the credit line |

Field configuration:

```php
FileUpload::make('water_quality_map_image_url')
    ->label('Gambar Peta Titik Sampling')
    ->helperText('Unggah peta yang sudah memuat posisi titik sampling. Format JPEG, PNG, atau WebP; maksimal 5 MB. Kosongkan untuk menyembunyikan section peta di halaman publik.')
    ->image()
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
    ->maxSize(5120)
    ->rules(['dimensions:max_width=6000,max_height=6000'])
    ->disk('public')
    ->directory('settings/water-quality')
    ->visibility('public')
    ->preventFilePathTampering()
    ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
        $path = $component->saveUploadedFile($file);

        if (filled($path)) {
            $this->pendingMapUploads[] = $path;
        }

        return $path;
    })
    ->imagePreviewHeight('240')
    ->openable()
    ->downloadable()
    ->columnSpanFull();

TextInput::make('water_quality_map_image_credit')
    ->label('Kredit/Sumber Peta (Opsional)')
    ->maxLength(255)
    ->helperText('Tampil sebagai keterangan kecil di bawah peta. Kosongkan jika tidak diperlukan.');
```

Order matters: `image()` sets `acceptedFileTypes(['image/*'])` (which allows SVG); the explicit `acceptedFileTypes([...])` call must come **after** it. `acceptedFileTypes()` and `maxSize()` register server-side rules; the browser `accept` attribute is not trusted.

**Custom-page requirement for tamper protection:** `preventFilePathTampering()` compares submitted string paths against the form's record. `ManageSiteSettings` is a custom page with no record by default. Set the record in `form()` — `$schema->record(SiteSettings::firstOrCreate([]))` — **not only in `mount()`**, because Livewire runs `mount()` on the initial request while `form()` is rebuilt for each subsequent request (including `save()`). Then `mount()` can continue to `$this->form->fill($data)`. Without a record on the save request, the existing stored path is treated as unauthorized and an unchanged save fails validation. With the record set, unchanged existing paths are authorized, while a forged path is rejected before it can be stored or deleted. Add a test for both cases.

Admin flow:

1. Open **Pengaturan → Pengaturan Situs**.
2. Expand the map section, upload an image, preview it, optionally fill the credit.
3. Save; success notification uses the existing page notification.
4. Replace: upload a new image; the old file is deleted only after a successful DB save.
5. Remove: clear the upload field and save; the DB value becomes `null` and the old file is deleted after a successful save.
6. Public page shows the section only when a path is stored.

### 3.1 Filament 5.8.4 Lifecycle (Verified)

Verified against the installed `filament/filament` **v5.8.4** (`vendor/filament/forms/src/Components/BaseFileUpload.php`):

| Stage | Actual behavior |
| --- | --- |
| Select/upload | Livewire stores the new file on the temporary upload disk (`livewire-tmp`); the form state holds a `TemporaryUploadedFile` |
| Validation | `getState()` validates first; `acceptedFileTypes()` → server-side `mimetypes:` rule and `maxSize()` → server-side `max:` rule run against the temporary file |
| Before dehydration | `saveUploadedFiles()` runs on `beforeStateDehydrated` and permanently stores the temp file via the `saveUploadedFileUsing` callback |
| Permanent storage | `saveUploadedFile()` writes to `disk('public')` + `directory('settings/water-quality')` with a ULID filename, then deletes the temp file |
| State after storage | State becomes the relative path string; `getState()` returns it |
| Remove in UI | `removeUploadedFile()` unsets state; `deleteUploadedFile()` only deletes if a `deleteUploadedFileUsing` callback is configured (none here) |
| Cancel | No permanent storage and no DB write; temp files are cleaned by Livewire; existing file remains |
| Load | `hydrateFiles()` hides paths whose files no longer exist on disk |

This confirms the form component, not the controller, owns file storage; the custom page `save()` must handle DB failure and old-file cleanup explicitly.

Do **not** configure `deleteUploadedFileUsing()` to delete the file immediately when the admin clicks remove. At that moment the database still references the path; deleting immediately would break the public image if the admin cancels. Leaving the callback unset (Filament's default) and deleting in `save()` after a successful commit is the correct cancel-safe lifecycle.

### 3.2 Failure, Cancel, Replace & Remove Handling

The new file is permanently stored **during** `$this->form->getState()`, before the DB update. A naïve `getState(); update();` can therefore leave an orphan file if `update()` fails. The implementation must track newly stored paths and compensate.

Recommended `save()` flow:

```php
public function save(): void
{
    $settings = SiteSettings::firstOrCreate([]);
    $oldPath = $settings->water_quality_map_image_url;
    $this->pendingMapUploads = [];

    try {
        $state = $this->form->getState(); // validates; stores new file during dehydration
        DB::transaction(fn () => $settings->update($state));
    } catch (Throwable $exception) {
        foreach (array_unique($this->pendingMapUploads) as $path) {
            if ($path !== $oldPath) {
                Storage::disk('public')->delete($path);
            }
        }

        $this->pendingMapUploads = [];
        $this->data['water_quality_map_image_url'] = $oldPath;

        throw $exception;
    }

    $newPath = $settings->water_quality_map_image_url;

    if (filled($oldPath) && $oldPath !== $newPath) {
        // Best effort: never fail the save because cleanup failed.
        Storage::disk('public')->delete($oldPath);
    }

    $this->pendingMapUploads = [];
}
```

- **DB failure after new file storage:** every path recorded by `saveUploadedFileUsing` is deleted (except the old path), so no orphan remains; the transaction leaves the DB on the old path. After cleanup, reset the map field state to the old path (or `null`) so the form does not retain a reference to the deleted file; then rethrow the original exception.
- **Old file deletion:** only after the DB transaction commits and only when the path changed. The old file is never deleted before the new value is durable.
- **Cancel / navigate away:** `save()` is not called; no permanent storage occurs (only `livewire-tmp`), `removeUploadedFile()` does not delete string paths without a `deleteUploadedFileUsing` callback, and the DB/file reference is untouched.
- **Remove then save:** state is empty → DB `null`; old file is deleted only after commit. If the save fails, old file/DB remain.
- **Replace then save:** new file stored; DB commits; old file deleted. If DB fails, new file is removed and old remains.
- **Path tampering:** `preventFilePathTampering()` is enabled; string paths must match the record's original path, so a tampered path cannot reach cleanup or be stored.
- **Rollback caveat:** a migration rollback drops the columns but does not delete stored files; document manual cleanup.

**Why track paths with `saveUploadedFileUsing`:** comparing only `$state['water_quality_map_image_url']` after `getState()` covers DB-update failures, but tracking also covers an exception after the file was stored and before `getState()` returns. The callback is a supported Filament 5 hook and still delegates to the standard `$component->saveUploadedFile($file)`.

---

## 4. Data & Storage Design

### Migration (required)
New migration: `database/migrations/2026_11_12_000000_add_water_quality_map_to_site_settings_table.php`

```php
Schema::table('site_settings', function (Blueprint $table) {
    $table->string('water_quality_map_image_url')->nullable()->after('org_chart_image_url');
    $table->string('water_quality_map_image_credit')->nullable()->after('water_quality_map_image_url');
});
```

- Two nullable string columns; **no backfill**, no default, no index.
- `down()` drops both columns. Dropping columns does **not** delete uploaded files; document manual storage cleanup for rollback.
- `water_quality_map_image_url` follows the existing `_image_url` naming convention but stores a **relative path** (e.g. `settings/water-quality/AbC123.webp`), exactly like `about_hero_image_url`. It is not an absolute URL.

### Storage
- Disk: `public` (`storage/app/public`).
- Directory: `settings/water-quality`.
- Filename: Filament default random/hashed name; never preserve client filenames.
- Public URL: `/storage/{relative-path}` (existing convention used by About/Home).
- `php artisan storage:link` must exist in the environment; it is already required for existing site images.
- Production backup must include `storage/app/public/settings/water-quality`.

### Model
Add both columns to `App\Models\SiteSettings::$fillable`. No cast is needed. No accessor is needed; the frontend builds `/storage/...` like existing pages. No changes to `WaterSamplingPoint`, `WaterQualityRecord`, thresholds, or config.

### Metadata
- One optional credit/source string is sufficient.
- Accessibility alt text is generated as a stable Indonesian label ("Peta titik sampling kualitas air Danau Matano"). A custom alt column is **not** required; add one only if the client wants per-image alt text.
- No EXIF/geo metadata is stored or used.

---

## 5. Validation & Upload Security

| Control | Implementation |
| --- | --- |
| File type | `acceptedFileTypes(['image/jpeg','image/png','image/webp'])` **after** `image()`; Filament registers a server-side `mimetypes:` rule |
| Size | `maxSize(5120)` (5 MB, KB unit); Filament registers a server-side `max:` rule |
| Image dimensions | `dimensions:max_width=6000,max_height=6000` (server-side); see rationale below |
| SVG/GIF | Explicitly excluded (SVG can carry scripts; GIF is not needed) |
| File content | Laravel MIME validation checks actual content, not just the extension; `image()`'s `image/*` default is overridden by the explicit MIME list |
| File name | Filament ULID filename; no user-controlled path or filename |
| Path handling | `preventFilePathTampering()` + store only the path returned by Filament |
| Public exposure | `public` disk is intentional; the map is public content |
| Authorization | Existing Admin Panel access; no new bypass |
| XSS | Rendered with React `<img>`; no HTML injection; SVG disallowed |
| Orphan files | Newly stored paths tracked during `getState()` and deleted if the DB transaction fails |
| Old-file deletion | Only after the DB transaction commits and only when the path changed; best effort |
| Error handling | Cleanup must not mask the original exception; DB rollback leaves the old path intact |

**Server-side enforcement:** `acceptedFileTypes()` and `maxSize()` are not merely client hints in Filament 5. They add `mimetypes:` and `max:` rules to the field, and `BaseFileUpload` validates temporary files against those rules (`getValidationRules()` + the per-file validator). The explicit `dimensions` rule is also server-side. Do not rely on the browser `accept` attribute.

**Dimension limit recommendation:** use **6,000 × 6,000 px** instead of 10,000 × 10,000. A 10k × 10k image is 100 MP; decoded RGBA is roughly 400 MB, which can exhaust mobile browsers and low-memory devices even though the file is only 5 MB. 6,000 × 6,000 (36 MP, ~144 MB decoded worst case) is ample for a station map with labels at reasonable zoom and supports ~20 inches at 300 DPI. If the client needs a larger print master, 8,000 × 8,000 is the absolute ceiling; do not go back to 10,000. The 5 MB file-size cap remains independent.

**Residual risks:** all panel users can change the map (existing settings model); a public-disk file is accessible by URL (intended); no image-version history (old file is deleted on replace); no EXIF stripping (map images are non-sensitive by nature). These are accepted.

---

## 6. Public Page Design

In `resources/js/Pages/WaterQuality.jsx`:

- Read `settings?.water_quality_map_image_url` and `settings?.water_quality_map_image_credit`.
- Add a `StationMap` section **after the explanatory note and before the filters**.
- Render the section only when the path is non-empty. When absent, render **nothing** — no placeholder, no fallback image, no empty card.
- Use:

```jsx
<figure>
    <img
        src={`/storage/${settings.water_quality_map_image_url}`}
        alt="Peta titik sampling kualitas air Danau Matano"
        loading="lazy"
        className="w-full h-auto rounded-xl border border-gray-100"
    />
    {settings.water_quality_map_image_credit && (
        <figcaption className="mt-2 text-xs text-gray-500">
            {settings.water_quality_map_image_credit}
        </figcaption>
    )}
</figure>
```

- Card styling follows the page: white background, rounded-2xl, shadow-sm, border, `p-5 md:p-6`.
- Responsive: `w-full h-auto`, `object-contain`, no fixed height, no horizontal overflow at 375px.
- Accessibility: section heading (`h2`), `<figure>`/`<figcaption>`, meaningful alt text; optional "Buka gambar" link is allowed but not required.
- Do **not** import a map library, use `samplingPoints.coordinates`, or attempt to plot markers over the uploaded image.
- Keep the page's existing hardcoded Indonesian copy convention; localization of the whole page remains separate debt.
- Do not touch summary cards, six charts, filters, history table, pagination, or thresholds.

---

## 7. Files to Change (Phase 4B)

| File | Change |
| --- | --- |
| `database/migrations/2026_11_12_000000_add_water_quality_map_to_site_settings_table.php` | **New** migration: two nullable columns |
| `app/Models/SiteSettings.php` | Add `water_quality_map_image_url`, `water_quality_map_image_credit` to `$fillable` |
| `app/Filament/Pages/ManageSiteSettings.php` | Add map section + `private array $pendingMapUploads`; in `form()` set `$schema->record(SiteSettings::firstOrCreate([]))`; import `Storage`, `DB`, `Throwable`, `BaseFileUpload`, `TemporaryUploadedFile`; track new uploads and delete old files only after commit |
| `resources/js/Pages/WaterQuality.jsx` | Add conditional responsive map section |
| `tests/Feature/ManageSiteSettingsTest.php` | Upload/replace/remove/cancel/DB-failure/validation/other-settings tests |
| `tests/Feature/WaterQualityDashboardTest.php` | Public page map-prop and hidden-when-null tests |
| `tests/Unit/WaterQualityMapUiTest.php` (optional) | Source-level assertion that the map section is conditional and uses no coordinates/Leaflet |

**No change required:** `app/Http/Controllers/WebController.php` (already passes `settings`), `config/water_quality.php`, `water_sampling_points`/`water_quality_records` models, thresholds, chart code, filters, pagination. No new Filament resource/menu/table. No `package.json` change.

---

## 8. Test Cases

### Admin settings (`tests/Feature/ManageSiteSettingsTest.php`)
Use `Storage::fake('public')`, `Storage::fake('livewire-tmp')` if needed, and `UploadedFile::fake()->image(...)`.

1. Uploading a valid JPEG/PNG/WebP stores the relative path and credit; file exists on the fake public disk; no form errors.
2. Replacing an image stores the new path, keeps the new file, and deletes the old file after save.
3. Clearing the field on save sets the DB value to `null` and deletes the old file.
4. Cancel after uploading a new file (set the field, do not call `save()`): DB and old file are unchanged; no permanent file is created.
5. Cancel after removing the field (set it to `null`, do not call `save()`): DB and old file are unchanged.
6. DB failure after a new file is stored: throw from a `SiteSettings::updating` listener (or equivalent), call `save()`, expect the exception; assert the new file was deleted, the old file still exists, and the DB still points to the old path.
7. Uploading a PDF/SVG/GIF is rejected with a server-side form error; DB and storage are unchanged.
8. Uploading a file larger than 5 MB is rejected server-side.
9. The server-side dimension rule is configured; verify with a >6,000 px image in manual QA. Do not allocate huge image fixtures in PHP tests.
10. Saving the form with the existing stored path unchanged succeeds (record authorization for `preventFilePathTampering`).
11. A forged/tampered string path is rejected and cannot cause deletion of an arbitrary public-disk file.
12. Credit persists independently and can be cleared.
13. Saving the map does not alter existing About/contact/stat/legal settings.
14. Guests still cannot open the settings page (existing test remains).

For test 6, a throwing `updating` listener is sufficient because a single-row `update()` is wrapped in `DB::transaction`; this exercises the compensating cleanup without mocking the whole model. Assert no new file remains under `settings/water-quality` and the old file still exists.

### Public page (`tests/Feature/WaterQualityDashboardTest.php`)
15. When a map path and credit exist, `/kualitas-air` exposes them through the `settings` prop.
16. When no path exists, the prop is `null`; the UI must hide the entire section (source-level assertion or component test if a JS test harness is added later).
17. The page still exposes the six active sampling points and the existing record/filter/pagination behavior.
18. No coordinates are used by the map feature; no map dependency is imported.

**Manual QA (not automatable here):** verify preview, replace, remove, cancel in the browser; verify 375/768/1280 px rendering; verify a 6,001 px image is rejected and a representative 4,000 px map is accepted.

### Verification
```powershell
php artisan test --compact --filter=ManageSiteSettings
php artisan test --compact --filter=WaterQuality
vendor/bin/pint --dirty --format agent
npm run build
```

Baseline to preserve: **99 passed / 732 assertions** (Phase 3B-6), build green. After implementation, ask the user to run the full suite.

---

## 9. Risks, Constraints & Decisions

| # | Risk / decision | Mitigation / status |
| --- | --- | --- |
| 1 | All panel users can upload/replace the map | Existing architecture; documented; no new permission system in scope |
| 2 | Public disk exposes the file by URL | Intended; map is public content |
| 3 | Replaced file is permanently deleted | Accepted; no version history requirement; backup storage directory |
| 4 | Large/unoptimized map images | 5 MB cap + 6,000 × 6,000 max dimensions; recommend admin optimize before upload |
| 5 | Wrong or misleading map content | Admin responsibility; optional credit/source field |
| 6 | Storage symlink missing in a new environment | Document `php artisan storage:link` in deployment checklist |
| 7 | Migration rollback leaves files | Document manual cleanup; files are outside DB rollback |
| 8 | Browser caching after replacement | Filament assigns a new random filename, so the URL changes; no stale cache |
| 9 | Page copy remains Indonesian-only | Follows current page convention; localization is separate scope |
| 10 | Alt text is generic | Stable default; add an alt column only if requested |
| 11 | DB fails after a new file is stored | Tracked `pendingMapUploads` are deleted on failure; DB transaction rolls back to the old path |
| 12 | Admin cancels after upload/remove | No permanent storage and no deletion without `save()`; existing reference remains |
| 13 | Tampered path on a custom page | `preventFilePathTampering()` + `$this->form->record($settings)`; reject forged paths |
| 14 | Filament upgrade changes upload lifecycle | Plan is verified against v5.8.4; re-verify hooks on any future Filament upgrade |
| 15 | Other settings image fields may orphan on DB failure | Pre-existing behavior outside the map scope; do not change them in Phase 4B |
| 16 | Concurrent map saves | No settings lock; the losing request's newly stored file can orphan. Rare for a singleton settings page; accepted unless multi-admin concurrency becomes a requirement |

**No client decision blocks Phase 4B.** Optional inputs: a preferred credit/source line and a recommended map aspect ratio (landscape is assumed).

---

## 10. Acceptance Criteria & Stop Point

Phase 4B is accepted when:

- An admin can upload, preview, replace, and remove a JPEG/PNG/WebP map (max 5 MB, max 6,000 × 6,000 px) from **Pengaturan Situs**.
- The DB stores only the relative path + optional credit; the file lives under `storage/app/public/settings/water-quality`.
- Invalid types, oversized files, and oversized dimensions are rejected server-side; SVG/GIF are excluded.
- The form is wired to the settings record so `preventFilePathTampering()` authorizes unchanged paths and rejects forged ones.
- Replacing/removing deletes the previous file **only after** the DB transaction commits.
- A forced DB failure after upload leaves no orphan: the new file is deleted and the old file/DB reference remain.
- Cancelling after upload/remove saves nothing and deletes nothing.
- `/kualitas-air` displays the map responsively with alt text and optional credit, and hides the entire section when no image exists.
- Seeded coordinates remain unused; no Leaflet/OSM/Maps API/dependency is added.
- Sampling stations, 131 records, six parameters, charts, filters, thresholds, and pagination are unchanged.
- Focused tests pass, Pint is clean, `npm run build` succeeds; full suite remains green.
- Filament compatibility is confirmed against v5.8.4; no deployment is performed.

**Stop point:** revisions complete. No implementation until explicit approval.

**Git status (unchanged; only these reports were modified/added):**

```
 M app/Filament/Pages/ManageSiteSettings.php
 M app/Http/Controllers/WebController.php
 M app/Models/SiteSettings.php
 M lang/en/site.php
 M lang/id/site.php
 M resources/js/Components/Public/AboutSnippet.jsx
 M resources/js/Pages/About.jsx
 M resources/js/Pages/Home.jsx
 M tests/Unit/SiteSettingsTest.php
?? database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php
?? docs/reports/
?? "docs/revisi web.docx"
?? tests/Feature/AboutPageTest.php
?? tests/Feature/ManageSiteSettingsTest.php
?? tests/Feature/SiteSettingsAboutContentTest.php
?? tests/Unit/SiteLocalizationTest.php
```
