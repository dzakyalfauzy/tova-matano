# Phase 4B-4A — Null Settings Robustness Fix Report

**Date:** 2026-11-13
**Scope:** Water Quality map null-settings robustness
**Result:** **PASS**

---

## 1. Objective

Fix the null-settings robustness issue in `resources/js/Pages/WaterQuality.jsx` where map-related settings were accessed directly:

```jsx
settings.water_quality_map_image_url
```

If the `settings` prop is `null` — possible when no `site_settings` row exists — the component could throw a runtime error.

---

## 2. Root Cause

`WebController::settings()` returns `SiteSettings::first()?->makeHidden(['about_content'])`, so `settings` can be `null`.

The map block introduced in Phase 4B-3 accessed `settings.` directly in four places:

- map-section condition,
- image `src`,
- credit condition,
- credit text.

Other public pages already guard nullable settings with optional chaining. The map block did not.

---

## 3. Changes Made

### Frontend

**File:** `resources/js/Pages/WaterQuality.jsx`

Updated the map block to use optional chaining:

```jsx
{settings?.water_quality_map_image_url && (
    <figure className="mt-8 md:mt-10 text-center">
        <img
            src={`/storage/${settings?.water_quality_map_image_url}`}
            alt="Peta titik sampling kualitas air Danau Matano"
            className="max-w-full h-auto rounded-lg border border-gray-100 object-contain shadow-sm"
            loading="lazy"
        />
        {settings?.water_quality_map_image_credit && (
            <figcaption className="mt-3 text-xs text-gray-500">
                {settings?.water_quality_map_image_credit}
            </figcaption>
        )}
    </figure>
)}
```

The image URL is still constructed only inside the guarded map block, so it is only built when a truthy map path exists.

### Regression Test

**File:** `tests/Feature/WaterQualityMapTest.php`

Added a focused test:

```php
it('renders the page when the settings prop is null', function () {
    SiteSettings::query()->delete();

    $response = $this->get('/kualitas-air');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('settings', null)
    );

    $source = waterQualityPageSource();

    expect($source)->toContain('settings?.water_quality_map_image_url &&')
        ->and($source)->toContain('/storage/${settings?.water_quality_map_image_url}')
        ->and($source)->toContain('settings?.water_quality_map_image_credit &&');
});
```

Existing map source assertions were updated from `settings.` to `settings?.` to match the fixed component. Inertia property paths remain `settings.water_quality...`.

The test verifies:

- the public page returns successfully with no `site_settings` row,
- the `settings` Inertia prop is `null`,
- the component source guards map image and credit access with optional chaining.

**Test limitation:** this project has no JavaScript test runner or browser test plugin. The PHP feature test verifies the null prop and source-level guard; it does not execute React in a browser.

---

## 4. Behavior Preservation

| Behavior | Status |
|---|---|
| Map section hidden when `settings` is null | **PASS** |
| Map section hidden when image path is null or empty | **PASS** |
| Map image displayed when a path exists | **PASS** |
| Credit displayed only when populated | **PASS** |
| `/storage/{relative-path}` URL construction unchanged | **PASS** |
| No UI, layout, or styling redesign | **PASS** |

No other component logic was changed.

---

## 5. Verification

### Targeted map tests
```text
php artisan test tests/Feature/WaterQualityMapTest.php --compact
```

Result: **8 passed / 159 assertions**

### Relevant Water Quality suite
```text
php artisan test \
  tests/Feature/WaterQualityDashboardTest.php \
  tests/Feature/WaterQualityMapTest.php \
  tests/Feature/ManageSiteSettingsMapUploadTest.php \
  tests/Feature/SiteSettingsWaterQualityMapTest.php \
  --compact
```

Result: **48 passed / 473 assertions**

### Full suite
```text
php artisan test --compact
```

Result: **127 passed / 999 assertions**

Baseline before this fix: **126 tests / 984 assertions**.  
Change: **+1 test / +15 assertions**.

### Build
```text
npm run build
```

Result: **SUCCESS** — `WaterQuality-C9QaeGHj.js` generated; only pre-existing Vite plugin/deprecation warnings.

### Pint
```text
vendor/bin/pint --dirty --format agent
```

Result: **passed**

---

## 6. Files Changed

- `resources/js/Pages/WaterQuality.jsx` — optional chaining for map settings access.
- `tests/Feature/WaterQualityMapTest.php` — updated source assertions and added null-settings regression test.
- `docs/reports/PHASE_4B_4A_NULL_SETTINGS_FIX_REPORT.md` — this report.

No other application files were modified.

---

## 7. Scope Compliance

- No redesign or new feature.
- No migration or database change.
- No `.env` change.
- No production access.
- No deployment, commit, or push.
- No destructive Git operation.
- Existing uncommitted changes preserved.
- No browser automation dependency added.

---

## 8. Final Status

**PASS**

The null-settings map access is now guarded, existing map behavior is preserved, and the regression coverage confirms the public route can return the page with a null `settings` prop while the source retains optional chaining for map settings.

Awaiting user approval before any further phase.
